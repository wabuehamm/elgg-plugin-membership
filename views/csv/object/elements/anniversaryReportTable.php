<?php

/**
 * Renders an anniversary report as a table.
 *
 * @uses $vars['report'] The report
 */

use Wabue\Membership\Tools;

$report = elgg_extract('report', $vars, []);

Tools::assert(
    !is_null($report)
);

if (count($report) == 0) {
    echo elgg_view_message('notice', elgg_echo('membership:anniversaryReport:noAnniversaries'));
    exit;
}

$f = fopen('php://memory', 'r+');
fputcsv(
    $f,
    [
        elgg_echo('membership:reports:profileFields:displayname'),
        elgg_echo('membership:reports:profileFields:years'),
        elgg_echo('membership:reports:profileFields:anniversary'),
    ]
);
foreach ($report as $displayName => $user_report) {
    fputcsv(
        $f,
        [
            $displayName,
            $user_report['years'],
            $user_report['anniversary'],
        ]
    );
}

rewind($f);
print(stream_get_contents($f));
