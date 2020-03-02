<?php

use Wabue\Membership\Entities\Season;
use Wabue\Membership\Tools;

$season_guid = elgg_extract('season_guid', $vars, null);

Tools::assert(
    !is_null($season_guid)
);

/** @var Season $season */
$season = get_entity($season_guid);

Tools::assert(
    !is_null($season_guid),
    'Season not found'
);

Tools::assert(
    $season instanceof Season
);

$participationTypes = preg_split('/,/', elgg_extract('participation_types', $vars, ''));

$departments = $season->getDepartments();

elgg_register_menu_item('title', [
    'name' => 'add',
    'href' => elgg_generate_url(
        'view:report:departments',
        [
            'season_guid' => $season_guid
        ]
    ),
    'icon' => 'file-csv',
    'text' => elgg_echo('membership:reports:export:csv'),
    'link_class' => 'elgg-button elgg-button-action event-calendar-button-add',
]);

$body = elgg_view_layout(
    'default',
    [
        'title' => elgg_echo('membership:reports:departments', [
            $participationTypes[$participationTypes]
        ]),
        'content' => elgg_view(
            'page/components/reports/reportPage',
            [
                'participationTypes' => $participationTypes,
                'participationObjects' => [$departments]
            ]
        ),
        'sidebar' => false,
    ]
);

echo elgg_view_page(
    elgg_echo('membership:reports:departments'),
    $body
);
