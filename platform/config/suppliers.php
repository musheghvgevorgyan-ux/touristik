<?php

/**
 * Supplier API Credentials
 *
 * In production, set actual credentials here. The Hotelbeds credentials
 * can be found in the legacy config at /config/hotelbeds.json on the
 * production server (touristik.am). Do NOT commit real credentials.
 *
 * Legacy keys (hotelbeds.json):
 *   api_key:    51589a...  (32-char hex)
 *   api_secret: ef5498...  (10-char hex)
 */

return [

    'hotelbeds' => [
        'api_key'     => '51589a0544f8a571da617bc0a2dd863a',
        'api_secret'  => 'ef5498bbd8',
        'environment' => 'test', // 'test' or 'live'
    ],

];
