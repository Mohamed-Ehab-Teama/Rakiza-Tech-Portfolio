{{--
    The list page shared by every content type (projects, websites, services,
    skills, numbers). Columns come from the controller's columns(); buttons
    only appear when the user has the matching permission.
--}}
@extends('admin.layouts.app')

@php($route = 'admin.'.$labels['route'])
@section('title', $labels['plural'])

@section('content')
  <header class="page-head">
    <div>
      <h1 class="page-head__title">{{ $labels['plural'] }}</h1>
      @isset($labels['description'])
        <p class="page-head__text">{{ $labels['description'] }}</p>
      @endisset
    </div>
    @can($area.'.create')
      <a class="btn btn--primary" href="{{ route($route.'.create') }}">
        @include('admin.partials.icon', ['name' => 'plus']) New {{ Str::lower($labels['singular']) }}
      </a>
    @endcan
  </header>

  @if ($hasSearch || $publishable)
    <form class="toolbar" method="GET" role="search">
      @if ($hasSearch)
        <label class="toolbar__search">
          <span class="sr-only">Search {{ Str::lower($labels['plural']) }}</span>
          @include('admin.partials.icon', ['name' => 'search'])
          <input class="input" type="search" name="q" value="{{ $search }}" placeholder="Search {{ Str::lower($labels['plural']) }}…">
        </label>
      @endif
      @if ($publishable)
        <label>
          <span class="sr-only">Status</span>
          <select class="select" name="status">
            <option value="">All</option>
            <option value="published" @selected($status === 'published')>Published</option>
            <option value="draft" @selected($status === 'draft')>Drafts</option>
          </select>
        </label>
      @endif
      <button class="btn btn--ghost btn--sm" type="submit">Filter</button>
      @if ($search !== '' || $status)
        <a class="link-accent" href="{{ route($route.'.index') }}">Clear</a>
      @endif
    </form>
  @endif

  @if ($records->isEmpty())
    <div class="empty">
      <p>{{ $search !== '' || $status ? 'Nothing matches this filter.' : 'No '.Str::lower($labels['plural']).' yet.' }}</p>
      @if ($search === '' && ! $status)
        @can($area.'.create')
          <a class="btn btn--primary" href="{{ route($route.'.create') }}">Add the first one</a>
        @endcan
      @endif
    </div>
  @else
    @if ($canDrag)
      <p class="hint">Drag a row by its handle (or use the arrows) to change the order on the website.</p>
    @endif

    <div class="table-wrap">
      <table class="table" @if ($canDrag) data-reorder-url="{{ route($route.'.reorder') }}" @endif>
        <thead>
          <tr>
            @if ($canDrag)
              <th class="table__handle"><span class="sr-only">Order</span></th>
            @endif
            @foreach ($columns as $heading => $column)
              <th scope="col">{{ $heading }}</th>
            @endforeach
            @if ($publishable)
              <th scope="col">Status</th>
            @endif
            <th class="table__actions" scope="col"><span class="sr-only">Actions</span></th>
          </tr>
        </thead>
        <tbody>
          @foreach ($records as $record)
            {{-- A column is either an attribute name or a closure (instanceof, not is_callable: "date" would count as a function). --}}
            @php($cells = collect($columns)->map(fn ($column) => $column instanceof \Closure ? $column($record) : data_get($record, $column)))
            @php($name = (string) $cells->first())
            <tr data-id="{{ $record->getKey() }}">
              @if ($canDrag)
                <td class="table__handle">
                  <span class="drag-handle" draggable="true" title="Drag to reorder" aria-hidden="true">
                    @include('admin.partials.icon', ['name' => 'grip'])
                  </span>
                  <span class="move-btns">
                    <button type="button" class="move-btn" data-move="up" aria-label="Move {{ $name }} up">@include('admin.partials.icon', ['name' => 'arrow-up'])</button>
                    <button type="button" class="move-btn" data-move="down" aria-label="Move {{ $name }} down">@include('admin.partials.icon', ['name' => 'arrow-down'])</button>
                  </span>
                </td>
              @endif

              @foreach ($cells as $heading => $value)
                @php($text = ($value === null || $value === '') ? '—' : $value)
                <td data-label="{{ $heading }}" @class(['table__primary' => $loop->first])>
                  @if ($loop->first && auth()->user()->can($area.'.update'))
                    <a href="{{ route($route.'.edit', $record->getKey()) }}">{{ $text }}</a>
                  @else
                    {{ $text }}
                  @endif
                </td>
              @endforeach

              @if ($publishable)
                <td data-label="Status">
                  @can($area.'.publish')
                    <form method="POST" action="{{ route($route.'.publish', $record->getKey()) }}">
                      @csrf
                      @method('PATCH')
                      <button type="submit" class="badge badge--button {{ $record->is_published ? 'badge--live' : 'badge--draft' }}"
                              title="Click to {{ $record->is_published ? 'unpublish' : 'publish' }}">
                        {{ $record->is_published ? 'Published' : 'Draft' }}
                      </button>
                    </form>
                  @else
                    <span class="badge {{ $record->is_published ? 'badge--live' : 'badge--draft' }}">{{ $record->is_published ? 'Published' : 'Draft' }}</span>
                  @endcan
                </td>
              @endif

              <td class="table__actions">
                @if ($url = $publicUrl($record))
                  <a class="icon-btn" href="{{ $url }}" target="_blank" rel="noopener" title="View on the website" aria-label="View {{ $name }} on the website">@include('admin.partials.icon', ['name' => 'eye'])</a>
                @endif
                @can($area.'.update')
                  <a class="icon-btn" href="{{ route($route.'.edit', $record->getKey()) }}" title="Edit" aria-label="Edit {{ $name }}">@include('admin.partials.icon', ['name' => 'edit'])</a>
                @endcan
                @can($area.'.delete')
                  <form method="POST" action="{{ route($route.'.destroy', $record->getKey()) }}"
                        data-confirm="Delete {{ Str::lower($labels['singular']) }} &quot;{{ $name }}&quot;? This cannot be undone.">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="icon-btn icon-btn--danger" title="Delete" aria-label="Delete {{ $name }}">@include('admin.partials.icon', ['name' => 'trash'])</button>
                  </form>
                @endcan
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    @if ($records instanceof \Illuminate\Contracts\Pagination\Paginator)
      {{ $records->links() }}
    @endif
  @endif
@endsection
