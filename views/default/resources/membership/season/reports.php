<?php

$acl = \Wabue\Membership\Acl::factory();

$valid_seasons = $acl->getAllowedSeasons(
    elgg_get_logged_in_user_entity()->username
);

if (in_array('*', $valid_seasons)) {
    $valid_seasons = null;
}

$season_entities = elgg_get_entities([
    'type' => 'object',
    'subtype' => 'season',
    'guids' => $valid_seasons,
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
