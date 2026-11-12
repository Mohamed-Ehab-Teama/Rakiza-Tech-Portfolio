<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Base controller for the dashboard's content types (projects, websites,
 * services, skills, numbers). A child controller only DESCRIBES its content:
 *
 *   $area      permission prefix, e.g. "projects" → projects.view/create/update/delete/publish
 *   model()    the Eloquent model class
 *   labels()   names shown in the dashboard + the route name prefix
 *   columns()  what the list shows
 *   fields()   the form: each field's label, type, validation rules and help text
 *
 * This class then provides the list, create/edit forms, saving (with image
 * uploads), deleting, the publish switch and drag & drop ordering, and checks
 * the matching permission for every action.
 *
 * Field types: text, textarea, markdown, number, url, email, date, select
 * ("options" => [value => label]), boolean, tags (comma separated → array),
 * image (upload; "directory" => folder in storage/app/public).
 * A field with 'publish' => true is only shown to users who may publish.
 */
abstract class ResourceController extends Controller implements HasMiddleware
{
    /** Permission prefix, e.g. "projects". */
    protected static string $area;

    /** @return class-string<Model> */
    abstract protected function model(): string;

    /**
     * @return array{singular: string, plural: string, route: string, description?: string}
     */
    abstract protected function labels(): array;

    /**
     * List columns: [heading => attribute name | fn (Model $record): string].
     */
    abstract protected function columns(): array;

    /**
     * Form fields: [attribute => [label, type, rules, help, options, width, …]].
     */
    abstract protected function fields(?Model $record): array;

    /** Attributes searched by the list's search box. */
    protected function searchable(): array
    {
        return [];
    }

    /** The list's base query (child classes may add eager loads or ordering). */
    protected function listQuery(): Builder
    {
        return $this->model()::query()->ordered();
    }

    /** Last chance to adjust the data before it is saved. */
    protected function beforeSave(array $data, ?Model $record): array
    {
        return $data;
    }

    /** Public URL of a record, to show a "View on site" link (or null). */
    protected function publicUrl(Model $record): ?string
    {
        return null;
    }

    /** Does the model have the is_published / sort_order columns? */
    protected function publishable(): bool
    {
        return true;
    }

    protected function sortable(): bool
    {
        return true;
    }

    /**
     * Permission checks for every action (Laravel's "can" middleware).
     */
    public static function middleware(): array
    {
        $area = static::$area;

        return [
            new Middleware("can:{$area}.view", only: ['index']),
            new Middleware("can:{$area}.create", only: ['create', 'store']),
            new Middleware("can:{$area}.update", only: ['edit', 'update', 'reorder']),
            new Middleware("can:{$area}.delete", only: ['destroy']),
            new Middleware("can:{$area}.publish", only: ['publish']),
        ];
    }

    // ------------------------------------------------------------------ list

    public function index(Request $request): View
    {
        $query = $this->listQuery();

        $search = trim((string) $request->query('q', ''));
        if ($search !== '' && $this->searchable()) {
            $query->where(function (Builder $q) use ($search) {
                foreach ($this->searchable() as $column) {
                    $q->orWhere($column, 'like', '%'.$search.'%');
                }
            });
        }

        $status = $request->query('status');
        if ($this->publishable() && in_array($status, ['published', 'draft'], true)) {
            $query->where('is_published', $status === 'published');
        }

        // Drag & drop needs the whole, unfiltered list on one page.
        $canDrag = $this->sortable() && $search === '' && ! $status && Gate::allows(static::$area.'.update');

        return view('admin.resources.index', [
            'records' => $canDrag ? $query->get() : $query->paginate(25)->withQueryString(),
            'columns' => $this->columns(),
            'labels' => $this->labels(),
            'area' => static::$area,
            'search' => $search,
            'status' => $status,
            'canDrag' => $canDrag,
            'hasSearch' => $this->searchable() !== [],
            'publishable' => $this->publishable(),
            'publicUrl' => fn (Model $record) => $this->publicUrl($record),
        ]);
    }

    // ------------------------------------------------------------ create/edit

    public function create(): View|RedirectResponse
    {
        return $this->form(null);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request, null);

        // Without the publish permission new records start as drafts.
        if ($this->publishable() && ! Gate::allows(static::$area.'.publish')) {
            $data['is_published'] = false;
        }

        $record = $this->model()::create($data);

