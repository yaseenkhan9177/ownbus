@component('mail::message')
# New Super Admin Registration Request

A new user has requested Super Admin access to the SaaS Platform.

**Details:**
- **Name:** {{ $name }}
- **Email:** {{ $email }}

This request requires approval from an active Super Admin. By approving this request, the user will be granted full administrative control over the entire system.

@component('mail::button', ['url' => $approvalUrl])
Review Request
@endcomponent

If you do not recognize this request, you may safely ignore it or reject it from the dashboard.

Thanks,<br>
{{ config('app.name') }} Security Guard
@endcomponent