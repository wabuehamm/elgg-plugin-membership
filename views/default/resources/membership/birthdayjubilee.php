<?php

/**
 * A report of members turning 50, 60, 70, or each five years after 70 (75, 80, etc.)
 */

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

elgg_register_menu_item('title', [
    'name' => 'export_csv',
    'href' => elgg_generate_url(
        'view:birthdayjubileereport',
        [
            'season_guid' => $season_guid,
            'view' => 'csv'
        ]
    ),
    'icon' => 'file-csv',
    'text' => elgg_echo('membership:reports:export:csv'),
    'link_class' => 'elgg-button elgg-button-action event-calendar-button-add',
]);

$body = elgg_view_layout(
    'default',
    [
        'title' => elgg_echo('membership:reports:birthdayjubilees', [
            $season->getYear()
        ]),
        'content' => elgg_view(
            'page/components/reports/birthdayJubileeReportPage',
            [
                'year' => $season->getYear()
            ]
        ),
        'sidebar' => false,
    ]
);

echo elgg_view_page(
    elgg_echo('membership:reports:birthdayjubilees'),
    $body
);
