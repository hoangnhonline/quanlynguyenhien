<?php
return [
    'paging' => 100, // number rows for paging
    'uploads' => [
        'storage' => 'local',
        'webpath' => '/media/uploads'
    ],

    'num_alert' => 10, // number rows for alert on top menu
    'upload_path' => public_path() . '/uploads/', // media_upload_path
    'upload_thumbs_path' => public_path() . '/uploads/thumbs/', // media_upload_path
    'upload_thumbs_path_2' => public_path() . '/uploads/thumbs/350x300/',
    'upload_url' => config('app.url') . '/uploads/', // image path,
    'max_size_upload' => 8000000,
    'customer_sources' => [
        'Fanpage' => [
            'Plan To Travel',
            'Tour 4 đảo Phú Quốc - Plan To Travel',
        ],
        'Hotline' => [
            '0911.380.111',
            '0827.308.308',
        ],
        'Zalo' => [
            'Zalo Hotline CTY',
            'Zalo đối tác',
        ],
        'Tiktok' => [
            'Phú Quốc Có Gì?',
            'Plan To Travel Phú Quốc',
        ],
        'Group FB' => null,
        'Khác' => null
    ]
];

?>
