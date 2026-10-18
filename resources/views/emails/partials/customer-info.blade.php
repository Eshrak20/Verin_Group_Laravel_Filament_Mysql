<h2 style="margin: 0 0 14px; font-size: 18px; line-height: 1.4; font-weight: 700; color: #0C0C0C;">
    Customer Information
</h2>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0"
    style="width: 100%; border-collapse: collapse; margin-bottom: 28px; font-size: 13px; line-height: 1.6;">

    <tbody>
        <tr>
            <td width="35%" style="padding: 11px 12px; border: 1px solid #E8E6E2; background-color: #F9F8F6; color: #0C0C0C; font-weight: 700;">
                Customer Name
            </td>
            <td style="padding: 11px 12px; border: 1px solid #E8E6E2; color: #333333;">
                {{ $order->customer_name }}
            </td>
        </tr>

        <tr>
            <td width="35%" style="padding: 11px 12px; border: 1px solid #E8E6E2; background-color: #F9F8F6; color: #0C0C0C; font-weight: 700;">
                Phone
            </td>
            <td style="padding: 11px 12px; border: 1px solid #E8E6E2; color: #333333;">
                {{ $order->phone }}
            </td>
        </tr>

        <tr>
            <td width="35%" style="padding: 11px 12px; border: 1px solid #E8E6E2; background-color: #F9F8F6; color: #0C0C0C; font-weight: 700;">
                Email
            </td>
            <td style="padding: 11px 12px; border: 1px solid #E8E6E2; color: #333333; overflow-wrap: anywhere;">
                {{ $order->email ?: 'Not provided' }}
            </td>
        </tr>

        <tr>
            <td width="35%" style="padding: 11px 12px; border: 1px solid #E8E6E2; background-color: #F9F8F6; color: #0C0C0C; font-weight: 700;">
                Delivery Address
            </td>
            <td style="padding: 11px 12px; border: 1px solid #E8E6E2; color: #333333;">
                {{ $order->delivery_address }}{{ $order->area ? ', ' . $order->area : '' }}{{ $order->district ? ', ' . $order->district : '' }}
            </td>
        </tr>

        <tr>
            <td width="35%" style="padding: 11px 12px; border: 1px solid #E8E6E2; background-color: #F9F8F6; color: #0C0C0C; font-weight: 700;">
                Delivery Zone
            </td>
            <td style="padding: 11px 12px; border: 1px solid #E8E6E2; color: #333333;">
                {{ $order->delivery_zone === 'inside_dhaka' ? 'Inside Dhaka' : 'Outside Dhaka' }}
            </td>
        </tr>

        @if ($order->customer_note)
            <tr>
                <td width="35%" style="padding: 11px 12px; border: 1px solid #E8E6E2; background-color: #F9F8F6; color: #0C0C0C; font-weight: 700;">
                    Customer Note
                </td>
                <td style="padding: 11px 12px; border: 1px solid #E8E6E2; color: #333333; overflow-wrap: anywhere;">
                    {{ $order->customer_note }}
                </td>
            </tr>
        @endif
    </tbody>
</table>