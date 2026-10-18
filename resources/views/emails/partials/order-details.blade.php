<h2>Ordered Products</h2>

<table width="100%" cellpadding="0" cellspacing="0"
    style="border-collapse: collapse; margin-bottom: 20px;">
    <thead>
        <tr style="background-color: #f3f4f6;">
            <th align="left" style="padding: 10px; border: 1px solid #ddd;">
                Product
            </th>
            <th align="center" style="padding: 10px; border: 1px solid #ddd;">
                Qty
            </th>
            <th align="right" style="padding: 10px; border: 1px solid #ddd;">
                Unit Price
            </th>
            <th align="right" style="padding: 10px; border: 1px solid #ddd;">
                Delivery
            </th>
            <th align="right" style="padding: 10px; border: 1px solid #ddd;">
                Total
            </th>
        </tr>
    </thead>

    <tbody>
        @foreach ($order->items as $item)
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd;">
                    <strong>{{ $item->product_name }}</strong>

                    @if ($item->sku)
                        <br>
                        <small>SKU: {{ $item->sku }}</small>
                    @endif
                </td>

                <td align="center" style="padding: 10px; border: 1px solid #ddd;">
                    {{ $item->quantity }}
                </td>

                <td align="right" style="padding: 10px; border: 1px solid #ddd;">
                    ৳{{ number_format((float) $item->unit_price, 2) }}

                    @if (
                        $item->regular_unit_price !== null
                        && (float) $item->regular_unit_price > (float) $item->unit_price
                    )
                        <br>
                        <small style="color: #777; text-decoration: line-through;">
                            ৳{{ number_format((float) $item->regular_unit_price, 2) }}
                        </small>
                    @endif
                </td>

                <td align="right" style="padding: 10px; border: 1px solid #ddd;">
                    ৳{{ number_format((float) $item->delivery_charge, 2) }}
                </td>

                <td align="right" style="padding: 10px; border: 1px solid #ddd;">
                    ৳{{ number_format((float) $item->line_total, 2) }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<table width="100%" cellpadding="8" cellspacing="0"
    style="border-collapse: collapse;">
    <tr>
        <td align="right">Product Subtotal</td>
        <td align="right">
            ৳{{ number_format((float) $order->subtotal, 2) }}
        </td>
    </tr>

    <tr>
        <td align="right">Total Discount</td>
        <td align="right" style="color: #16a34a;">
            - ৳{{ number_format((float) $order->discount_amount, 2) }}
        </td>
    </tr>

    <tr>
        <td align="right">Total Delivery Charge</td>
        <td align="right">
            ৳{{ number_format((float) $order->delivery_charge, 2) }}
        </td>
    </tr>

    <tr style="font-weight: bold; background-color: #f3f4f6;">
        <td align="right">Total Payable</td>
        <td align="right">
            ৳{{ number_format((float) $order->total_amount, 2) }}
        </td>
    </tr>
</table>