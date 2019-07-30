<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Freelancer Main Menu
    |--------------------------------------------------------------------------
    |
    |
    */
    'freelancer_main_menu' => [
        'find_jobs' => [
            'route' => 'job.my_proposals',
            'patterns' => [
                'search.job',
                '*job.*'
            ],
            'children' => [
                'proposals' => [
                    'route' => 'job.my_proposals',
                    'children' => [
                        'archived' => [
                            'route' => 'job.my_archived', 
                            'hidden' => true, 
                        ], 
                    ],
                    'seperator' => true
                ],
                'find_jobs' => [
                    'route' => 'search.job'
                ],
                'saved_jobs' => [
                    'route' => 'saved_jobs.index',
                ]
            ],
        ],

        'workroom' => [
            'route' => 'job.my_jobs',
            'patterns' => [
                '*contract.*',
                '*.workdiary.*'
            ],
            'children' => [
                'my_contracts' => [
                    'route' => 'contract.all_contracts',
                ],
                'work_diary' => [
                    'route' => 'workdiary.view_first',
                ]
            ],
        ],

        'report' => [
            'route' => 'report.overview',
            'patterns' => [
                '*.report.*',
                '*timesheet'
            ],
            'children' => [
                'overview' => [
                    'route' => 'report.overview',
                ],
                'timelogs' => [
                    'route' => 'report.timelogs',
                ],
                'timesheet' => [
                    'route' => 'report.timesheet',
                ],
                'transactions' => [
                    'route' => 'report.transactions',
                ],
                /*
                'connection_history' => [
                    'route' => 'report.connections',
                ]
                */
            ],
        ],
        'messages' => [
            'route' => 'message.list',
            'patterns' => [
                'message*'
            ],
            'children' => [],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Freelancer Right Menu
    |--------------------------------------------------------------------------
    |
    |
    */
    'freelancer_right_menu' => [
        /*
        'buyer_workshop' => [
            'route' => 'user.switch',
            'icon' => 'fa fa-user-plus'
        ],
        */
        'profile' => [
            'route' => 'user.my_profile',
            'icon' => 'icon-user',
        ],
        'withdraw' => [
            'route' => 'user.withdraw',
            'icon' => 'icon-wallet',
        ],
        'user_settings' => [
            'route' => 'user.contact_info',
            'icon' => 'icon-settings',
        ],
        'affiliate' => [
            'route' => 'user.affiliate',
            'icon' => 'icon-share',
            'seperator' => true
        ],
        'logout' => [
            'route' => 'user.logout',
            'icon' => 'icon-logout',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Language Menu
    |--------------------------------------------------------------------------
    |
    |
    */
    'enable_lang_menu' => 0,
    'lang_menu' => [
        'en' => [
            'label' => 'English',
            'route' => '/account/update-locale/en',
            'img' => '/assets/images/common/lang_flags/en.png',
        ],
        'ch' => [
            'label' => 'Chinese',
            'route' => '/account/update-locale/ch',
            'img' => '/assets/images/common/lang_flags/ch.png',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Freelancer User Settings Menu
    |--------------------------------------------------------------------------
    |
    |
    */
    'freelancer_user_settings_menu' => [
        'my_info' => [
            'route' => 'user.contact_info',
        ],
        'payment_method' => [
            'route' => 'user.payment_method',
        ],    
        'change_password' => [
            'route' => 'user.change_password',
        ],
        'change_security_question' => [
            'route' => 'user.change_security_question',
        ],
        'notification_settings' => [
            'route' => 'user.notification_settings',
        ],

        'SEPERATOR-1' => true,
        'withdraw' => [
            'route' => 'user.withdraw',
        ],
        'affiliate' => [
            'route' => 'user.affiliate',
            'class' => 'hidden-desktop',
        ],

        /*'SEPERATOR-2' => true,
        'close_my_account' => [
            'route' => 'user.close_my_account',
        ], */

        'logout' => [
            'route' => 'user.logout',
            'class' => 'hidden-desktop',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Freelancer Report Menu
    |--------------------------------------------------------------------------
    |
    |
    */
    'freelancer_report_menu' => [
        'overview' => [
            'route' => 'report.overview',
        ], 
        'timelogs' => [
            'route' => 'report.timelogs',
        ],    
        'timesheet' => [
            'route' => 'report.timesheet',
        ],
        'transaction_history' => [
            'route' => 'report.transactions',
        ],
        'connection_history' => [
            'route' => 'report.connections',
        ],
    ],


    /*
    |--------------------------------------------------------------------------
    | Buyer Main Menu
    |--------------------------------------------------------------------------
    |
    |
    */
    'buyer_main_menu' => [
        'hire_freelancers' => [
            'route' => 'job.my_jobs',
            'patterns' => [
                '*contract.my_freelancers',
                'job.*',
                '*.job.*',
                'search.user',
            ],
            'children' => [
                'my_jobs' => [
                    'route' => 'job.all_jobs'
                ],
                'my_freelancers' => [
                    'route' => 'contract.my_freelancers',
                    'seperator' => true
                ],
                'find_freelancers' => [
                    'route' => 'search.user',
                ],
                'post_a_job' => [
                    'route' => 'job.create',
                ]
            ],
        ],

        'workroom' => [
            'route' => 'search.user',
            'patterns' => [
                'contract.my_contracts',
                '*.workdiary.*'
            ],
            'children' => [
                'my_contracts' => [
                    'route' => 'contract.all_contracts'
                ],
                'work_diary' => [
                    'route' => 'workdiary.view_first',
                    'children' => [
                        'individual_work_diary' => [
                            'route' => 'workdiary.view',
                            'hidden'=> true,
                        ],
                    ]
                ]
            ],
        ],
        'reports' => [
            'route' => 'report.weekly_summary',
            'patterns' => [
                '*report.*'
            ],
            'children' => [
                'weekly_summary' => [
                    'route' => 'report.weekly_summary',
                ],
                'timesheet' => [
                    'route' => 'report.timesheet',
                ],
                'transactions' => [
                    'route' => 'report.transactions',
                ]
            ], 
        ],
        'messages' => [
            'route' => 'message.list',
            'patterns' => [
                'message*'
            ],
            'children' => [],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Buyer Right Menu
    |--------------------------------------------------------------------------
    |
    |
    */
    'buyer_right_menu' => [
        /*
        'freelancer_workshop' => [
            'route' => 'user.switch',
            'icon' => 'fa fa-user-plus'
        ],
        */
        'deposit' => [
            'route' => 'user.deposit',
            'icon' => 'icon-credit-card',
        ],
        'user_settings' => [
            'route' => 'user.contact_info',
            'icon' => 'icon-settings',
        ],
        'affiliate' => [
            'route' => 'user.affiliate',
            'icon' => 'icon-share',
            'seperator' => true
        ],
        'logout' => [
            'route' => 'user.logout',
            'icon' => 'icon-logout',
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Buyer User Settings Menu
    |--------------------------------------------------------------------------
    |
    |
    */
    'buyer_user_settings_menu' => [
        'my_info' => [
            'route' => 'user.contact_info',
        ],
        // 'contact_info' => [
        //     'route' => 'user.contact_info',
        // ],
        'payment_method' => [
            'route' => 'user.payment_method',
        ],    
        'change_password' => [
            'route' => 'user.change_password',
        ],
        'change_security_question' => [
            'route' => 'user.change_security_question',
        ],
        'notification_settings' => [
            'route' => 'user.notification_settings',
        ],
        'SEPERATOR-1' => true,
        'deposit' => [
            'route' => 'user.deposit',
        ],
        'withdraw' => [
            'route' => 'user.withdraw',
        ],
        'affiliate' => [
            'route' => 'user.affiliate',
            'class' => 'hidden-desktop',
        ],
        'logout' => [
            'route' => 'user.logout',
            'class' => 'hidden-desktop',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Buyer Report Menu
    |--------------------------------------------------------------------------
    |
    |
    */
    'buyer_report_menu' => [
        'weekly_summary' => [
            'route' => 'report.weekly_summary',
        ],
        'budgets' => [
            'route' => 'report.budgets',
        ],
        'transactions' => [
            'route' => 'report.transactions',
        ],
        'timesheet' => [
            'route' => 'report.timesheet',
        ],
    ],


    /*
    |--------------------------------------------------------------------------
    | Admin Sidebar
    |--------------------------------------------------------------------------
    |
    | This option provides the sidebar info.
    |
    */
    'sidebar' => [
        'dashboard' => [
            'route' => 'admin.dashboard',
            'icon' => 'icon-home',
        ],
        'users' => [
            'icon' => 'icon-users',
            'children' => [
                'list' => [
                    'route' => 'admin.user.list',
                    'icon' => 'icon-list',
                    'alternates' => [
                    'admin.report.usertransaction'
                    ],
                ],
                'add' => [
                    'route' => 'admin.user.add',
                    'icon' => 'icon-user-follow',
                    'alternates' => ['admin.user.edit'],
                ],
            ],
        ],
        'contracts' => [
            'icon' => 'icon-magic-wand',
            'children' => [
                'list' => [
                    'route' => 'admin.contract.list',
                    'icon' => 'icon-list',
                    'alternates' => [
                        'admin.workdiary.view',
                        'admin.contract.details',
                        'admin.report.transaction'
                    ],
                ],
            ],
        ],
        'jobs' => [
            'icon' => 'icon-rocket',
            'children' => [
                'list' => [
                    'route' => 'admin.job.list',
                    'icon' => 'icon-list',
                ]
            ],
        ],
        'tickets' => [
            'icon' => 'icon-eye',
            'children' => [
                'list' => [
                    'route' => 'admin.ticket.list',
                    'icon' => 'icon-list',
                ],
            ],
        ],
        'notifications' => [
            'icon' => 'icon-bell',
            'children' => [
                'list' => [
                    'route' => 'admin.notification.list',
                    'icon' => 'icon-list',
                ],
                'send' => [
                    'route' => 'admin.notification.send',
                    'icon' => 'icon-bubbles',
                ],
            ],
        ],
        'system' => [
            'icon' => 'icon-settings',
            'children' => [
                'category' => [
                    'route' => 'admin.category.list',
                    'icon' => 'icon-layers',
                ],
                'skill' => [
                    'route' => 'admin.skill.list',
                    'icon' => 'icon-directions',
                ],
                'faq' => [
                    'route' => 'admin.faq.list',
                    'icon' => 'icon-question',
                ],
                'affiliate' => [
                    'route' => 'admin.affiliate.edit',
                    'icon' => 'icon-users',
                ],
                'fee' => [
                    'route' => 'admin.fee.settings',
                    'icon' => 'icon-pie-chart',
                ],
            ],
        ],
    ],
    /*
    |--------------------------------------------------------------------------
    | About Menu
    |--------------------------------------------------------------------------
    |
    |
    */
    'about_menu' => [
        'about_us' => [
            'route' => 'about',
        ],    
        'careers' => [
            'route' => 'about.careers',
        ],
        'team' => [
            'route' => 'about.team',
        ],
        'board' => [
            'route' => 'about.board',
        ],
        'press' => [
            'route' => 'about.press',
        ],
        'contact' => [
            'route' => 'home',
        ],
    ],
];
