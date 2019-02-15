<?php
return [
    'page_size' => env('PAGE_SIZE', 10),

    'admin_menu' => [
        [
            'name'  => '基本',
            'children' => [
                [
                    'name'  => '控制台',
                    'to'    => '/admin'
                ]
            ]
        ],
        [
            'name' => 'Packges',
            'children' => [
                [
                    'name'  => '添加',
                    'to'    => '/admin/packages/add'
                ], [
                    'name'  => '列表',
                    'to'    => '/admin/packages'
                ], [
                    'name'  => '日志',
                    'to'    => '/admin/packages/log'
                ]
            ]
        ],
        [
            'name'  => '设置',
            'children' => [
                [
                    'name'  => '基本',
                    'to'    => '/admin/setup'
                ], [
                    'name'  => '缓存',
                    'to'    => '/admin/cache'
                ]
            ]
        ],
        [
            'name'  => '个人',
            'children' => [
                [
                    'name'  => '修改密码',
                    'to'    => '/admin/personal/password'
                ], [
                    'name'  => '退出登陆',
                    'to'    => '/admin/login'
                ]
            ]
        ]
    ],
    
    'monitor' => [
        'flux' => [
            'name'      => '流量',
            'filter'    => 1
        ],
        'bandwidth' => [
            'name'      => '带宽',
            'filter'    => 2
        ],
        'statusCode' => [
            'name'      => '状态码',
            'filter'    => 3
        ]
    ],

    'basic' => [
        'bandwidth' => [
            'name' => '最大带宽'
        ],
        'flux' => [
            'name' => '总流量'
        ],
        'request' => [
            'name' => '总请求数'
        ],
        'fluxHitRate' => [
            'name' => '总命中率'
        ]
    ],

    'cache_expire' => env('CACHE_EXPICE', 10),

    'delete_files' => env('DELETE_FILE', false),

    'storage' => [
        'bucket'        => env('STORAGE_SECRET_BUCKET'),
        'secret_id'     => env('STORAGE_SECRET_ID'),
        'secret_key'    => env('STORAGE_SECRET_KEY'),
        'region'        => env('STORAGE_REGION'),
        'domain'        => env('STORAGE_CDN_DOMAIN'),
        'path'          => env('STORAGE_PATH')
    ],

    'suffix_show' => [
        'js',
        'css'
    ],

    'cron' => env('CRON', '0 */1 * * *'),

    'python_name' => env('PYTHON_NAME', 'python3')
];
