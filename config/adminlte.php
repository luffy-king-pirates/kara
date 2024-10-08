<?php
use Illuminate\Support\Facades\Auth;

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    |
    | Here you can change the default title of your admin panel.
    |
    | For detailed instructions you can look the title section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'title' => 'Kara',
    'title_prefix' => '',
    'title_postfix' => '',

    /*
    |--------------------------------------------------------------------------
    | Favicon
    |--------------------------------------------------------------------------
    |
    | Here you can activate the favicon.
    |
    | For detailed instructions you can look the favicon section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_ico_only' => false,
    'use_full_favicon' => false,

    /*
    |--------------------------------------------------------------------------
    | Google Fonts
    |--------------------------------------------------------------------------
    |
    | Here you can allow or not the use of external google fonts. Disabling the
    | google fonts may be useful if your admin panel internet access is
    | restricted somehow.
    |
    | For detailed instructions you can look the google fonts section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'google_fonts' => [
        'allowed' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Logo
    |--------------------------------------------------------------------------
    |
    | Here you can change the logo of your admin panel.
    |
    | For detailed instructions you can look the logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'logo' => '<b>Kara Traders</b>LTD',
    'logo_img' => 'https://res.cloudinary.com/dx8hb4haj/image/upload/v1726793346/karaLogo_ievyhl.png',
    'logo_img_class' => 'brand-image img-circle elevation-3',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xs',
    'logo_img_alt' => 'Admin Logo',

    /*
    |--------------------------------------------------------------------------
    | Authentication Logo
    |--------------------------------------------------------------------------
    |
    | Here you can setup an alternative logo to use on your login and register
    | screens. When disabled, the admin panel logo will be used instead.
    |
    | For detailed instructions you can look the auth logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'auth_logo' => [
        'enabled' => false,
        'img' => [
            'path' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
            'alt' => 'Auth Logo',
            'class' => '',
            'width' => 50,
            'height' => 50,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Preloader Animation
    |--------------------------------------------------------------------------
    |
    | Here you can change the preloader animation configuration. Currently, two
    | modes are supported: 'fullscreen' for a fullscreen preloader animation
    | and 'cwrapper' to attach the preloader animation into the content-wrapper
    | element and avoid overlapping it with the sidebars and the top navbar.
    |
    | For detailed instructions you can look the preloader section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'preloader' => [
        'enabled' => true,
        'mode' => 'fullscreen',
        'img' => [
            'path' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
            'alt' => 'AdminLTE Preloader Image',
            'effect' => 'animation__shake',
            'width' => 60,
            'height' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Menu
    |--------------------------------------------------------------------------
    |
    | Here you can activate and change the user menu.
    |
    | For detailed instructions you can look the user menu section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'usermenu_enabled' => true,
    'usermenu_header' => false,
    'usermenu_header_class' => 'bg-primary',
   'usermenu_image' => 'path/to/default/image.png',

    'usermenu_desc' => true,
    'usermenu_profile_url' => false,

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | Here we change the layout of your admin panel.
    |
    | For detailed instructions you can look the layout section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => null,
    'layout_fixed_navbar' => null,
    'layout_fixed_footer' => null,
    'layout_dark_mode' => null,

    /*
    |--------------------------------------------------------------------------
    | Authentication Views Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the authentication views.
    |
    | For detailed instructions you can look the auth classes section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_auth_card' => 'card-outline card-primary',
    'classes_auth_header' => '',
    'classes_auth_body' => '',
    'classes_auth_footer' => '',
    'classes_auth_icon' => '',
    'classes_auth_btn' => 'btn-flat btn-primary',

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the admin panel.
    |
    | For detailed instructions you can look the admin panel classes here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_body' => '',
    'classes_brand' => '',
    'classes_brand_text' => '',
    'classes_content_wrapper' => '',
    'classes_content_header' => '',
    'classes_content' => '',
    'classes_sidebar' => 'sidebar-dark-primary elevation-4',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-white navbar-light',
    'classes_topnav_nav' => 'navbar-expand',
    'classes_topnav_container' => 'container',

    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar of the admin panel.
    |
    | For detailed instructions you can look the sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'sidebar_mini' => 'lg',
    'sidebar_collapse' => false,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => false,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,

    /*
    |--------------------------------------------------------------------------
    | Control Sidebar (Right Sidebar)
    |--------------------------------------------------------------------------
    |
    | Here we can modify the right sidebar aka control sidebar of the admin panel.
    |
    | For detailed instructions you can look the right sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Here we can modify the url settings of the admin panel.
    |
    | For detailed instructions you can look the urls section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_route_url' => false,
    'dashboard_url' => 'home',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => 'register',
    'password_reset_url' => 'password/reset',
    'password_email_url' => 'password/email',
    'profile_url' => false,

    /*
    |--------------------------------------------------------------------------
    | Laravel Mix
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Laravel Mix option for the admin panel.
    |
    | For detailed instructions you can look the laravel mix section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'enabled_laravel_mix' => false,
    'laravel_mix_css_path' => 'css/app.css',
    'laravel_mix_js_path' => 'js/app.js',

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar/top navigation of the admin panel.
    |
    | For detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'menu' => [
        // Navbar items:
        [
            'type' => 'navbar-search',
            'text' => 'search',
            'topnav_right' => true,
        ],
        [
            'type' => 'navbar-user',
            'topnav_right' => true,
            'text' => 'ok', // Display user's name
        ],
        [
            'type' => 'fullscreen-widget',
            'topnav_right' => true,
        ],

        //Sidebar items:
        [
            'type' => 'sidebar-menu-search',
            'text' => 'search',
        ],
        [
            'text' => 'Users',
            'url' => 'users',
            'icon' => 'fas fa-fw fa-user', // Font Awesome icon for user
        ],

        // [
        //     'text' => 'pages',
        //     'url' => 'admin/pages',
        //     'icon' => 'far fa-fw fa-file',
        //     'label' => 4,
        //     'label_color' => 'success',
        // ],
        // ['header' => 'account_settings'],
        // [
        //     'text' => 'profile',
        //     'url' => 'admin/settings',
        //     'icon' => 'fas fa-fw fa-user',
        // ],
        // [
        //     'text' => 'change_password',
        //     'url' => 'admin/settings',
        //     'icon' => 'fas fa-fw fa-lock',
        // ],
        // [
        //     'text' => 'multilevel',
        //     'icon' => 'fas fa-fw fa-share',
        //     'submenu' => [
        //         [
        //             'text' => 'level_one',
        //             'url' => '#',
        //         ],
        //         [
        //             'text' => 'level_one',
        //             'url' => '#',
        //             'submenu' => [
        //                 [
        //                     'text' => 'level_two',
        //                     'url' => '#',
        //                 ],
        //                 [
        //                     'text' => 'level_two',
        //                     'url' => '#',
        //                     'submenu' => [
        //                         [
        //                             'text' => 'level_three',
        //                             'url' => '#',
        //                         ],
        //                         [
        //                             'text' => 'level_three',
        //                             'url' => '#',
        //                         ],
        //                     ],
        //                 ],
        //             ],
        //         ],
        //         [
        //             'text' => 'level_one',
        //             'url' => '#',
        //         ],
        //     ],
        // ],
        [
            'text' => 'Stock Transfert',
            'icon' => 'fas fa-boxes',
            'submenu' => [
                [
                    'icon' => 'fas fa-money-bill-wave', // Icon for Cash submenu
                    'text' => 'Godown to Shop',

                    'submenu' => [
                        [
                            'icon' => 'fas fa-plus-circle', // Icon for Create Godown to Shop
                            'text' => 'Godown to Shop (Create)',
                            'url' => 'godownshop/create',
                            'can' => 'manage-godwan-to-shop'
                        ],
                        [
                            'icon' => 'fas fa-box-open', // Icon for Existing Godown to Shop
                            'text' => 'Godown to Shop (View)',
                            'url' => 'godownshop',
                            'can' => 'create-godwan-to-shop'
                        ]
                    ],
                ],
                [
                    'icon' => 'fas fa-warehouse', // Icon for Godown to Shop submenu
                    'text' => 'Godown to Shop (Ashok)',
                    'submenu' => [
                        [
                            'icon' => 'fas fa-truck-loading', // Icon for Create Godown to Shop
                            'text' => 'Godown to Shop (Ashok) (Create)',
                            'url' => 'godownShopAshok/create',
                            'can'=> 'manage-godwan-to-shop-ashok'
                        ],
                        [
                            'icon' => 'fas fa-eye', // Icon for View Godown to Shop
                            'text' => 'Godown to Shop (Ashok) (View)',
                            'url' => 'godownShopAshok',
                            'can' => 'create-godwan-to-shop-ashok'
                        ]
                    ],
                ],

                [
                    'icon' => 'fas fa-warehouse', // Icon for Godown to Shop submenu
                    'text' => 'Shop to Godown',
                    'submenu' => [
                        [
                            'icon' => 'fas fa-truck-loading', // Icon for Create Godown to Shop
                            'text' => 'Shop to Godown (Create)',
                            'url' => 'shopGodown/create',
                            'can'=>'create-shop-to-godwan'
                        ],
                        [
                            'icon' => 'fas fa-eye', // Icon for View Godown to Shop
                            'text' => 'Shop to Godown (View)',
                            'url' => 'shopGodown',
                            'can'=> 'manage-shop-to-godwan'
                        ]
                    ],
                ],

                [
                    'icon' => 'fas fa-store', // Icon for Shop to Godown submenu
                    'text' => 'Shop (Sevices) to Godown',
                    'submenu' => [
                        [
                            'icon' => 'fas fa-arrow-circle-up', // Icon for Create Shop to Godown
                            'text' => 'Shop (Sevices) to Godown (Create)',
                            'url' => 'services/create',
                            'can'=> 'create-shop-service-to-godwan'
                        ],
                        [
                            'icon' => 'fas fa-list-alt', // Icon for View Shop to Godown
                            'text' => 'Shop (Sevices) to Godown (View)',
                            'url' => 'services',
                             'can'=> 'manage-shop-service-to-godwan'
                        ]
                    ],
                ],

                [
                    'icon' => 'fas fa-fw fa-box',
                    'text' => 'Existing Transfers',
                    'url' => 'existingTranfers',
                    'can' =>'manage-existance-transfert'
                ],
            ]
            ],
       // sales
       [
        'text' => 'Sales',
        'icon' => 'fas fa-shopping-cart', // Icon for Sales
        'submenu' => [
            [
                'icon' => 'fas fa-money-bill-wave', // Icon for Cash submenu
                'text' => 'Cash',
                'submenu' => [
                    [
                        'icon' => 'fas fa-cash-register', // Icon for Cash Sales
                        'text' => 'Cash Sales',
                        'url' => 'cash/create',
                        'can' => 'create-cash-sale'
                    ],
                    [
                        'icon' => 'fas fa-receipt', // Icon for Existing Cash Sale
                        'text' => 'Existing Cash Sale',
                        'url' => 'cash',
                           'can' => 'manage-cash-sale'
                    ]
                ],
            ],
            [
                'icon' => 'fas fa-credit-card', // Icon for Credit submenu
                'text' => 'Credit',
                'submenu' => [
                    [
                        'icon' => 'fas fa-hand-holding-usd', // Icon for Credit Sales
                        'text' => 'Credit Sales',
                        'url' => 'credit/create',
                        'can' => 'create-credit-sale'
                    ],
                    [
                        'icon' => 'fas fa-receipt', // Icon for Existing Credit Sale
                        'text' => 'Existing Credit Sale',
                        'url' => 'credit',
                         'can' => 'manage-credit-sale'
                    ]
                ],
            ],
            [
                'icon' => 'fas fa-file-invoice', // Icon for Proforma submenu
                'text' => 'Proforma',
                'submenu' => [
                    [
                        'icon' => 'fas fa-file-export', // Icon for Proforma Sales
                        'text' => 'Proforma Sales',
                        'url' => 'proforma/create',
                        'can' => 'create-proforma'
                    ],
                    [
                        'icon' => 'fas fa-receipt', // Icon for Existing Proforma Sale
                        'text' => 'Existing Proforma Sale',
                        'url' => 'proforma',
                        'can' => 'manage-proforma'
                    ]
                ],
            ],
        ]
    ],


    [
        'text' => 'Dashboard',
        'icon' => 'fas fa-shopping-cart', // Icon for Sales
        'submenu' => [
            [
                'icon' => 'fas fa-money-bill-wave', // Icon for Cash submenu
                'text' => 'Sales Dashboard',
                "url" => 'dashboard'
            ],
            [
                'icon' => 'fas fa-credit-card', // Icon for Credit submenu
                'text' => 'Stock Transfert Dashboard ',
                "url" => 'dashboard-stock-transfert'

            ],

        ]
    ],



    [
        'text' => 'Purchase',
        'icon' => 'fas fa-shopping-cart', // Icon for Purchase
        'submenu' => [
            [
                'text' => 'Local Purchase',
                'icon' => 'fas fa-shopping-cart', // Icon for Local Purchase
                'submenu' => [
                    [
                        'icon' => 'fas fa-money-bill-wave', // Icon for Cash submenu
                        'text' => 'Local Purchase (Create)',
                        'url' => 'purchase/create',
                        'can'=>'create-local-purchase'
                    ],
                    [
                        'icon' => 'fas fa-credit-card', // Icon for Credit submenu
                        'text' => 'Local Purchase (View)',
                        'url' => 'purchase',
                         'can'=>'manage-local-purchase'
                    ],
                ],
            ],
            [
                'text' => 'Imports',
                'icon' => 'fas fa-shopping-cart', // Icon for Imports

                'submenu' => [
                    [
                        'icon' => 'fas fa-money-bill-wave', // Icon for Cash submenu
                        'text' => 'Imports (Create)',
                        'url' => 'imports/create',
                        'can' => 'manage-imports'
                    ],
                    [
                        'icon' => 'fas fa-credit-card', // Icon for Credit submenu
                        'text' => 'Imports (View)',
                        'url' => 'imports',
                        'can' => 'create-imports'
                    ],
                ],
            ],
        ],
    ],




                [
                    'text' => 'Stock Adjustment',
                    'icon' => 'fas fa-warehouse',
                    'submenu' => [
                        [
                            'icon' => 'fas fa-box',
                            'text' => '
                                Stock Adjustment
                                ',
                            'url' => 'adjustments',
                            'can'=> 'manage-adjustments'
                        ],
                        [
                            'icon' => 'fas fa-plus-circle',
                            'text' => '
                                 Adjust Stock
                                ',
                            'url' => 'adjustments/create',
                            'can' => 'create-adjustments'
                        ],
                    ]
                    ],


        [
            'text' => 'Manage Items',
            'icon' => 'fas fa-boxes',
            'submenu' => [
                [
                    'icon' => 'fas fa-fw fa-box',
                    'text' => 'Items',
                    'url' => 'items',
                ],
            ]
        ]
            ,

        [
            'text' => 'Security & Roles',
            'icon' => 'fas fa-shield-alt',
            'submenu' => [
                [
                    'icon' => 'fas fa-fw fa-user-shield',
                    'text' => 'User roles',
                    'url' => 'roles',


                ],
                [
                    'icon' => 'fas fa-fw fa-user-check',
                    'text' => 'Assigned Role',
                    'url' => 'assignedRoles',


                ],
                [
                    'icon' => 'fas fa-fw fa-user-check',
                    'text' => 'Manage Permissions',
                    'url' => 'managePermissions',


                ],
                [
                    'icon' => 'fas fa-fw fa-history',
                    'text' => 'Logs',
                    'url' => 'logs',


                ],
            ]],
        [
            'text' => 'Setting',
            'icon' => 'fas fa-fw fa-cog',
            'submenu' => [
                [
                    'icon' => 'fas fa-fw fa-tags',
                    'text' => 'Item Categories',
                    'url' => 'categories',
                    'can' => 'manage-category'

                ],
                [
                    'icon' => 'fas fa-fw fa-dollar-sign',
                    'text' => 'Currencies',
                    'url' => 'currencies',
                      'can' => 'manage-currency'
                ],
                [
                    'icon' => 'fas fa-fw fa-warehouse',
                    'text' => 'Suppliers',
                    'url' => 'suppliers',
                       'can' => 'manage-supplier'
                ],
                [
                    'icon' => 'fas fa-fw fa-globe',
                    'text' => 'Product Countries',
                    'url' => 'countries',
                         'can' => 'manage-country'
                ],

                [
                    'icon' => 'fas fa-fw fa-address-book',
                    'text' => 'Customers',
                    'url' => 'customers',
     'can' => 'manage-customer'
                ],
                [
                    'icon' => 'fas fa-fw fa-boxes',
                    'text' => 'Stock Types',
                    'url' => 'type',
                    'can' => 'manage-stock-type'
                ],
                [
                    'icon' => 'fas fa-fw fa-calendar year-icon',
                    'text' => 'Years',
                    'url' => 'years',
                     'can' => 'manage-year'
                ],
                [
                    'icon' => 'fas fa-fw fa-ruler',
                    'text' => 'Units',
                    'url' => 'units',
                     'can' => 'manage-unit'
                ],

                [
                    'icon' => 'fas fa-fw fa-calendar-week',
                    'text' => 'Months',
                    'url' => 'months',
                      'can' => 'manage-month'
                ],
                [
                    'icon' => 'fas fa-fw fa-trademark',
                    'text' => 'Brands',
                    'url' => 'brands',
                      'can' => 'manage-brand'
                ],
            ]
        ]


    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    |
    | Here we can modify the menu filters of the admin panel.
    |
    | For detailed instructions you can look the menu filters section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins Initialization
    |--------------------------------------------------------------------------
    |
    | Here we can modify the plugins used inside the admin panel.
    |
    | For detailed instructions you can look the plugins section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Plugins-Configuration
    |
    */

    'plugins' => [
        'Datatables' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css',
                ],
            ],
        ],
        'Select2' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css',
                ],
            ],
        ],
        'Chartjs' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js',
                ],
            ],
        ],
        'Sweetalert2' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.jsdelivr.net/npm/sweetalert2@8',
                ],
            ],
        ],
        'Pace' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | IFrame
    |--------------------------------------------------------------------------
    |
    | Here we change the IFrame mode configuration. Note these changes will
    | only apply to the view that extends and enable the IFrame mode.
    |
    | For detailed instructions you can look the iframe mode section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/IFrame-Mode-Configuration
    |
    */

    'iframe' => [
        'default_tab' => [
            'url' => null,
            'title' => null,
        ],
        'buttons' => [
            'close' => true,
            'close_all' => true,
            'close_all_other' => true,
            'scroll_left' => true,
            'scroll_right' => true,
            'fullscreen' => true,
        ],
        'options' => [
            'loading_screen' => 1000,
            'auto_show_new_tab' => true,
            'use_navbar_items' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Livewire
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Livewire support.
    |
    | For detailed instructions you can look the livewire here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'livewire' => false,
];
