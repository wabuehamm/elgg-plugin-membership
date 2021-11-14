<?php

/**
 * Show a report as a tabular page
 */

use Wabue\Membership\Entities\ParticipationObject;
use Wabue\Membership\Tools;

$report_array = [];

elgg_call(ELGG_IGNORE_ACCESS, function () use (&$report_array) {
    $report_array = Tools::generateAdultMembersReport();
});

echo elgg_view(
    'object/elements/simpleReportTable',
    [
        'columns' => [
            elgg_echo('membership:reports:profileFields:displayname'),
            elgg_echo('membership:reports:profileFields:street'),
            elgg_echo('membership:reports:profileFields:zip'),
            elgg_echo('membership:reports:profileFields:city'),
            elgg_echo('membership:reports:profileFields:telephone'),
            elgg_echo('membership:reports:profileFields:mobile'),
            elgg_echo('membership:reports:profileFields:email'),
            elgg_echo('membership:reports:profileFields:birthday'),
            elgg_echo('membership:reports:age'),
        ],
        'report' => $report_array
    ]
);
