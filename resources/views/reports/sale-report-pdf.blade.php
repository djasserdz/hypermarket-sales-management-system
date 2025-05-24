<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #007bff;
            margin: 0;
            font-size: 28px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .report-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 30px;
        }
        .report-info h2 {
            margin-top: 0;
            color: #007bff;
            font-size: 18px;
        }
        .info-row {
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: bold;
            color: #495057;
            display: inline-block;
            width: 120px;
        }
        .summary-section {
            margin-bottom: 30px;
        }
        .summary-section h2 {
            color: #007bff;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 5px;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        .summary-card {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            border-left: 4px solid #007bff;
        }
        .summary-card h3 {
            margin: 0 0 10px 0;
            color: #495057;
            font-size: 14px;
            text-transform: uppercase;
        }
        .summary-card .value {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin: 0;
        }
        .table-section {
            margin-bottom: 30px;
        }
        .table-section h2 {
            color: #007bff;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 12px;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            color: #666;
            font-size: 12px;
            border-top: 1px solid #dee2e6;
            padding-top: 20px;
        }
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }
        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }
        .badge-info {
            background-color: #d1ecf1;
            color: #0c5460;
        }
    </style>
</head>
<body>
    <?php
        // Safe date formatting helper
        $currentDateTime = date('Y-m-d H:i:s');
        $safeReportDate = isset($reportDate) ? $reportDate : 'Unknown';
        $safeCreatedAt = 'Unknown';
        
        if (isset($report->created_at)) {
            if (is_string($report->created_at)) {
                $safeCreatedAt = $report->created_at;
            } elseif (is_object($report->created_at) && method_exists($report->created_at, 'format')) {
                $safeCreatedAt = $report->created_at->format('Y-m-d H:i:s');
            }
        }
    ?>

    <div class="header">
        <h1>Sales Report</h1>
        <p>{{ $reportType ?? 'Sales Report' }}</p>
        <p>Generated on {{ $currentDateTime }}</p>
    </div>

    <div class="report-info">
        <h2>Report Information</h2>
        <div class="info-row">
            <span class="info-label">Report Date:</span>
            <span>{{ $safeReportDate }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Report Type:</span>
            <span class="badge {{ isset($reportType) && str_contains($reportType, 'General') ? 'badge-success' : 'badge-info' }}">
                {{ $reportType ?? 'Unknown' }}
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">Generated At:</span>
            <span>{{ $safeCreatedAt }}</span>
        </div>
    </div>

    @if(isset($content) && is_array($content) && (isset($content['total_money']) || isset($content['total_quantity']) || isset($content['total_orders'])))
    <div class="summary-section">
        <h2>Summary</h2>
        <div class="summary-grid">
            @if(isset($content['total_money']))
            <div class="summary-card">
                <h3>Total Revenue</h3>
                <p class="value">${{ number_format((float)$content['total_money'], 2) }}</p>
            </div>
            @endif
            
            @if(isset($content['total_quantity']))
            <div class="summary-card">
                <h3>Total Quantity</h3>
                <p class="value">{{ number_format((int)$content['total_quantity']) }}</p>
            </div>
            @endif
            
            @if(isset($content['total_orders']))
            <div class="summary-card">
                <h3>Total Orders</h3>
                <p class="value">{{ number_format((int)$content['total_orders']) }}</p>
            </div>
            @endif
            
            @if(isset($content['total_quantity']) && isset($content['total_orders']) && (int)$content['total_orders'] > 0)
            <div class="summary-card">
                <h3>Avg Items/Order</h3>
                <p class="value">{{ number_format((float)$content['total_quantity'] / (float)$content['total_orders'], 2) }}</p>
            </div>
            @endif
        </div>
    </div>
    @endif

    @if(isset($content['products']) && is_array($content['products']) && count($content['products']) > 0)
    <div class="table-section">
        <h2>Product Sales Details</h2>
        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th class="text-right">Quantity Sold</th>
                    <th class="text-right">Unit Price</th>
                    <th class="text-right">Total Revenue</th>
                </tr>
            </thead>
            <tbody>
                @foreach($content['products'] as $product)
                <tr>
                    <td>{{ isset($product['name']) ? $product['name'] : 'N/A' }}</td>
                    <td class="text-right">{{ isset($product['quantity']) ? number_format((int)$product['quantity']) : 'N/A' }}</td>
                    <td class="text-right">
                        @if(isset($product['price']))
                            ${{ number_format((float)$product['price'], 2) }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td class="text-right">
                        @if(isset($product['total_revenue']))
                            ${{ number_format((float)$product['total_revenue'], 2) }}
                        @elseif(isset($product['quantity']) && isset($product['price']))
                            ${{ number_format((float)$product['quantity'] * (float)$product['price'], 2) }}
                        @else
                            N/A
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if(isset($content['supermarkets']) && is_array($content['supermarkets']) && count($content['supermarkets']) > 0)
    <div class="table-section">
        <h2>Supermarket Performance</h2>
        <table>
            <thead>
                <tr>
                    <th>Supermarket</th>
                    <th class="text-right">Total Revenue</th>
                    <th class="text-right">Total Orders</th>
                    <th class="text-right">Items Sold</th>
                </tr>
            </thead>
            <tbody>
                @foreach($content['supermarkets'] as $market)
                <tr>
                    <td>{{ isset($market['name']) ? $market['name'] : 'N/A' }}</td>
                    <td class="text-right">
                        @if(isset($market['total_revenue']))
                            ${{ number_format((float)$market['total_revenue'], 2) }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td class="text-right">{{ isset($market['total_orders']) ? number_format((int)$market['total_orders']) : 'N/A' }}</td>
                    <td class="text-right">{{ isset($market['total_items']) ? number_format((int)$market['total_items']) : 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if(isset($content['daily_sales']) && is_array($content['daily_sales']) && count($content['daily_sales']) > 0)
    <div class="table-section">
        <h2>Daily Sales Breakdown</h2>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th class="text-right">Revenue</th>
                    <th class="text-right">Orders</th>
                    <th class="text-right">Items Sold</th>
                </tr>
            </thead>
            <tbody>
                @foreach($content['daily_sales'] as $daily)
                <tr>
                    <td>{{ isset($daily['date']) ? $daily['date'] : 'N/A' }}</td>
                    <td class="text-right">
                        @if(isset($daily['revenue']))
                            ${{ number_format((float)$daily['revenue'], 2) }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td class="text-right">{{ isset($daily['orders']) ? number_format((int)$daily['orders']) : 'N/A' }}</td>
                    <td class="text-right">{{ isset($daily['items']) ? number_format((int)$daily['items']) : 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        <p>This report was automatically generated from the sales data.</p>
        <p>Report ID: {{ isset($report->id) ? $report->id : 'Unknown' }} | Generated: {{ $currentDateTime }}</p>
    </div>
</body>
</html>