<h2 style="margin: 0 0 14px; font-size: 18px; line-height: 1.4; font-weight: 700; color: #0C0C0C;">
    Ordered Products
</h2>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0"
    style="width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 12px; line-height: 1.5;">

    <thead>
        <tr style="background-color: #F9F8F6;">
            <th align="left" style="padding: 12px 8px; border: 1px solid #E8E6E2; color: #0C0C0C; font-weight: 700;">
                Product
            </th>
            <th align="center" style="padding: 12px 6px; border: 1px solid #E8E6E2; color: #0C0C0C; font-weight: 700;">
                Qty
            </th>
            <th align="right" style="padding: 12px 8px; border: 1px solid #E8E6E2; color: #0C0C0C; font-weight: 700;">
                Unit Price
            </th>
            <th align="right" style="padding: 12px 8px; border: 1px solid #E8E6E2; color: #0C0C0C; font-weight: 700;">
                Delivery
            </th>
            <th align="right" style="padding: 12px 8px; border: 1px solid #E8E6E2; color: #0C0C0C; font-weight: 700;">
                Total
            </th>
        </tr>
    </thead>

    <tbody>
        @foreach ($order->items as $item)
            <tr>
                <td style="padding: 12px 8px; border: 1px solid #E8E6E2; color: #0C0C0C;">
                    <strong>{{ $item->product_name }}</strong>

                    @if ($item->sku)
                        <br>
                        <small style="color: #777777; font-size: 11px;">
                            SKU: {{ $item->sku }}
                        </small>
                    @endif
                </td>

                <td align="center" style="padding: 12px 6px; border: 1px solid #E8E6E2; color: #333333;">
                    {{ $item->quantity }}
                </td>

                <td align="right" style="padding: 12px 8px; border: 1px solid #E8E6E2; color: #333333; white-space: nowrap;">
                    ৳{{ number_format((float) $item->unit_price, 2) }}

                    @if (
                        $item->regular_unit_price !== null
                        && (float) $item->regular_unit_price > (float) $item->unit_price
                    )
                        <br>
                        <small style="color: #888888; text-decoration: line-through;">
                            ৳{{ number_format((float) $item->regular_unit_price, 2) }}
                        </small>
                    @endif
                </td>

                <td align="right" style="padding: 12px 8px; border: 1px solid #E8E6E2; color: #333333; white-space: nowrap;">
                    ৳{{ number_format((float) $item->delivery_charge, 2) }}
                </td>

                <td align="right" style="padding: 12px 8px; border: 1px solid #E8E6E2; color: #0C0C0C; font-weight: 700; white-space: nowrap;">
                    ৳{{ number_format((float) $item->line_total, 2) }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

{{-- Order Totals --}}
<table role="presentation" width="100%" cellpadding="0" cellspacing="0"
    style="width: 100%; border-collapse: collapse; margin-bottom: 28px; font-size: 13px; line-height: 1.6;">

    <tbody>
        <tr>
            <td align="right" style="padding: 9px 12px; color: #555555;">
                Product Subtotal
            </td>
            <td width="35%" align="right" style="padding: 9px 12px; color: #0C0C0C; white-space: nowrap;">
                ৳{{ number_format((float) $order->subtotal, 2) }}
            </td>
        </tr>

        <tr>
            <td align="right" style="padding: 9px 12px; color: #555555;">
                Total Discount
            </td>
            <td align="right" style="padding: 9px 12px; color: #0C0C0C; white-space: nowrap;">
                - ৳{{ number_format((float) $order->discount_amount, 2) }}
            </td>
        </tr>

        <tr>
            <td align="right" style="padding: 9px 12px; color: #555555;">
                Total Delivery Charge
            </td>
            <td align="right" style="padding: 9px 12px; color: #0C0C0C; white-space: nowrap;">
                ৳{{ number_format((float) $order->delivery_charge, 2) }}
            </td>
        </tr>

        <tr>
            <td align="right" style="padding: 15px 12px; background-color: #F9F8F6; border-top: 1px solid #0C0C0C; color: #0C0C0C; font-weight: 700; font-size: 15px;">
                Total Payable
            </td>
            <td align="right" style="padding: 15px 12px; background-color: #F9F8F6; border-top: 1px solid #0C0C0C; color: #0C0C0C; font-weight: 800; font-size: 15px; white-space: nowrap;">
                ৳{{ number_format((float) $order->total_amount, 2) }}
            </td>
        </tr>
    </tbody>
</table>