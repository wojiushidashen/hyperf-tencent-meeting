<?php
// 腾讯会议
return [
    'appId' => env('TENCENT_MEETING_APP_ID', ''),
    'secretId' => env('TENCENT_MEETING_SECRET_ID', ''),
    'secretKey' => env('TENCENT_MEETING_SECRET_KEY', ''),
    'restApi' => [
        'MEETING' => [
            // 创建会议
            'CREATE_MEETING' => [
                'METHOD' => 'POST',
                'URI' => 'https://api.meeting.qq.com/v1/meetings',
            ]
        ]
    ]
];
