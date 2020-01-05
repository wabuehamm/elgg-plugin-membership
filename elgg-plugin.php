<?php

/**
 * ELGG plugin descriptor.
 * 
 * Please check out http://learn.elgg.org/en/stable/guides/plugins.html#elgg-plugin-php for details
 */

return [
    'bootstrap' =>  Wabue\Membership\Bootstrap::class,
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
            'class' => \Wabue\Membership\Entities\Departments::class
        ]
    ],
    'actions' => [
        'membership/season/update' => [
            'access' => 'admin',
        ]
    ],
    'routes' => [
        'default:object:season' => [
            'path' => '/season/list',
            'resource' => 'membership/season/list',
            'middleware' => [
                \Elgg\Router\Middleware\AdminGatekeeper::class,
            ],
        ],
        'add:object:season' => [
            'path' => '/season/add',
            'resource' => 'membership/season/update',
            'middleware' => [
                \Elgg\Router\Middleware\AdminGatekeeper::class,
            ],
            'defaults' => [
                'mode' => 'add',
            ]
        ],
        'add:object:production' => [
            'path' => '/season/{season_guid}/production/add',
            'resource' => 'membership/season/production/update',
            'middleware' => [
                \Elgg\Router\Middleware\AdminGatekeeper::class,
            ],
            'defaults' => [
                'mode' => 'add',
            ]
        ],
        'view:object:season' => [
            'path' => '/season/view/{guid}',
            'resource' => 'membership/season/view',
            'middleware' => [
                \Elgg\Router\Middleware\AdminGatekeeper::class
            ]
        ]
    ],
    'widgets' => [
        // Register custom widgets here
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
