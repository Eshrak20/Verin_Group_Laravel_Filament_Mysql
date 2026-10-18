
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>New Order Notification</title>

    <style>
        {!! $emailCss !!}
    </style>
    </head>

<body>
    <div class="email-container">
        @include('emails.components.header')

        <div class="email-content">
            <h1>A New Order Has Been Received!</h1>

            <p>Hello Admin,</p>

            <p>
                A new order has been placed on your store.
                Please review the details below.
            </p>

            <h2>
                Order Number: {{ $order->order_number }}
            </h2>

            @include('emails.partials.customer-info')

            @include('emails.partials.payment-info')

            @include('emails.partials.order-products')

            @include('emails.partials.order-summary')

            @include('emails.components.button', [
                'url' => 'http://test.veringroup.com/',
                'text' => 'Visit Store',
            ])
        </div>

        @include('emails.components.footer')
    </div>
</body>
</html>
