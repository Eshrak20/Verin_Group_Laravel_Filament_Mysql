
<h2 style="margin-bottom: 12px;">Payment Information</h2>

<table width="100%" cellpadding="10" cellspacing="0"
    style="border-collapse: collapse; margin-bottom: 24px;">
    <tbody>
        <tr>
            <td width="35%" style="border: 1px solid #ddd; font-weight: bold;">
                Payment Method
            </td>
            <td style="border: 1px solid #ddd;">
                {{ strtoupper($order->payment_method) }}

                @if ($order->payment_provider)
                    ({{ ucfirst($order->payment_provider) }})
                @endif
            </td>
        </tr>

        <tr>
            <td style="border: 1px solid #ddd; font-weight: bold;">
                Payment Status
            </td>
            <td style="border: 1px solid #ddd;">
                {{ ucfirst($order->payment_status) }}
            </td>
        </tr>

        @if ($order->payment_method === 'online' && $order->transaction_id)
            <tr>
                <td style="border: 1px solid #ddd; font-weight: bold;">
                    Transaction ID
                </td>
                <td style="border: 1px solid #ddd; word-break: break-word;">
                    {{ $order->transaction_id }}
                </td>
            </tr>
        @endif
    </tbody>
</table>
