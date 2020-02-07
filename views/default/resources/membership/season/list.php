<?php

$seasons = elgg_list_entities([
    'type' => 'object',
    'subtype' => 'season',
    'show_social_menu' => false,
    'order_by_metadata' => [
        'name' => 'year',
        'direction' => 'DESC',
        'as' => 'integer'
    ]
]);

elgg_register_menu_item('title', [
    'name' => 'add',
    'href' => elgg_generate_url('add:object:season'),
    'text' => elgg_echo('membership:season:add'),
    'link_class' => 'elgg-button elgg-button-action event-calendar-button-add',
]);

echo elgg_view_layout(
    'default',
    [
        'title' => elgg_echo('membership:overview:tabs:seasons'),
        'content' => $seasons,
        'sidebar' => false,
    ]
);
