<?php

/**
 * Show a jubilee report as a tabular page
 *
 * @uses $vars['year'] the year to calculate the jubilees for
 */

use Wabue\Membership\Entities\ParticipationObject;
use Wabue\Membership\Tools;

$year = elgg_extract('year', $vars, null);

Tools::assert(
    !is_null($year)
);

$report_array = Tools::generateJubileesReport(
    $year
);


echo elgg_view(
    'object/elements/jubileesReportTable',
    [
        'report' => $report_array
    ]
);
