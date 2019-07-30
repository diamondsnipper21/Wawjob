<?php

return [
    // Res Version
    'res_version' => [
        'frontend' => '1.3.69',
        'backend' => '1.3.46'
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Settings
    |--------------------------------------------------------------------------
    |
    |
    */
    'admin' => [
        'per_page' => 10,
        'detail_page' => 5,
        'avatar_size' => 32,
        'description_preload_length' => 330,
        'description_modal_reason_length' => 30,
    ],

    'timezone' => 'Asia/Chongqing',

    /*
    |--------------------------------------------------------------------------
    | FrontEnd Settings
    |--------------------------------------------------------------------------
    |
    |
    */
    'frontend' => [
        'per_page' => 10,
        'use_account_both' => false
    ],

    'reserved_user_ids' => [
        '*ijobdesk*',
        '*ijob_desk*',
        '*i_job_desk*',
        '*xisensoft*',
        'jobdesk',
        'admin',
        'superadmin',
        'administrator',
        'freelancer',
        'buyer',
        'client',
        'xisen',
        'bot',
        'test'
    ],

    /*
    |--------------------------------------------------------------------------
    | Frontend Settings
    |--------------------------------------------------------------------------
    |
    |
    */
    'hourly_log_unit' => 10,

    /*
    |--------------------------------------------------------------------------
    | Buyer Settings
    |--------------------------------------------------------------------------
    |
    |
    */
    'buyer' => [
        'job' => [
            'per_page' => 10,
        ],

	   'contract' => [
            'filesize' => 26214400
        ],

        'my_job' => [
            'per_page' => 5
        ],

        'contracts' => [
            'per_page' => 10,
        ],

        'proposals' => [
            'per_page' => 10,
        ],

        'messages' => [
            'per_page' => 10,
        ],

        'workdiary' => [
            'memo' => 100,    
        ],
    ],

    'freelancer' => [
        'per_page' => 10,

        'proposals' => [
            'per_page' => 10,
        ],

        'contracts' => [
            'per_page' => 10,
        ],

        'user' => [
            'title_length' => 50,
            'keyword_length' => 100,
            'description_preload_length' => 168,
            'description_length' => 5000,
            'portfolio_image_width' => 960
        ],

        'workdiary' => [
            'memo' => 100,    
        ],
    ],

    'payment_gateway' => 'Test',

    'uploads' => [
        'max_count' => 20,
        'file_size' => 25 * 1024 * 1024, // 25 MBytes
        'file_types' => [
            'zip', 
            'rar', 
            'tar', 
            'gzip', 
            'pdf', 
            'doc', 
            'txt',
            'docx', 
            'bmp', 
            'jpg', 
            'png', 
            'xls', 
            'xlsx', 
            'ppt', 
            'pptx'
        ],
        'image_types' => [
            'bmp', 
            'jpg', 
            'jpeg', 
            'png'
        ],
        'id_verification_types' => [
            'bmp', 
            'jpg', 
            'jpeg', 
            'png', 
            'pdf'
        ]
    ],

];