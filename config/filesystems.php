<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    */

    'default' => getenv('FILESYSTEM_DISK') ?: 'public_uploads',

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Hostinger serves public files from public_html only. Public uploads
    | should use public_uploads so URLs point directly at APP_URL/<path>.
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root'   => base_path('private'),
            'serve'  => true,
            'throw'  => false,
            'report' => false,
        ],

        'public' => [
            'driver'     => 'local',
            'root'       => public_path(),
            'url'        => rtrim(getenv('APP_URL') ?: 'http://localhost:8000', '/'),
            'visibility' => 'public',
            'throw'      => false,
            'report'     => false,
        ],

        'public_uploads' => [
            'driver'     => 'local',
            'root'       => public_path(),
            'url'        => rtrim(getenv('APP_URL') ?: 'http://localhost:8000', '/'),
            'visibility' => 'public',
            'throw'      => false,
            'report'     => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Public Links
    |--------------------------------------------------------------------------
    |
    | Hostinger does not need public symlinks.
    */

    'links' => [],

];
