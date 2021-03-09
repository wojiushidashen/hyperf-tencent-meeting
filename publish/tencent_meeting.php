<?php

declare(strict_types=1);
return [
    'appId' => env('TENCENT_MEETING_APP_ID', ''),
    'sdkId' => env('TENCENT_MEETING_APP_SDK_ID', ''),
    'secretId' => env('TENCENT_MEETING_SECRET_ID', ''),
    'secretKey' => env('TENCENT_MEETING_SECRET_KEY', ''),
    'restApi' => [
        'HOST' => 'https://api.meeting.qq.com',
        // 会议
        'MEETING' => [
            // 创建会议
            'CREATE_MEETING' => ['METHOD' => 'POST', 'URI' => '/v1/meetings'],
            // 会议列表
            'MEETING_LIST' => ['METHOD' => 'GET', 'URI' => '/v1/meetings'],
        ],
    ],
];
