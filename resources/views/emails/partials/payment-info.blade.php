<h2 style="margin: 0 0 14px; font-size: 18px; line-height: 1.4; font-weight: 700; color: #0C0C0C;">
    Payment Information
</h2>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0"
    style="width: 100%; border-collapse: collapse; margin-bottom: 28px; font-size: 13px; line-height: 1.6;">

    <tbody>
        <tr>
            <td width="35%" style="padding: 12px; border: 1px solid #E8E6E2; background-color: #F9F8F6; color: #0C0C0C; font-weight: 700;">
                Payment Method
            </td>
            <td style="padding: 12px; border: 1px solid #E8E6E2; color: #333333;">
                {{ strtoupper($order->payment_method) }}

                @if ($order->payment_provider)
                    ({{ ucfirst($order->payment_provider) }})
                @endif
            </td>
        </tr>

        <tr>
            <td style="padding: 12px; border: 1px solid #E8E6E2; background-color: #F9F8F6; color: #0C0C0C; font-weight: 700;">
                Payment Status
            </td>
            <td style="padding: 12px; border: 1px solid #E8E6E2; color: #0C0C0C; font-weight: 700;">
                {{ ucfirst($order->payment_status) }}
            </td>
        </tr>

        @if ($order->payment_method === 'online' && $order->transaction_id)
            <tr>
                <td style="padding: 12px; border: 1px solid #E8E6E2; background-color: #F9F8F6; color: #0C0C0C; font-weight: 700;">
                    Transaction ID
                </td>
                <td style="padding: 12px; border: 1px solid #E8E6E2; color: #333333; overflow-wrap: anywhere; word-break: break-all;">
                    {{ $order->transaction_id }}
                </td>
            </tr>
        @endif
    </tbody>
</table>