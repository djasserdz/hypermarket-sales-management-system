<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Report</title>
    <style>
        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif; /* Added DejaVu Sans for better UTF-8 support */
            margin: 0;
            padding: 20px;
            color: #333;
            font-size: 12px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #007bff;
            padding-bottom: 15px; /* Adjusted */
            margin-bottom: 25px; /* Adjusted */
        }
        .header h1 {
            color: #007bff;
            margin: 0;
            font-size: 24px; /* Adjusted */
        }
        .header p {
            margin: 5px 0;
            color: #666;
            font-size: 12px; /* Adjusted */
        }
        .report-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 25px; /* Adjusted */
            border: 1px solid #e3e3e3; /* Added border */
        }
        .report-info h2 {
            margin-top: 0;
            margin-bottom: 10px; /* Added */
            color: #007bff;
            font-size: 16px; /* Adjusted */
        }
        .info-row {
            margin-bottom: 8px; /* Adjusted */
            font-size: 12px; /* Adjusted */
        }
        .info-label {
            font-weight: bold;
            color: #495057;
            display: inline-block;
            width: 130px; /* Adjusted */
        }
        .summary-section {
            margin-bottom: 25px; /* Adjusted */
        }
        .summary-section h2 {
            color: #007bff;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 8px; /* Adjusted */
            font-size: 18px; /* Added */
            margin-bottom: 15px; /* Added */
        }
        /* Summary cards - using table for better PDF rendering compatibility */
        .summary-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .summary-table td {
            border: 1px solid #e3e3e3;
            padding: 10px;
            text-align: center;
            width: 33.33%; /* For 3 cards per row */
        }
        .summary-table h3 {
            margin: 0 0 8px 0;
            color: #495057;
            font-size: 13px; /* Adjusted */
            text-transform: uppercase;
        }
        .summary-table .value {
            font-size: 20px; /* Adjusted */
            font-weight: bold;
            color: #007bff;
            margin: 0;
        }

        .table-section {
            margin-bottom: 25px; /* Adjusted */
        }
        .table-section h2 {
            color: #007bff;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 8px; /* Adjusted */
            font-size: 18px; /* Added */
            margin-bottom: 15px; /* Added */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px; /* Adjusted */
            font-size: 11px; /* Adjusted for more data */
        }
        th, td {
            padding: 6px 8px; /* Adjusted */
            text-align: left;
            border: 1px solid #dee2e6; /* Added full borders */
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
        .text-center {
            text-align: center;
        }
        .footer {
            margin-top: 40px; /* Adjusted */
            text-align: center;
            color: #666;
            font-size: 10px; /* Adjusted */
            border-top: 1px solid #dee2e6;
            padding-top: 15px; /* Adjusted */
        }
        .badge {
            padding: 3px 6px; /* Adjusted */
            border-radius: 4px;
            font-size: 10px; /* Adjusted */
            font-weight: bold;
            color: white; /* General color */
        }
        .badge-success {
            background-color: #28a745; /* Darker green */
        }
        .badge-info {
            background-color: #17a2b8; /* Darker info blue */
        }
        .no-data {
            text-align: center;
            padding: 20px;
            color: #6c757d;
            font-style: italic;
        }
        .supermarket-breakdown {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
            border: 1px solid #e3e3e3;
        }
        .supermarket-breakdown h3 {
            color: #0056b3; /* Darker blue for supermarket name */
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 16px;
        }
    </style>
</head>
<body>
    @php
        // Determine report structure based on $reportType and $content structure
        $isGeneralAdminReport = ($reportType === 'General (Admin)' && isset($content['supermarkets_breakdown']));
        $isSupermarketReport = (!$isGeneralAdminReport && isset($content['supermarket']));

        // Safe date formatting
        $currentDateTime = date('Y-m-d H:i:s');
        $safeReportDate = $content['date'] ?? 'Unknown'; // Get date from content
        
        // Extract main data points
        $reportTitle = $isGeneralAdminReport ? 'General Admin Sales Report' : ($isSupermarketReport ? 'Supermarket Sales Report: ' . $content['supermarket'] : 'Sales Report');
        $totalRevenue = $content['total_money'] ?? 0;
        $totalProductsSold = $content['total_products_sold'] ?? ($content['total_quantity'] ?? 0); // Adjusted to check both keys

        $mainProductList = [];
        if ($isGeneralAdminReport && isset($content['all_products'])) {
            $mainProductList = $content['all_products'];
        } elseif ($isSupermarketReport && isset($content['report'])) {
            $mainProductList = $content['report'];
        }

        $supermarketBreakdownList = $isGeneralAdminReport && isset($content['supermarkets_breakdown']) ? $content['supermarkets_breakdown'] : [];
        $activeSupermarketsCount = count($supermarketBreakdownList);
    @endphp

    <div class="header">
        <h1>{{ $reportTitle }}</h1>
        <p>Date: {{ $safeReportDate }}</p>
        <p>Generated on: {{ $currentDateTime }}</p>
    </div>

    <div class="report-info">
        <h2>Report Information</h2>
        <div class="info-row">
            <span class="info-label">Report Date:</span>
            <span>{{ $safeReportDate }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Report Type:</span>
            <span class="badge {{ $isGeneralAdminReport ? 'badge-success' : 'badge-info' }}">
                {{ $reportType ?? 'Unknown' }}
            </span>
        </div>
        @if($isSupermarketReport && isset($content['supermarket']))
        <div class="info-row">
            <span class="info-label">Supermarket:</span>
            <span>{{ $content['supermarket'] }}</span>
        </div>
        @endif
    </div>

    <div class="summary-section">
        <h2>Summary</h2>
        <table class="summary-table">
            <tr>
                <td>
                    <h3>Total Revenue</h3>
                    <p class="value">DZD{{ number_format((float)$totalRevenue, 2) }}</p>
                </td>
                <td>
                    <h3>Total Products Sold</h3>
                    <p class="value">{{ number_format((int)$totalProductsSold) }}</p>
                </td>
                @if($isGeneralAdminReport)
                <td>
                    <h3>Active Supermarkets</h3>
                    <p class="value">{{ $activeSupermarketsCount }}</p>
                </td>
                @else
                 <td>
                    <h3>Products Types Sold</h3>
                    <p class="value">{{ count($mainProductList) }}</p>
                </td>
                @endif
            </tr>
        </table>
    </div>

    @if(count($mainProductList) > 0)
    <div class="table-section">
        <h2>{{ $isGeneralAdminReport ? 'All Products Sold (Combined)' : 'Sales Details' }}</h2>
        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    @if($isSupermarketReport || $isGeneralAdminReport) {{-- ID is usually in individual product lists --}}
                        <th>ID</th>
                    @endif
                    <th class="text-right">Unit Price</th>
                    <th class="text-right">Quantity Sold</th>
                    <th class="text-right">Total Revenue</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mainProductList as $item)
                <tr>
                    <td>{{ $item['name'] ?? 'N/A' }}</td>
                    @if($isSupermarketReport || $isGeneralAdminReport)
                         <td>{{ $item['id'] ?? 'N/A' }}</td>
                    @endif
                    <td class="text-right">DZD{{ number_format((float)($item['price'] ?? 0), 2) }}</td>
                    <td class="text-right">{{ number_format((int)($item['total_quantity'] ?? ($item['quantity'] ?? 0) )) }}</td>
                    <td class="text-right">DZD{{ number_format((float)($item['total_price'] ?? 0), 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="table-section">
        <h2>{{ $isGeneralAdminReport ? 'All Products Sold (Combined)' : 'Sales Details' }}</h2>
        <p class="no-data">No product sales data available for this report.</p>
    </div>
    @endif

    @if($isGeneralAdminReport && count($supermarketBreakdownList) > 0)
    <div class="table-section">
        <h2>Breakdown by Supermarket</h2>
        @foreach($supermarketBreakdownList as $supermarketData)
            <div class="supermarket-breakdown">
                <h3>
                    {{ $supermarketData['supermarket'] ?? 'Unknown Supermarket' }} - 
                    Total: DZD{{ number_format((float)($supermarketData['total_money'] ?? 0), 2) }}
                </h3>
                @if(isset($supermarketData['report']) && count($supermarketData['report']) > 0)
                <table>
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>ID</th>
                            <th class="text-right">Unit Price</th>
                            <th class="text-right">Quantity Sold</th>
                            <th class="text-right">Total Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($supermarketData['report'] as $item)
                        <tr>
                            <td>{{ $item['name'] ?? 'N/A' }}</td>
                            <td>{{ $item['id'] ?? 'N/A' }}</td>
                            <td class="text-right">DZD{{ number_format((float)($item['price'] ?? 0), 2) }}</td>
                            <td class="text-right">{{ number_format((int)($item['total_quantity'] ?? ($item['quantity'] ?? 0))) }}</td>
                            <td class="text-right">DZD{{ number_format((float)($item['total_price'] ?? 0), 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <p class="no-data">No sales data for this supermarket.</p>
                @endif
            </div>
        @endforeach
    </div>
    @elseif($isGeneralAdminReport)
    <div class="table-section">
        <h2>Breakdown by Supermarket</h2>
        <p class="no-data">No supermarket breakdown data available.</p>
    </div>
    @endif

    <div class="footer">
        This is an automatically generated report. &copy; {{ date('Y') }} Hypermarket Sales Management System.
    </div>
</body>
</html>