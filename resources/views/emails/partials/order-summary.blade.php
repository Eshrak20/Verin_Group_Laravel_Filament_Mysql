
<h2 style="margin-bottom: 12px;">Order Summary</h2>

<table width="100%" cellpadding="10" cellspacing="0"
    style="border-collapse: collapse; margin-bottom: 24px;">
    <tbody>
        <tr>
            <td align="right" style="border: 1px solid #ddd;">
                Product Subtotal
            </td>
            <td width="35%" align="right" style="border: 1px solid #ddd;">
                ৳{{ number_format((float) $order->subtotal, 2) }}
            </td>
        </tr>

        <tr>
            <td align="right" style="border: 1px solid #ddd;">
                Total Discount
            </td>
            <td align="right" style="border: 1px solid #ddd; color: #16a34a;">
                - ৳{{ number_format((float) $order->discount_amount, 2) }}
            </td>
        </tr>

        <tr>
            <td align="right" style="border: 1px solid #ddd;">
                Total Delivery Charge
            </td>
            <td align="right" style="border: 1px solid #ddd;">
                ৳{{ number_format((float) $order->delivery_charge, 2) }}
            </td>
        </tr>

        <tr style="font-weight: bold; background-color: #f3f4f6;">
            <td align="right" style="padding: 14px; border: 1px solid #ddd;">
                Total Payable
            </td>
            <td align="right" style="padding: 14px; border: 1px solid #ddd;">
                ৳{{ number_format((float) $order->total_amount, 2) }}
            </td>
        </tr>
    </tbody>
</table>
