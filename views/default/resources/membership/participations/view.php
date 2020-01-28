<?php

use Wabue\Membership\Tools;

$owner_guid = elgg_extract('guid', $vars, null);

Tools::assert(!is_null($owner_guid));

$season_entities = elgg_get_entities([
    'type' => 'object',
    'subtype' => 'season',
    'order_by_metadata' => [
        'name' => 'year',
        'direction' => 'DESC',
        'as' => 'integer'
    ]
]);

$seasons = elgg_view(
    'page/components/membership/seasons',
    [
        'entities' => $season_entities,
        'no_entities' => elgg_echo('membership:no_seasons')
    ]
);

$body = elgg_view_layout(
    'default',
    [
        'title' => elgg_echo('membership:participations:title'),
        'content' => $seasons,
        'sidebar' => false,
    ]
);

echo elgg_view_page(
    elgg_echo('membership:participations:title'),
    $body
);