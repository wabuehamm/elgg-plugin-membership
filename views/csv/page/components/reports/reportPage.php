<?php

/**
 * Show a report as a tabular page
 *
 * @uses $vars['participationTypes'] a list of participation types to report on
 * @uses $vars['participationObjects'] a list of participation objects to report on
 */

use Wabue\Membership\Entities\ParticipationObject;
use Wabue\Membership\Tools;

$participationTypes = elgg_extract('participationTypes', $vars, []);

/** @var ParticipationObject[] $participationObjects */
$participationObjects = elgg_extract('participationObjects', $vars, null);

Tools::assert(
    !is_null($participationObjects)
);

$columnHeaders = [];

foreach ($participationObjects as $participationObject) {
    Tools::assert(
        $participationObject instanceof ParticipationObject
    );
    foreach($participationObject->getParticipationTypes() as $key => $label) {
        if (count($participationTypes) == 0 || in_array($key, $participationTypes)) {
            if (!array_key_exists($key, $columnHeaders)) {
                $columnHeaders[$key] = $label;
            }
        }
    }
}

$report_array = Tools::generateReport(
    $participationObjects,
    $participationTypes
);

echo elgg_view(
    'object/elements/reportTable',
    [
        'participationObjects' => $participationObjects,
        'columns' => $columnHeaders,
        'column_filter' => $participationTypes,
        'report' => $report_array
    ]
);
