<?php

use Wabue\Membership\Entities\Season;
use Wabue\Membership\Tools;

$season_guid = elgg_extract('container_guid', $vars, null);

Tools::assert(!is_null($season_guid));

/** @var Season $season */
$season = get_entity($season_guid);

Tools::assert($season instanceof Season);

$form = elgg_view_form(
    'membership/season/batch',
    [],
    [
        'season' => $season
    ]
);

elgg_require_js('membership/batch');

$body = elgg_view_layout(
    'default',
    [
        'title' => elgg_echo('membership:season:batch:title', [$season->getYear()]),
        'content' => $form,
        'sidebar' => false,
    ]
);

echo elgg_view_page(
    elgg_echo('membership:season:batch:title', [$season->getYear()]),
    $body
);
