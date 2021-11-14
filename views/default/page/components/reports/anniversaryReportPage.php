<?php

/**
 * Show an anniversary report as a tabular page
 *
 * @uses $vars['year'] the year to calculate the anniversary for
 */

use Wabue\Membership\Entities\ParticipationObject;
use Wabue\Membership\Tools;

$year = elgg_extract('year', $vars, null);

Tools::assert(
    !is_null($year)
);

$report_array = [];

elgg_call(ELGG_IGNORE_ACCESS, function () use (&$report_array, $year) {
    $report_array = Tools::generateAnniversaryReport(
        $year
    );
});

echo elgg_view(
    'object/elements/anniversaryReportTable',
    [
        'report' => $report_array
    ]
);
