{{-- Plain-text email: {!! !!} prints the text as-is (no HTML escaping needed in text mail). --}}
New message from the {!! setting('site_name') !!} website

From:    {!! $contactMessage->name !!} <{!! $contactMessage->email !!}>
Sent:    {{ $contactMessage->created_at->format('Y-m-d H:i') }}

{!! $contactMessage->message !!}

—
Reply to this email to answer {!! $contactMessage->name !!} directly.
All messages: {{ route('admin.messages.index') }}
