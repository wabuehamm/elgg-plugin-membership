<?php

/**
 * Show a specialized report for insurance requests
 *
 * @uses $vars['season'] the season to calculate the insurance for
 */

use Wabue\Membership\Entities\ParticipationObject;
use Wabue\Membership\Tools;

$season = elgg_extract('season', $vars, null);

Tools::assert(
    !is_null($season)
);

$report_array = Tools::generateInsuranceReport(
    $season
);

echo elgg_view(
    'object/elements/insuranceReport',
    [
        'report' => $report_array
    ]
);
