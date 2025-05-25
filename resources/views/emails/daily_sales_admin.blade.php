<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Daily Sales Report</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
            background-color: #f4f7f6;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .email-container {
            max-width: 760px; /* Slightly wider for admin report */
            margin: 20px auto;
            padding: 25px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        h1 {
            color: #2c3e50; /* Admin primary color - dark blue/charcoal */
            font-size: 26px;
            margin-top: 0;
            text-align: center;
            margin-bottom: 25px;
        }
        h2 {
            color: #34495e; /* Slightly lighter than h1 */
            font-size: 22px;
            margin-top: 35px;
            margin-bottom: 15px;
            border-bottom: 2px solid #3498db; /* Admin accent color */
            padding-bottom: 8px;
        }
        h3 {
            color: #34495e;
            font-size: 18px;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        p {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        th, td {
            padding: 12px 15px;
            border: 1px solid #e0e0e0;
            text-align: left;
        }
        th {
            background-color: #34495e; /* Table header matching h2 color */
            color: white;
            font-weight: 600;
        }
        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .supermarket-card {
            background-color: #fdfdfd;
            border: 1px solid #ecf0f1;
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.03);
        }
        .summary-totals {
            margin-top:30px;
            padding-top:20px;
            border-top: 2px solid #3498db;
        }
        .summary-totals p {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 8px;
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
        <h1>Admin Daily Sales Report</h1>
        <p style="text-align:center; margin-top:-15px; margin-bottom:30px;"><strong>Date:</strong> {{ $data['date'] ?? now()->toDateString() }}</p>

        <div class="summary-totals">
            <h2>Overall Summary</h2>
            <p>Total Revenue (All Supermarkets): ${{ number_format($data['total_money'] ?? 0, 2) }}</p>
            <p>Total Products Sold (All Supermarkets): {{ $data['total_products_sold'] ?? 0 }}</p>
        </div>

        <h2>Global Product Sales Summary</h2>
        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Unit Price</th>
                    <th>Total Quantity Sold</th>
                    <th>Total Revenue Generated</th>
                </tr>
            </thead>
            <tbody>
                @if (!empty($data['all_products']) && is_iterable($data['all_products']))
                    @forelse ($data['all_products'] as $item)
                        <tr>
                            <td>{{ $item->name ?? 'N/A' }}</td>
                            <td>${{ number_format($item->price ?? 0, 2) }}</td>
                            <td>{{ $item->total_quantity ?? 0 }}</td>
                            <td>${{ number_format($item->total_price ?? 0, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center;">No global product sales data available.</td>
                        </tr>
                    @endforelse
                @else
                    <tr>
                        <td colspan="4" style="text-align: center;">Global product data is not available or in an incorrect format.</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <h2>Sales Breakdown by Supermarket</h2>
        @if (!empty($data['supermarkets_breakdown']) && is_iterable($data['supermarkets_breakdown']))
            @foreach ($data['supermarkets_breakdown'] as $supermarketData)
                <div class="supermarket-card">
                    <h3>{{ $supermarketData['supermarket'] ?? 'Unknown Supermarket' }}</h3>
                    <p><strong>Total Revenue:</strong> ${{ number_format($supermarketData['total_money'] ?? 0, 2) }}</p>
                    @if (!empty($supermarketData['report']) && is_iterable($supermarketData['report']))
                        <table>
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Unit Price</th>
                                    <th>Quantity Sold</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($supermarketData['report'] as $item)
                                    <tr>
                                        <td>{{ $item->name ?? 'N/A' }}</td>
                                        <td>${{ number_format($item->price ?? 0, 2) }}</td>
                                        <td>{{ $item->total_quantity ?? 0 }}</td>
                                        <td>${{ number_format($item->total_price ?? 0, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" style="text-align: center;">No sales data for this supermarket.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    @else
                        <p>No detailed sales report available for this supermarket.</p>
                    @endif
                </div>
            @endforeach
        @else
            <p>No supermarket breakdown data available.</p>
        @endif

        <div class="footer">
            <p>&copy; {{ date('Y') }} Your Hypermarket Administration. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
