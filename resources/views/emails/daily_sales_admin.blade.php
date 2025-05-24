<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin - Daily Sales Report</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #fdfdfd; }
        h1 { color: #2c3e50; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th, td { padding: 10px; border: 1px solid #ddd; }
        th { background-color: #2c3e50; color: #fff; }
        .section-title { color: #2980b9; margin-top: 40px; }
    </style>
</head>
<body>
    <h1>Admin Daily Sales Report - {{ $data['date'] ?? now()->toDateString() }}</h1>

    <h2 class="section-title">Global Product Summary</h2>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity Sold</th>
                <th>Total Revenue</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data['all_products'] as $item)
                <tr>
                    <td>{{ $item->name }}</td>
                    <td>${{ number_format($item->price, 2) }}</td>
                    <td>{{ $item->total_quantity }}</td>
                    <td>${{ number_format($item->total_price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2 class="section-title">Breakdown by Supermarket</h2>
    @foreach ($data['supermarkets_breakdown'] as $report)
        <h3>{{ $report['supermarket'] }}</h3>
        <p><strong>Total Revenue:</strong> ${{ number_format($report['total_money'], 2) }}</p>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity Sold</th>
                    <th>Total Revenue</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($report['report'] as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td>${{ number_format($item->price, 2) }}</td>
                        <td>{{ $item->total_quantity }}</td>
                        <td>${{ number_format($item->total_price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach
</body>
</html>
