<?php

return [

    'paths' => ['api/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'https://felmanicbrandz.com',
        'https://www.felmanicbrandz.com'
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,


    'supports_credentials' => true,

];
