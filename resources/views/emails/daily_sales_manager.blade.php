<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager - Daily Sales Report</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
            background-color: #f4f7f6;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .email-container {
            max-width: 680px;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        h1 {
            color: #1abc9c;
            font-size: 24px;
            margin-top: 0;
        }
        h2 {
            color: #2c3e50;
            font-size: 20px;
            margin-top: 30px;
            margin-bottom: 10px;
            border-bottom: 1px solid #ecf0f1;
            padding-bottom: 5px;
        }
        p {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 12px 15px;
            border: 1px solid #e0e0e0;
            text-align: left;
        }
        th {
            background-color: #1abc9c;
            color: white;
            font-weight: 600;
        }
        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #7f8c8d;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <h1>Daily Sales Report for {{ $data['supermarket'] ?? 'Unknown Supermarket' }}</h1>
        <p><strong>Date:</strong> {{ $data['date'] ?? now()->toDateString() }}</p>
        <p><strong>Total Revenue:</strong> DZD{{ number_format($data['total_money'] ?? 0, 2) }}</p>
        <p><strong>Total Products Sold:</strong> {{ $data['total_products_sold'] ?? 0 }}</p>

        <h2>Products Sold Summary</h2>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Unit Price</th>
                    <th>Quantity Sold</th>
                    <th>Total Revenue</th>
                </tr>
            </thead>
            <tbody>
                @if (!empty($data['report']) && is_iterable($data['report']))
                    @forelse ($data['report'] as $item)
                        <tr>
                            <td>{{ $item->name ?? 'N/A' }}</td>
                            <td>DZD{{ number_format($item->price ?? 0, 2) }}</td>
                            <td>{{ $item->total_quantity ?? 0 }}</td>
                            <td>DZD{{ number_format($item->total_price ?? 0, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center;">No sales data available for this period.</td>
                        </tr>
                    @endforelse
                @else
                    <tr>
                        <td colspan="4" style="text-align: center;">Sales data is not available or in an incorrect format.</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Your Hypermarket. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
