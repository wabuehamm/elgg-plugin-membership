<?php

/**
 * ELGG plugin descriptor.
 *
 * Please check out http://learn.elgg.org/en/stable/guides/plugins.html#elgg-plugin-php for details
 */

use Elgg\Router\Middleware\AdminGatekeeper;
use Elgg\Router\Middleware\Gatekeeper;
use Elgg\Router\Middleware\UserPageOwnerCanEditGatekeeper;
use Wabue\Membership\Entities\Departments;
use Wabue\Membership\ReportGatekeeper;

return [
    'bootstrap' => Wabue\Membership\Bootstrap::class,
    'entities' => [
        [
            'type' => 'object',
            'subtype' => 'season',
            'class' => Wabue\Membership\Entities\Season::class
        ],
        [
            'type' => 'object',
            'subtype' => 'participation',
            'class' => Wabue\Membership\Entities\Participation::class
        ],
        [
            'type' => 'object',
            'subtype' => 'production',
            'class' => Wabue\Membership\Entities\Production::class
        ],
        [
            'type' => 'object',
            'subtype' => 'departments',
            'class' => Departments::class
        ]
    ],
    'actions' => [
        'membership/season/update' => [
            'access' => 'admin',
        ],
        'membership/season/batch' => [
            'access' => 'admin'
        ],
        'membership/season/production/update' => [
            'access' => 'admin',
        ],
        'membership/participation/update' => [
            'access' => 'logged_in'
        ]
    ],
    'routes' => [
        'default:object:season' => [
            'path' => '/membership',
            'resource' => 'membership/season/overview',
            'middleware' => [
                ReportGatekeeper::class,
            ],
        ],
        'add:object:season' => [
            'path' => '/membership/season/add',
            'resource' => 'membership/season/update',
            'middleware' => [
                AdminGatekeeper::class,
            ],
            'defaults' => [
                'mode' => 'add',
            ]
        ],
        'edit:object:season' => [
            'path' => '/membership/season/{guid}/edit',
            'resource' => 'membership/season/update',
            'middleware' => [
                AdminGatekeeper::class,
            ],
            'defaults' => [
                'mode' => 'edit',
            ]
        ],
        'add:object:production' => [
            'path' => '/membership/season/{container_guid}/production/add',
            'resource' => 'membership/season/production/update',
            'middleware' => [
                AdminGatekeeper::class,
            ],
            'defaults' => [
                'mode' => 'add',
            ]
        ],
        'view:object:season' => [
            'path' => '/membership/season/{guid}',
            'resource' => 'membership/season/view',
            'middleware' => [
                AdminGatekeeper::class
            ]
        ],
        'view:participations:seasons' => [
            'path' => '/membership/participations/{guid}',
            'resource' => 'membership/participations/view',
            'middleware' => [
                UserPageOwnerCanEditGatekeeper::class
            ],
        ],
        'edit:participations:seasons' => [
            'path' => '/membership/participations/{guid}/{season_guid}',
            'resource' => 'membership/participations/update',
            'middleware' => [
                UserPageOwnerCanEditGatekeeper::class
            ],
        ],
        'view:report' => [
            'path' => '/membership/reports/season/{season_guid}/{participation_object_guids}/{participation_types}',
            'resource' => 'membership/reports',
            'middleware' => [
                Gatekeeper::class,
                ReportGatekeeper::class
            ],
            'defaults' => [
                'participation_object_guids' => '',
                'participation_types' => '',
            ]
        ],
        'view:missingreport' => [
            'path' => '/membership/reports/missing/{season_guid}',
            'resource' => 'membership/missingReport',
            'middleware' => [
                Gatekeeper::class,
                ReportGatekeeper::class
            ],
        ],
        'view:jubileereport' => [
            'path' => '/membership/reports/jubilees/{season_guid}',
            'resource' => 'membership/jubilees',
            'middleware' => [
                Gatekeeper::class,
                ReportGatekeeper::class
            ]
        ],
        'view:anniversaryreport' => [
            'path' => '/membership/reports/anniversary/{season_guid}',
            'resource' => 'membership/anniversary',
            'middleware' => [
                Gatekeeper::class,
                ReportGatekeeper::class
            ]
        ],
        'view:insurancereport' => [
            'path' => '/membership/reports/insurance/{season_guid}',
            'resource' => 'membership/insurance',
            'middleware' => [
                Gatekeeper::class,
                ReportGatekeeper::class
            ]
        ],
        'view:season:batch' => [
            'path' => '/membership/season/{container_guid}/batch',
            'resource' => 'membership/season/batch',
            'middleware' => [
                AdminGatekeeper::class
            ]
        ],
        'view:user:membercard' => [
            'path' => '/membership/membercard/{username}',
            'resource' => 'membership/membercard',
            'walled' => false,
        ],
        'view:youngreport' => [
            'path' => '/membership/reports/young/{season_guid}',
            'resource' => 'membership/young',
            'middleware' => [
                Gatekeeper::class,
                ReportGatekeeper::class
            ]
        ],
        'view:adultreport' => [
            'path' => '/membership/reports/adult/{season_guid}',
            'resource' => 'membership/adult',
            'middleware' => [
                Gatekeeper::class,
                ReportGatekeeper::class
            ]
        ],
        'view:birthdayjubileereport' => [
            'path' => '/membership/reports/birthdayjubilee/{season_guid}',
            'resource' => 'membership/birthdayjubilee',
            'middleware' => [
                Gatekeeper::class,
                ReportGatekeeper::class
            ]
        ],
    ],
    'widgets' => [
        // Register custom widgets here
    ],
    'settings' => [
        'departments_participations' => "
            mk:Marketing
            gp:Geländepflege
            bb:Bühnenbau
            ng:Nähgruppe
            fu:Fundus
            ko:Kostüme
            rq:Requisite
            vs:Vorstand
            bj:Babyjahr
            sj:Sabbatjahr
            so:Sonstiges
        ",
        'production_participations' => "
            ra:Regie/Assistenz
            sp:Spieler
            ms:Maske
            tk:Technik
            py:Pyrotechnik
            ks:Kasse
            or:Ordner
            vk:Verkaufsbuden
            kk:Kaffeeküche
            th:Theke Studio
            kü:Küche Studio
            so:Sonstigesx
        ",
        'reportProfileFields' => [
            "street",
            "zip",
            "city",
            "telephone",
            "mobile",
            "birthday"
        ],
        'lockBlocklist' => "admin\nroot\nadministrator"
    ],
    'user_settings' => [
        // Register user settings for your plugin here
    ],
    'views' => [
        // Alias third party vendor paths here
    ],
    'hooks' => [
        // Register plugin hooks here (http://learn.elgg.org/en/stable/guides/hooks-list.html)
    ],
    'events' => [
        // Register event handlers here (http://learn.elgg.org/en/stable/guides/events-list.html)
    ]
];
