<?php

/**
 * ELGG plugin descriptor.
 *
 * Please check out http://learn.elgg.org/en/stable/guides/plugins.html#elgg-plugin-php for details
 */

use Elgg\Router\Middleware\AdminGatekeeper;
use Elgg\Router\Middleware\UserPageOwnerCanEditGatekeeper;
use Wabue\Membership\Entities\Departments;

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
                AdminGatekeeper::class,
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
            'path' => '/membership/season/view/{guid}',
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
        'report:object:departments' => [
            'path' => '/membership/reports/season/{season_guid}/departments/{participation_types}',
            'resource' => 'membership/reports/departments',
            'middleware' => [
                AdminGatekeeper::class
            ]
        ],
        'report:object:production' => [
            'path' => '/membership/reports/season/{season_guid}/production/{production_guid}/{participation_types}',
            'resource' => 'membership/reports/production',
            'default' => [
                'production_guid' => 0,
            ],
            'middleware' => [
                AdminGatekeeper::class
            ]
        ]
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
        ",
        'reportProfileFields' => [
            "street",
            "zip",
            "city",
            "telephone",
            "mobile"
        ]
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
