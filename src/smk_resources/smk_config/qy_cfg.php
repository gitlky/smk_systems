<?php

return [
    'provider_id'=>2, //1是慧联客
    'url'=>'qy.cdsmartlink.com',
    'is_local'=>false,//是否为本地部署

    'the_key'=>[  //session 键值
        'admin'=>[
            'Session_User_Info'=> 'admin_manager_info',
            'is_developing'=> true,
            'cache_menu_key'=> 'menu_data_with_auth',
        ],
        'wx'=>[
            'corp'=>'wx_corp_id',
            'user'=>'wx_user_id',
            'smk_id'=>'wx_smk_id',
            'user_wx_id'=>'user_wx_id',
        ],
    ],
    'gaode_map'=>[
        'key'=>'f4c9bdd8f7095fe3b70dc8ef041cf290'
    ]
];
