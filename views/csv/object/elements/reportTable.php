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

$report = elgg_extract('report', $vars, []);

$basicInfoFields = ['displayname', 'name', 'givenname', 'username', 'email'];

$reportProfileFields = elgg_get_plugin_setting("reportProfileFields", "membership", []);

$f = fopen('php://memory', 'r+');

$line = array_fill(0, count(array_merge($basicInfoFields, $reportProfileFields)), '');

foreach ($participationObjects as $participationObject) {
    $line[] = $participationObject->getDisplayName();
    $line = array_merge($line, array_fill(0, count($columns[$participationObject->getGUID()]) - 1, ''));
}

fputcsv($f, $line);

$line = [];
foreach (array_merge($basicInfoFields, $reportProfileFields) as $key) {
    array_push($line, elgg_echo('membership:reports:profileFields:' . $key));
}
foreach ($participationObjects as $participationObject) {
    foreach ($columns[$participationObject->getGUID()] as $key => $label) {
        array_push($line, $label);
    }
}

fputcsv($f, $line);

foreach ($report as $username => $user_report) {
    $line = [];
    foreach (array_merge($basicInfoFields, $reportProfileFields) as $key) {
        array_push($line, $user_report['_userInfo'][$key]);
    }
    foreach ($participationObjects as $participationObject) {
        foreach (array_keys($columns[$participationObject->getGUID()]) as $key) {
            if (in_array($key, $user_report[$participationObject->getDisplayName()])) {
                array_push($line, 'X');
            } else {
                array_push($line, '');
            }
        }
    }
    fputcsv($f, $line);
}

rewind($f);
print(stream_get_contents($f));
