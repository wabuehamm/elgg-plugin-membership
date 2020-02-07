<?php

$season_entities = elgg_get_entities([
    'type' => 'object',
    'subtype' => 'season',
    'order_by_metadata' => [
        'name' => 'year',
        'direction' => 'DESC',
        'as' => 'integer'
    ]
]);

$reports = elgg_view(
    'page/components/membership/reports',
    [
        'entities' => $season_entities,
        'no_entities' => elgg_echo('membership:no_seasons')
    ]
);

echo elgg_view_layout(
    'default',
    [
        'title' => elgg_echo('membership:overview:tabs:reports'),
        'content' => $reports,
        'sidebar' => false,
    ]
);