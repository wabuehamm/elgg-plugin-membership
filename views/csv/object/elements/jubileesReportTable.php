<?php

/**
 * Renders a jubilees report as a table.
 *
 * @uses $vars['report'] The report
 */

use Wabue\Membership\Tools;

$report = elgg_extract('report', $vars, []);

Tools::assert(
    !is_null($report)
);

if (count($report) == 0) {
    echo elgg_view_message('notice', elgg_echo('membership:jubileesReport:noJubilees'));
    exit;
}

$f = fopen('php://memory', 'r+');
fputcsv(
    $f,
    [
        elgg_echo('membership:reports:profileFields:displayname'),
        elgg_echo('membership:profile:member_since:label'),
        elgg_echo('membership:profile:away_years:label'),
        elgg_echo('membership:profile:active_years:label')
    ]
);
foreach ($report as $displayName => $user_report) {
    fputcsv(
        $f,
        [
            $displayName,
            $user_report['member_since'],
            $user_report['away_years'],
            $user_report['active_years'],
        ]
    );
}

rewind($f);
print(stream_get_contents($f));
