<?php

$acl = \Wabue\Membership\Acl::factory();

$valid_seasons = $acl->getAllowedSeasons(
    elgg_get_logged_in_user_entity()->username
);

if (in_array('*', $valid_seasons)) {
    $valid_seasons = null;
}

$selected_season = get_input('season', null);

/** @var \Wabue\Membership\Entities\Season[] $season_entities */
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

$season = $season_entities[0];

if ($selected_season == null) {
    $selected_season = $season_entities[0]->year;
}

echo '<div class="seasonList">';

foreach ($season_entities as $index => $season_entity) {
    if ($season_entity->year == $selected_season) {
        $season = $season_entity;
        echo $season_entity->year;
    } else {
        echo '<a href="' . elgg_generate_url('default:object:season', ['season' => $season_entity->year]). '">' . $season_entity->year . '</a>';
    }
    if ($index < count($season_entities) - 1) {
        echo ' | ';
    }
}

echo '</div>';

$reports = elgg_view(
    'page/components/membership/reports',
    [
        'entities' => [$season],
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
