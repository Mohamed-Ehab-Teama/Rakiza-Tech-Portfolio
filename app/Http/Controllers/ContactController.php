<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactMessageRequest;
use App\Mail\NewContactMessage;
use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

/**
 * Receives the website's contact form and saves it to Dashboard → Messages.
 * Works with JavaScript (contact.js sends JSON) and without it (normal form
 * post + redirect back). Spam protection: a hidden honeypot field and a rate
 * limit on the route (see routes/web.php).
 */
class ContactController extends Controller
{
    public function store(StoreContactMessageRequest $request): JsonResponse|RedirectResponse
    {
        $thanks = "Message sent. We'll get back to you within 24h.";

        // Pretend it worked so bots learn nothing, but save nothing.
        if ($request->isSpam()) {
            return $this->respond($request, $thanks);
        }

        $message = ContactMessage::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'message' => $request->validated('message'),
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 490, ''),
        ]);

        if (setting('notify_new_messages') && setting('contact_email')) {
            try {
                Mail::to(setting('contact_email'))->send(new NewContactMessage($message));
            } catch (Throwable $e) {
                // The message is already saved; a mail problem must not lose it.
                Log::warning('Contact notification email failed: '.$e->getMessage());
            }
        }

        return $this->respond($request, $thanks);
    }

    private function respond(StoreContactMessageRequest $request, string $status): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $status]);
        }

        return redirect()->to(route('home').'#contact')->with('contact_status', $status);
    }
}
