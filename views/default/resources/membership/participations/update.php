<?php

use Wabue\Membership\Entities\Departments;
use Wabue\Membership\Entities\Season;
use Wabue\Membership\Tools;

$owner_guid = elgg_extract('guid', $vars, null);

Tools::assert(!is_null($owner_guid));

$season_guid = elgg_extract('season_guid', $vars, null);

Tools::assert(!is_null($season_guid));

/** @var $season Season */
$season = get_entity($season_guid);

Tools::assert(!is_null($season));
Tools::assert($season instanceof Season);

$content = '';

$content = elgg_view_form(
    'membership/participation/update',
    [],
    [
        'season' => $season,
    ]
);

$body = elgg_view_layout(
    'default',
    [
        'title' => elgg_echo('membership:participations:season:title', [$season->getYear()]),
        'content' => $content,
        'sidebar' => false,
    ]
);

echo elgg_view_page(
    elgg_echo('membership:participations:season:title', [$season->getYear()]),
    $body
);