<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: DejaVu Sans; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 6px; }
        th { background: #f2f2f2; }
    </style>
</head>
<body>

<h2>Quotation</h2>

<p>
<b>Quotation No:</b> {{ $quotation->quotation_no }}<br>
<b>Customer:</b> {{ $quotation->customer->customer_name }}<br>
<b>Mobile:</b> {{ $quotation->customer->mobile }}
</p>

<table>
<thead>
<tr>
  <th>Product</th>
  <th>Qty</th>
  <th>Supply Rate</th>
  <th>GST</th>
  <th>Total</th>
</tr>
</thead>

<tbody>
@foreach($quotation->items as $item)
<tr>
  <td>{{ $item->product->name }}</td>
  <td>{{ $item->qty }}</td>
  <td>{{ number_format($item->supply_rate,2) }}</td>
  <td>{{ number_format($item->gst_amount,2) }}</td>
  <td>{{ number_format($item->total_amount,2) }}</td>
</tr>
@endforeach
</tbody>
</table>

<h3 style="text-align:right">
Grand Total: ₹ {{ number_format($quotation->grand_total,2) }}
</h3>

</body>
</html>
