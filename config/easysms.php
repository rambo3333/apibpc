<?php
return [
    // HTTP ����ĳ�ʱʱ�䣨�룩
    'timeout' => 5.0,

    // Ĭ�Ϸ�������
    'default' => [
        // ���ص��ò��ԣ�Ĭ�ϣ�˳�����
        'strategy' => \Overtrue\EasySms\Strategies\OrderStrategy::class,

        // Ĭ�Ͽ��õķ�������
        'gateways' => [
            'yunpian',
        ],
    ],
    // ���õ���������
    'gateways' => [
        'errorlog' => [
            'file' => '/tmp/easy-sms.log',
        ],
        'yunpian' => [
            'api_key' => env('YUNPIAN_API_KEY'),
        ],
    ],
];