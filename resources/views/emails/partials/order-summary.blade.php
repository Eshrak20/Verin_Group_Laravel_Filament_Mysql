<h2 style="margin: 0 0 14px; font-size: 18px; line-height: 1.4; font-weight: 700; color: #0C0C0C;">
    Order Summary
</h2>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0"
    style="width: 100%; border-collapse: collapse; margin-bottom: 28px; font-size: 13px; line-height: 1.6;">

    <tbody>
        <tr>
            <td align="right" style="padding: 12px; border: 1px solid #E8E6E2; color: #555555;">
                Product Subtotal
            </td>
            <td width="35%" align="right" style="padding: 12px; border: 1px solid #E8E6E2; color: #0C0C0C; white-space: nowrap;">
                ৳{{ number_format((float) $order->subtotal, 2) }}
            </td>
        </tr>

        <tr>
            <td align="right" style="padding: 12px; border: 1px solid #E8E6E2; color: #555555;">
                Total Discount
            </td>
            <td align="right" style="padding: 12px; border: 1px solid #E8E6E2; color: #0C0C0C; white-space: nowrap;">
                - ৳{{ number_format((float) $order->discount_amount, 2) }}
            </td>
        </tr>

        <tr>
            <td align="right" style="padding: 12px; border: 1px solid #E8E6E2; color: #555555;">
                Total Delivery Charge
            </td>
            <td align="right" style="padding: 12px; border: 1px solid #E8E6E2; color: #0C0C0C; white-space: nowrap;">
                ৳{{ number_format((float) $order->delivery_charge, 2) }}
            </td>
        </tr>

        <tr>
            <td align="right" style="padding: 15px 12px; border: 1px solid #E8E6E2; border-top: 2px solid #0C0C0C; background-color: #F9F8F6; color: #0C0C0C; font-size: 15px; font-weight: 700;">
                Total Payable
            </td>
            <td align="right" style="padding: 15px 12px; border: 1px solid #E8E6E2; border-top: 2px solid #0C0C0C; background-color: #F9F8F6; color: #0C0C0C; font-size: 15px; font-weight: 800; white-space: nowrap;">
                ৳{{ number_format((float) $order->total_amount, 2) }}
            </td>
        </tr>
    </tbody>
</table>