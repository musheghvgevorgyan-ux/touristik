<?php

return [
    // Default gateway used when none is explicitly selected.
    // Options: 'sandbox', 'office', or a real gateway name (e.g. 'stripe', 'ameriabank')
    'default_gateway' => 'sandbox',

    // ─── Sandbox (test) ──────────────────────────────────────
    'sandbox' => [
        'enabled'   => true,
        'test_card' => '4242424242424242', // Always succeeds
        // '4000000000000002' always declines (for testing failure flows)
    ],

    // ─── Office (pay at branch) ──────────────────────────────
    'office' => [
        'enabled'      => true,
        'instructions' => 'Visit any of our 3 branches in Yerevan to complete payment.',
    ],

    // ─── Future gateways ─────────────────────────────────────
    // 'ameriabank' => [
    //     'enabled'     => false,
    //     'merchant_id' => '',
    //     'secret_key'  => '',
    //     'test_mode'   => true,
    //     'callback_url' => '/payment/callback?gateway=ameriabank',
    // ],
    //
    // 'stripe' => [
    //     'enabled'        => false,
    //     'publishable_key' => '',
    //     'secret_key'      => '',
    //     'webhook_secret'  => '',
    //     'callback_url'    => '/payment/callback?gateway=stripe',
    // ],
];
