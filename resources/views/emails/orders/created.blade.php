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
            <h1 class="main-title">
                A New Order Has Been Received!
            </h1>

            <p>Hello Admin,</p>

            <p class="intro-text">
                A new order has been placed on your store.
                Please review the order details below.
            </p>

            <div class="order-number">
                <span>Order Number</span>
                <strong>{{ $order->order_number }}</strong>
            </div>

            @include('emails.partials.customer-info')

            @include('emails.partials.payment-info')

            @include('emails.partials.order-products')

            @include('emails.partials.order-summary')

            @include('emails.components.button', [
                'url' => 'https://veringroup.com',
                'text' => 'Visit Store',
            ])
        </div>

        @include('emails.components.footer')
    </div>
</body>
</html>