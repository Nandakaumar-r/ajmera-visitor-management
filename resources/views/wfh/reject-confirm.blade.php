<div style="max-width: 600px; margin: 40px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); font-family: Arial, sans-serif;">
    <div style="background-color: #dc3545; color: #fff; padding: 15px; border-radius: 4px 4px 0 0;">
        <h3 style="margin: 0;text-align: center;">Reject WFH Request</h3>
    </div>

    <div style="padding: 20px;">

        @if (session('success'))
            <div style="background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
                ✗ {{ session('success') }}
            </div>
        @endif

        @if (!session('success'))
            <p><strong>Employee:</strong> {{ $wfhRequest->employee->name }}</p>
            <p><strong>Duration:</strong> {{ $wfhRequest->start_date->format('d M Y') }} - {{ $wfhRequest->end_date->format('d M Y') }}</p>
            <p><strong>Reason:</strong> {{ $wfhRequest->reason }}</p>

            <form method="POST" action="{{ route('wfh.reject', $wfhRequest->id) }}">
                @csrf
                <div style="margin-bottom: 15px;">
                    <label for="comments" style="display: block; margin-bottom: 5px; font-weight: bold;">Manager Comments (optional):</label>
                    <textarea name="comments" id="comments" rows="3" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;"></textarea>
                </div>
                <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 10px;">
                    <button type="submit" style="background-color: #dc3545; color: #fff; border: none; padding: 12px 20px; border-radius: 4px; cursor: pointer; font-weight: bold;">✗ Yes, Reject</button>
                    <a href="{{ url()->previous() }}" style="display: inline-block; text-align: center; background-color: #6c757d; color: #fff; text-decoration: none; padding: 12px 20px; border-radius: 4px; font-weight: bold;">Cancel</a>
                </div>
            </form>
        @endif

    </div>
</div>