        return redirect()
            ->route('admin.'.$this->labels()['route'].'.index')
            ->with('success', $this->labels()['singular'].' "'.$this->nameOf($record).'" was created.');
    }

    public function edit(int $id): View
    {
        return $this->form($this->find($id));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $record = $this->find($id);
        $record->update($this->validatedData($request, $record));

        return redirect()
            ->route('admin.'.$this->labels()['route'].'.index')
            ->with('success', $this->labels()['singular'].' "'.$this->nameOf($record).'" was saved.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $record = $this->find($id);
        $name = $this->nameOf($record);

        $record->delete();
        $this->deleteImages($record);

        return redirect()
            ->route('admin.'.$this->labels()['route'].'.index')
            ->with('success', $this->labels()['singular'].' "'.$name.'" was deleted.');
    }

    // ------------------------------------------------- publish switch / order

    public function publish(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $record = $this->find($id);
        $record->update(['is_published' => ! $record->is_published]);

        $message = $this->labels()['singular'].' "'.$this->nameOf($record).'" is now '
            .($record->is_published ? 'published' : 'a draft').'.';

        return $request->expectsJson()
            ? response()->json(['published' => $record->is_published, 'message' => $message])
            : back()->with('success', $message);
    }

    /** Saves a new order sent by the list's drag & drop: { ids: [3, 1, 2] }. */
    public function reorder(Request $request): JsonResponse
    {
        $model = $this->model();
        $table = (new $model)->getTable();

        $ids = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'distinct', "exists:{$table},id"],
        ])['ids'];

        DB::transaction(function () use ($ids, $model) {
            foreach ($ids as $position => $id) {
                // A plain query (no model events) so this is one log entry, not one per row.
                $model::whereKey($id)->update(['sort_order' => $position + 1]);
            }
        });

        ActivityLog::record('reordered', description: 'Reordered '.Str::lower($this->labels()['plural']));

        return response()->json(['message' => 'New order saved.']);
    }

    // --------------------------------------------------------------- helpers

    protected function find(int $id): Model
    {
        return $this->model()::findOrFail($id);
    }

    protected function nameOf(Model $record): string
    {
        return Str::limit(method_exists($record, 'activityLabel') ? $record->activityLabel() : '#'.$record->getKey(), 60);
    }

    protected function form(?Model $record): View
    {
        return view('admin.resources.form', [
            'record' => $record,
            'fields' => $this->visibleFields($record),
            'labels' => $this->labels(),
            'area' => static::$area,
            'publicUrl' => $record ? $this->publicUrl($record) : null,
        ]);
    }

    /** The form fields this user may fill in (publish fields need the publish permission). */
    protected function visibleFields(?Model $record): array
    {
        $canPublish = Gate::allows(static::$area.'.publish');

        return array_filter(
            $this->fields($record),
            fn (array $field) => ! ($field['publish'] ?? false) || $canPublish
        );
    }

    /** Validate the request against the field rules and turn it into model data. */
    protected function validatedData(Request $request, ?Model $record): array
    {
        $fields = $this->visibleFields($record);

        $rules = [];
        $attributes = [];
        foreach ($fields as $name => $field) {
            $attributes[$name] = Str::lower($field['label']);
            $rules[$name] = match ($field['type']) {
                'boolean' => ['nullable', 'boolean'],
                default => $field['rules'] ?? ['nullable'],
            };
            if ($field['type'] === 'image') {
                $rules["remove_{$name}"] = ['nullable', 'boolean'];
            }
        }

        $validated = $request->validate($rules, [], $attributes);

        $data = [];
        foreach ($fields as $name => $field) {
            $data[$name] = match ($field['type']) {
                'boolean' => $request->boolean($name),
                'tags' => $this->splitTags($validated[$name] ?? ''),
                'image' => $this->storeImage($request, $name, $field, $record),
                'number' => ($validated[$name] ?? '') === '' ? null : $validated[$name] + 0,
                default => ($validated[$name] ?? '') === '' ? null : $validated[$name],
            };
        }

        return $this->beforeSave($data, $record);
    }

    /** "Laravel, MySQL ,  Blade" → ["Laravel", "MySQL", "Blade"] */
    protected function splitTags(?string $value): array
    {
        $tags = array_map('trim', explode(',', (string) $value));

        return array_values(array_unique(array_filter($tags, fn ($tag) => $tag !== '')));
    }

    /** Store a newly uploaded image (replacing the old file) or remove it. */
    protected function storeImage(Request $request, string $name, array $field, ?Model $record): ?string
    {
        $current = $record?->{$name};

        if ($request->hasFile($name)) {
            if ($current) {
                Storage::disk('public')->delete($current);
            }

            return $request->file($name)->store($field['directory'] ?? 'uploads', 'public');
        }

        if ($request->boolean("remove_{$name}") && $current) {
            Storage::disk('public')->delete($current);

            return null;
        }

        return $current;
    }

    protected function deleteImages(Model $record): void
    {
        foreach ($this->fields($record) as $name => $field) {
            if ($field['type'] === 'image' && $record->{$name}) {
                Storage::disk('public')->delete($record->{$name});
            }
        }
    }
}
