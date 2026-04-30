@component('mail::message')
# Booking Extension Confirmation

Dear {{ $booking->user->name }},

Your cabin booking has been successfully extended. Here are the updated details:

@component('mail::panel')
**Booking Details**
- **Cabin:** {{ $booking->cabin->name }}
- **New End Time:** {{ $booking->end_time->format('F j, Y g:i A') }}
- **Location:** {{ $booking->cabin->location }}
@endcomponent

@component('mail::button', ['url' => route('bookings.details', $booking->id)])
View Booking Details
@endcomponent

If you have any questions or need further assistance, please don't hesitate to contact us.

Best regards,  
{{ config('app.name') }}
@endcomponent
