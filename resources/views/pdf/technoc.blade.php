<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>NOC Document</title>
    <style>
        body { font-family: Arial, sans-serif; }
        h1 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>No Objection Certificate</h1>
    <p>Employee Name: {{ $resignation->employee_name }}</p>
    <p>Employee ID: {{ $resignation->employee_id }}</p>
    <p>Department: {{ $resignation->department_name }}</p>
    <p>Date of Resignation: {{ $resignation->created_at->format('d/m/Y') }}</p>

    <h2>Assets</h2>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Serial Number</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @forelse($assets as $asset)
            <tr>
                <td>{{ $asset['name'] }}</td>
                <td>{{ $asset['serial'] }}</td>
                <td>{{ $asset['notes'] ?? 'N/A' }}</td>
            </tr>
            @empty
            <tr><td colspan="3">No assets assigned</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Accessories</h2>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Serial Number</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @forelse($accessories as $accessory)
            <tr>
                <td>{{ $accessory['name'] ?? 'N/A' }}</td>
                <td>{{ $accessory['serial'] ?? 'N/A' }}</td>
                <td>{{ $accessory['notes'] ?? 'N/A' }}</td>
            </tr>
            @empty
            <tr><td colspan="3">No accessories assigned</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Licenses</h2>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Product Key</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @forelse($licenses as $license)
            <tr>
                <td>{{ $license['name'] }}</td>
                <td>{{ $license['product_key'] }}</td>
                <td>{{ $license['notes'] ?? 'N/A' }}</td>
            </tr>
            @empty
            <tr><td colspan="3">No licenses assigned</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
