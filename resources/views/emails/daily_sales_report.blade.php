<h1>Daily Sales Report</h1>

<table border="1" cellpadding="10">
    <thead>
        <tr>
            <th>Product Name</th>
            <th>Total Quantity Sold</th>
            <th>Total Price</th>
        </tr>
    </thead>
    <tbody>
        @foreach($report['report'] as $item)
            <tr>
                <td>{{ $item->name }}</td>
                <td>{{ $item->total_quantity }}</td>
                <td>{{ number_format($item->total_price, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<h3>Total Revenue: ${{ number_format($report['total_money'], 2) }}</h3>
