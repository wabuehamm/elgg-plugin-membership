<?php

/**
 * Show a report of missing members as a CSV export
 */

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

$report_array = [];

elgg_call(ELGG_IGNORE_ACCESS, function () use (&$report_array, $season) {
    $report_array = Tools::generateMissingReport(
        $season
    );
});

$columns = [];

echo elgg_view(
    'object/elements/reportTable',
    [
        'columns' => $columns,
        'report' => $report_array
    ]
);
