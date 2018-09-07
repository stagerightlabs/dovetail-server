<?php

/*
 * This file is part of Laravel Hashids.
 *
 * (c) Vincent Klaiber <hello@vinkla.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Default Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the connections below you wish to use as
    | your default connection for all work. Of course, you may use many
    | connections at once using the manager class.
    |
    */

    'default' => 'main',

    /*
    |--------------------------------------------------------------------------
    | Hashids Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the connections setup for your application. Example
    | configuration has been included, but you may add as many connections as
    | you would like.
    |
    */

    'connections' => [

        'main' => [
            'salt' => 'PuU3jdQvTwCnG2gx6QFQBEcYu399KrqVMxaTZvEBuBBWtvzLJgAOn59mTseQW0n',
            'length' => 7,
            'alphabet' => 'abcdefghjklmnpqrstuvwxyz0123456789',

        ],

        'invitation' => [
            'salt' => 'fjV1QoALSOduXRDwNrm6g0TpTmLoQqM0Cuvh1axpETpU425n5NnmdXc4LPkWo0w',
            'length' => 16,
            'alphabet' => 'ABCDEFGHJKLMNPQRSTUVWXYZ'
        ],


    ],

];
