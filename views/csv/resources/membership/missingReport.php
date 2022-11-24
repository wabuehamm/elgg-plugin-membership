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

header('Content-Type: text/csv');

echo elgg_view(
    'page/components/reports/missingReportPage',
    [
        'season_guid' => $season_guid,
    ]
);
