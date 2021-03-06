<?php

return [
    'gzsl' => 10, //购置税率 单位：%
    'spf' => 500, //上牌费 单位：元
    'ccs1' => 180, //车船税 1.0L （含）以下
    'ccs2' => 300, //车船税 1.0L - 1.6L （含）
    'ccs3' => 360, //车船税 1.6L - 2.0L （含）
    'ccs4' => 660, //车船税 2.0L - 2.5L （含）
    'ccs5' => 1200, //车船税 2.5L - 3.0L （含）
    'ccs6' => 2400, //车船税 3.0L - 4.0L （含）
    'ccs7' => 3600, //车船税 4.0L以上
    'jqx1' => 950, //家用6座以下
    'jqx2' => 1100, //家用6座以上
    'dszzrx1' => 1369.2, //第三者责任险 50万
    'dszzrx2' => 1783.6, //第三者责任险 100万
    'dszzrx3' => 2046.88, //第三者责任险 150万
    'csryzrx_sj' => 28.7, //1万/座 （司机）
    'csryzrx_ck' => 18.2, //1万/座 （乘客）
    'one_service1' => 3000, //（全款）成交总价 15万及以下，单位：元
    'one_service2' => 4000, //（全款）成交总价 30万及以下
    'one_service3' => 5000, //（全款）成交总价 30万以上

    'dyf' => 200, //抵押费 单位：元

    'clssx_rate' => 0.016173,
    'qcdqx_rate' => 0.003747,
    'blddpsx_rate' => 0.00133,
    'wfzddsf_rate' => 0.0004052,
    'zrssx_rate' => 0.00084,

    'bmbc' => 1.15,

    'dj' => 50000, //定金

    'scene_one' => 'invite_user', //微信参数 场景：邀请用户

    'image_domain' => 'http://res.bpche.com.cn/uploads/',

    //级别名称
    'level1_name' => '新手上路',
    'level2_name' => '车手',
    'level3_name' => '车帝',
    'level4_name' => '车神',

    //管理级别名称
    'manage_level1_name' => '支队长',
    'manage_level2_name' => '大队长',
    'manage_level3_name' => '总队长',

    //最大星星数量
    'level1_star_max' => 2,
    'level2_star_max' => 3,
    'level3_star_max' => 4,
    'level4_star_max' => 5,

    //星星对应的新增客户数
    'client_to_star' => 50,
];
