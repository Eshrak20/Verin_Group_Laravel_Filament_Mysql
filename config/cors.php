<?php
return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['http://localhost:5173', 'https://e-commerce-hazel.vercel.app', 'https://veringroup.com','http://192.168.0.104:5173'], // <-- your Vite frontend

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true, // <-- important for cookies
];
