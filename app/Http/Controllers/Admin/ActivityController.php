<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

/**
 * Dashboard → Activity: who changed what, newest first.
 */
class ActivityController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('can:activity.view')];
    }

    public function __invoke(Request $request): View
    {
        $userId = $request->integer('user') ?: null;
        $search = trim((string) $request->query('q', ''));

        $entries = ActivityLog::with('user')
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->when($search !== '', fn ($q) => $q->where('description', 'like', "%{$search}%"))
            ->latest()
            ->latest('id')
            ->paginate(30)
            ->withQueryString();

        return view('admin.activity.index', [
            'entries' => $entries,
            'users' => User::orderBy('name')->pluck('name', 'id'),
            'userId' => $userId,
            'search' => $search,
        ]);
    }
}
