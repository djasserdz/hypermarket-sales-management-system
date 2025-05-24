<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Manager - Daily Sales Report</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f0f0f5; }
        h1 { color: #1abc9c; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 8px; border: 1px solid #ccc; }
        th { background-color: #1abc9c; color: white; }
    </style>
</head>
<body>
    <h1>Daily Sales Report for {{ $data['supermarket'] ?? 'Unknown Supermarket' }}</h1>
    <p><strong>Date:</strong> {{ $data['date'] ?? now()->toDateString() }}</p>
    <p><strong>Total Revenue:</strong> ${{ number_format($data['total_money'] ?? 0, 2) }}</p>

    <h2>Products Sold</h2>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Revenue</th>
            </tr>
        </thead>
        <tbody>
            @if (!empty($data['report']) && is_iterable($data['report']))
                @foreach ($data['report'] as $item)
                    <tr>
                        <td>{{ $item->name ?? 'N/A' }}</td>
                        <td>${{ number_format($item->price ?? 0, 2) }}</td>
                        <td>{{ $item->total_quantity ?? 0 }}</td>
                        <td>${{ number_format($item->total_price ?? 0, 2) }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="4">No sales data available.</td>
                </tr>
            @endif
        </tbody>
    </table>
</body>
</html>
