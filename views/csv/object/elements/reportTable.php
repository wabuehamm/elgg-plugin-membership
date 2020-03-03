<?php

/**
 * Renders a membership report as a csv table.
 *
 * The report contains a list of members by username. Each user has a list of participation objects
 * (e.g. the departments or a production) and a special key _userInfo which contains additional informations about
 * the member. The participation object keys contain all participations the member has selected.
 *
 * @uses $vars['participationObjects'] An array of column heads of the reported participation objects
 * @uses $vars['columns'] The columns of the table (as key => displayname)
 * @uses $vars['column_filter'] Only display the column keys in this array
 * @uses $vars['report'] The report
 */

use Wabue\Membership\Entities\ParticipationObject;

/** @var ParticipationObject[] $participationObjects */
$participationObjects = elgg_extract('participationObjects', $vars, []);
$columns = elgg_extract('columns', $vars, []);
$column_filter = elgg_extract('column_filter', $vars, []);

if (count($column_filter) == 0) {
    $column_filter = array_keys($columns);
}

$report = elgg_extract('report', $vars, []);

$reportProfileFields = elgg_get_plugin_setting("reportProfileFields", "membership", []);

$delimiter = ',';
$newline = "\n";

echo str_repeat($delimiter, count(array_merge(['name', 'username', 'email'], $reportProfileFields)) - 1);
foreach ($participationObjects as $participationObject) {
    echo $participationObject->getDisplayName() . str_repeat($delimiter, count($column_filter) - 1);
}
echo $newline;

$header = [];
foreach (array_merge(['name', 'username', 'email'], $reportProfileFields) as $key) {
    array_push($header, elgg_echo('membership:reports:profileFields:' . $key));
}
foreach ($participationObjects as $participationObject) {
    foreach ($columns as $key => $label) {
        if (in_array($key, $column_filter)) {
            array_push($header, $label);
        }
    }
}
echo join($delimiter, $header) . $newline;

foreach ($report as $username => $user_report) {
    $row = [];
    foreach (array_merge(['name', 'username', 'email'], $reportProfileFields) as $key) {
        array_push($row, $user_report['_userInfo'][$key]);
    }
    foreach ($participationObjects as $participationObject) {
        foreach (array_keys($columns) as $key) {
            if (in_array($key, $user_report[$participationObject->getDisplayName()])) {
                array_push($row, 'X');
            } else {
                array_push($row, '');
            }
        }
    }
    echo join(',', $row) . $newline;
}
