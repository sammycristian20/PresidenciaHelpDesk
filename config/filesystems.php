<?php

return [

    /*
      |--------------------------------------------------------------------------
      | Default Filesystem Disk
      |--------------------------------------------------------------------------
      |
      | Here you may specify the default filesystem disk that should be used
      | by the framework. A "local" driver, as well as a variety of cloud
      | based drivers are available for your choosing. Just store away!
      |
      | Supported: "local", "s3", "rackspace"
      |
     */
    'default' => env('DISK_TESTING', 'local'),
    /*
      |--------------------------------------------------------------------------
      | Default Cloud Filesystem Disk
      |--------------------------------------------------------------------------
      |
      | Many applications store files both locally and in the cloud. For this
      | reason, you may specify a default "cloud" driver here. This driver
      | will be bound as the Cloud disk implementation in the container.
      |
     */
    'cloud' => 's3',
    /*
      |--------------------------------------------------------------------------
      | Filesystem Disks
      |--------------------------------------------------------------------------
      |
      | Here you may configure as many filesystem "disks" as you wish, and you
      | may even configure multiple disks of the same driver. Defaults have
      | been setup for each driver as an example of the required options.
      |
     */
    'disks' => [

        'local' => [
            'driver' => 'local',
            'root'   => storage_path() . '/app/attachments',
        ],
        's3' => [
            'driver' => 's3',
            'key'    => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'endpoint' => env('AWS_ENDPOINT'),
        ],
        'rackspace' => [
            'driver'    => 'rackspace',
            'username'  => 'your-username',
            'key'       => 'your-key',
            'container' => 'your-container',
            'endpoint'  => 'https://identity.api.rackspacecloud.com/v2.0/',
            'region'    => 'IAD',
            'url_type'  => 'publicURL',
        ],

        //for KB (now `system` disk is being used; kept for backward compatibility)
        'public' => [
            'driver' => 'local',
            'root' => public_path('uploads')
        ],

        //for ticket attachments (now `system` disk is being used; kept for backward compatibility)
        'private' => [
            'driver' => 'local',
            'root' => storage_path() . '/app/private'
        ],

        'system' => [
            'driver' => 'local',
            'root' => storage_path() . '/app/public',
            'permissions' => [
                'file' => [
                    'public' => 0664,
                    'private' => 0664,
                ],
                'dir' => [
                    'public' => 0775,
                    'private' => 0775,
                ],
            ],
        ]
    ],

    'allowed_mime_types_public' => ['png', 'gif', 'jpg', 'jpeg', 'zip', 'rar', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'pdf', 'csv','txt'],
];
