<?php

return [
    'default_gateway' => env('PAYMENT_GATEWAY', 'sandbox'),

    'sandbox' => [
        'enabled'   => true,
        'test_card' => '4242424242424242',
    ],

    'office' => [
        'enabled'      => true,
        'instructions' => 'Visit any of our 3 branches in Yerevan to complete payment.',
    ],
];
