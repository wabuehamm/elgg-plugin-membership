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

$report_array = [];

elgg_call(ELGG_IGNORE_ACCESS, function () use (&$report_array, $participationTypes, $participationObjects) {
    $report_array = Tools::generateReport(
        $participationObjects,
        $participationTypes
    );
});

$columns = [];

foreach ($participationObjects as $participationObject) {
    Tools::assert($participationObject instanceof ParticipationObject);
    $columns[$participationObject->getGUID()] = [];
    foreach ($participationObject->getParticipationTypes() as $key => $label) {
        if (count($participationTypes) == 0 || in_array($key, $participationTypes)) {
            $columns[$participationObject->getGUID()][$key] = $label;
        }
    }
}

echo elgg_view(
    'object/elements/reportTable',
    [
        'participationObjects' => $participationObjects,
        'columns' => $columns,
        'report' => $report_array
    ]
);
