
<h2 style="margin-bottom: 12px;">Customer Information</h2>

<table width="100%" cellpadding="10" cellspacing="0"
    style="border-collapse: collapse; margin-bottom: 24px;">
    <tbody>
        <tr>
            <td width="35%" style="border: 1px solid #ddd; font-weight: bold;">
                Customer Name
            </td>
            <td style="border: 1px solid #ddd;">
                {{ $order->customer_name }}
            </td>
        </tr>

        <tr>
            <td style="border: 1px solid #ddd; font-weight: bold;">
                Phone
            </td>
            <td style="border: 1px solid #ddd;">
                {{ $order->phone }}
            </td>
        </tr>

        <tr>
            <td style="border: 1px solid #ddd; font-weight: bold;">
                Email
            </td>
            <td style="border: 1px solid #ddd;">
                {{ $order->email ?: 'Not provided' }}
            </td>
        </tr>

        <tr>
            <td style="border: 1px solid #ddd; font-weight: bold;">
                Delivery Address
            </td>
            <td style="border: 1px solid #ddd;">
                {{ $order->delivery_address }},
                {{ $order->area ? $order->area . ', ' : '' }}
                {{ $order->district ?? '' }}
            </td>
        </tr>

        <tr>
            <td style="border: 1px solid #ddd; font-weight: bold;">
                Delivery Zone
            </td>
            <td style="border: 1px solid #ddd;">
                {{ $order->delivery_zone === 'inside_dhaka'
                    ? 'Inside Dhaka'
                    : 'Outside Dhaka' }}
            </td>
        </tr>

        @if ($order->customer_note)
            <tr>
                <td style="border: 1px solid #ddd; font-weight: bold;">
                    Customer Note
                </td>
                <td style="border: 1px solid #ddd;">
                    {{ $order->customer_note }}
                </td>
            </tr>
        @endif
    </tbody>
</table>
