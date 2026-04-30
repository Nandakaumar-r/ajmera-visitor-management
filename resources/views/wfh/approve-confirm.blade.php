<div style="max-width: 600px; margin: 40px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); font-family: Arial, sans-serif;">
    <div style="background-color: #28a745; color: #fff; padding: 15px; border-radius: 4px 4px 0 0;">
        <h3 style="margin: 0;text-align: center;">Approve WFH Request</h3>
    </div>

    <div style="padding: 20px;">

        @if (session('success'))
            <div style="background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
                ✅ {{ session('success') }}
            </div>
        @endif

        @if (!session('success'))
            <p><strong>Employee:</strong> {{ $wfhRequest->employee->name }}</p>
            <p><strong>Duration:</strong> {{ $wfhRequest->start_date->format('d M Y') }} - {{ $wfhRequest->end_date->format('d M Y') }}</p>
            <p><strong>Reason:</strong> {{ $wfhRequest->reason }}</p>

            <form method="POST" action="{{ route('wfh.approve', $wfhRequest->id) }}">
                @csrf
                <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 10px;">
                    <button type="submit" style="background-color: #28a745; color: #fff; border: none; padding: 12px 20px; border-radius: 4px; cursor: pointer; font-weight: bold;">✅ Yes, Approve</button>
                    <a href="{{ url()->previous() }}" style="display: inline-block; text-align: center; background-color: #6c757d; color: #fff; text-decoration: none; padding: 12px 20px; border-radius: 4px; font-weight: bold;">Cancel</a>
                </div>
            </form>
        @endif

    </div>
</div>
