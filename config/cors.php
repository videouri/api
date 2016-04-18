<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Laravel CORS
    |--------------------------------------------------------------------------
    |

    | allowedOrigins, allowedHeaders and allowedMethods can be set to array('*')
    | to accept any value, the allowed methods however have to be explicitly listed.
    |
    */
    'supportsCredentials' => false,
    'allowedOrigins'      => ['videouri.com', 'local.videouri.com'],
    'allowedHeaders'      => ['Content-Type', 'Accept', 'X-Requested-With', 'X-CSRF-TOKEN', 'Origin'],
    'allowedMethods'      => ['GET', 'POST', 'PUT', 'DELETE'],
    'exposedHeaders'      => [],
    'maxAge'              => 3600,
    'hosts'               => [],
];
