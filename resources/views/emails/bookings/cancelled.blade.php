@component('mail::message')
# Booking Cancellation Confirmation

Dear {{ $booking->user->name }},

Your cabin booking has been successfully cancelled. Here are the details:

@component('mail::panel')
**Cancellation Details**
- **Cabin:** {{ $booking->cabin->name }}
- **Original Booking Period:** {{ $booking->start_time->format('F j, Y g:i A') }} to {{ $booking->end_time->format('F j, Y g:i A') }}
- **Cancellation Time:** {{ $booking->cancelled_at->format('F j, Y g:i A') }}
@endcomponent

If you have any questions or need further assistance, please don't hesitate to contact us.

Best regards,  
{{ config('app.name') }}
@endcomponent
