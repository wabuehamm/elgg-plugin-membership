<?php

use Wabue\Membership\Entities\Season;
use Wabue\Membership\Tools;

elgg_register_menu_item('title', [
    'name' => 'export_csv',
    'href' => elgg_generate_url(
        'view:youngreport',
        [
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
        'title' => elgg_echo('membership:reports:young'),
        'content' => elgg_view(
            'page/components/reports/youngReportPage'
        ),
        'sidebar' => false,
    ]
);

echo elgg_view_page(
    elgg_echo('membership:reports:young'),
    $body
);
