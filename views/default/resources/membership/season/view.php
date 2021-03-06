<?php

use Wabue\Membership\Entities\Season;
use Wabue\Membership\Tools;

$season_guid = elgg_extract('guid', $vars, null);

Tools::assert(!is_null($season_guid));

/** @var Season $season */
$season = get_entity($season_guid);

Tools::assert($season instanceof Season);

$department_entity = $season->getDepartments();

Tools::assert(!is_null($department_entity));

$departments = elgg_view_module(
    'info',
    elgg_echo('membership:departments'),
    elgg_view(
        'page/components/membership/participationOverview',
        [
            'entities' => [$department_entity],
        ]
    )
);

$production_entities = $season->getProductions();

$productions = elgg_view_module(
    'info',
    elgg_echo('membership:productions'),
    elgg_view(
        'page/components/membership/participationOverview',
        [
            'entities' => $production_entities,
            'no_entities' => elgg_echo('membership:season:no_productions')
        ]
    )
);

elgg_register_menu_item('title', [
    'name' => 'edit_season',
    'href' => elgg_generate_url('edit:object:season', [
        'guid' => $season_guid
    ]),
    'text' => elgg_echo('membership:season:edit'),
    'link_class' => 'elgg-button elgg-button-action',
]);

elgg_register_menu_item('title', [
    'name' => 'add_production',
    'href' => elgg_generate_url('add:object:production', [
        'container_guid' => $season_guid
    ]),
    'text' => elgg_echo('membership:season:production:add'),
    'link_class' => 'elgg-button elgg-button-action',
]);

elgg_register_menu_item('title', [
    'name' => 'batch',
    'href' => elgg_generate_url('view:season:batch', [
        'container_guid' => $season_guid
    ]),
    'text' => elgg_echo('membership:season:batch'),
    'link_class' => 'elgg-button elgg-button-action',
]);

$body = elgg_view_layout(
    'default',
    [
        'title' => elgg_echo('membership:season:title', [$season->getYear()]),
        'content' => $departments . $productions,
        'sidebar' => false,
    ]
);

echo elgg_view_page(
    elgg_echo('membership:season:title', [$season->getYear()]),
    $body
);
