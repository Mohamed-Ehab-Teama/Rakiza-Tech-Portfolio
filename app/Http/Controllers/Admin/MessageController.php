<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

/**
 * Dashboard → Messages: the contact-form inbox.
 */
class MessageController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:messages.view', only: ['index', 'show']),
            new Middleware('can:messages.update', only: ['toggleRead', 'markAllRead']),
            new Middleware('can:messages.delete', only: ['destroy']),
        ];
    }

    public function index(Request $request): View
    {
        $filter = $request->query('filter');
        $search = trim((string) $request->query('q', ''));

        $messages = ContactMessage::query()
            ->when($filter === 'unread', fn ($q) => $q->unread())
            ->when($filter === 'read', fn ($q) => $q->whereNotNull('read_at'))
            ->when($search !== '', function ($q) use ($search) {
                $q->where(fn ($inner) => $inner
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%"));
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.messages.index', [
            'messages' => $messages,
            'filter' => $filter,
            'search' => $search,
            'unread' => ContactMessage::unread()->count(),
        ]);
    }

    /** Opening a message marks it as read (if the user may change messages). */
    public function show(Request $request, ContactMessage $message): View
    {
        if ($request->user()->can('messages.update')) {
            $message->markAsRead();
        }

        return view('admin.messages.show', ['message' => $message]);
    }

    public function toggleRead(ContactMessage $message): RedirectResponse
    {
        $message->isRead() ? $message->markAsUnread() : $message->markAsRead();

        return back()->with('success', 'Message marked as '.($message->isRead() ? 'read' : 'unread').'.');
    }

    public function markAllRead(): RedirectResponse
    {
        $count = ContactMessage::unread()->update(['read_at' => now()]);

        return back()->with('success', $count ? "{$count} messages marked as read." : 'There were no unread messages.');
    }

    public function destroy(ContactMessage $message): RedirectResponse
    {
        ActivityLog::record('deleted', $message, description: 'Deleted the message from '.$message->name.' <'.$message->email.'>');
        $message->delete();

        return redirect()->route('admin.messages.index')->with('success', 'Message deleted.');
    }
}
