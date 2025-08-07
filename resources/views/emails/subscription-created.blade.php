<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tax Invoice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .invoice-header {
            text-align: center;
            margin-bottom: 25px;
        }

        .business-details, .client-details {
            margin-bottom: 20px;
        }

        .invoice-table {
            width: 100%;
            border-collapse: collapse;
        }

        .invoice-table th, .invoice-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .total-row {
            font-weight: bold;
        }

        .footer {
            font-size: 0.9em;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="invoice-header">
    <img src="{{ url('logo.png') }}" alt="TrinityAi Logo" class="logo" width="100">
    <h1>Tax Invoice</h1>
</div>

<div class="business-details">
    <strong>TrinityAi</strong><br>
    Date: {{ $date->format('d F Y') }}<br>
    Invoice #: {{ $invoiceNumber }}
</div>

<div class="client-details">
    <strong>To:</strong><br>
    {{ $user->first_name }} {{ $user->last_name }}<br>
    {{ $user->email }}
</div>

<table class="invoice-table">
    <thead>
    <tr>
        <th>Description</th>
        <th>Amount</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>{{ $planName }}</td>
        <td>${{ number_format($baseAmount, 2) }}</td>
    </tr>
    <tr>
        <td>GST</td>
        <td>${{ number_format($gst, 2) }}</td>
    </tr>
    <tr class="total-row">
        <td>Total (Including GST)</td>
        <td>${{ number_format($totalAmount, 2) }}</td>
    </tr>
    </tbody>
</table>

@if(isset($coupon))
    <div style="margin-top: 10px;">
        <strong>Discount Applied:</strong>
        @if($coupon->percent_off)
            {{ $coupon->percent_off }}% off
        @elseif($coupon->amount_off)
            ${{ number_format($coupon->amount_off/100, 2) }} off
        @endif
    </div>
@endif

<div class="footer">
    This document is intended to be a tax invoice for GST purposes.
</div>
</body>
</html>
