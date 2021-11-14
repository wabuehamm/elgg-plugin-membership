<?php

/**
 * Renders a simple member report as a table.
 *
 * @uses $vars['columns'] The columns of the table (as key => displayname)
 * @uses $vars['report'] The report
 */

$columns = elgg_extract('columns', $vars, []);
$report = elgg_extract('report', $vars, []);

if (count($report) == 0) {
    echo elgg_view_message('notice', elgg_echo('membership:simpleReport:noData'));
    exit;
}

$f = fopen('php://memory', 'r+');
fputcsv(
    $f,
    $columns
);
foreach ($report as $row) {
    fputcsv(
        $f,
        $row
    );
}

rewind($f);
print(stream_get_contents($f));
