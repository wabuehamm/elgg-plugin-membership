<?php

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

$body = elgg_view_layout(
    'default',
    [
        'title' => elgg_echo('membership:reports:insurance', [
            $season->getYear()
        ]),
        'content' => elgg_view(
            'page/components/reports/insuranceReportPage',
            [
                'season' => $season
            ]
        ),
        'sidebar' => false,
    ]
);

echo elgg_view_page(
    elgg_echo('membership:reports:insurance'),
    $body
);
