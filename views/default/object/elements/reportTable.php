<?php

/**
 * Renders a membership report as a table.
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
$report = elgg_extract('report', $vars, []);

$reportProfileFields = elgg_get_plugin_setting("reportProfileFields", "membership", []);

?>
<table class="membershipReport">
    <thead>
    <tr>
        <th colspan="<?php echo count(array_merge(['name', 'username', 'email'], $reportProfileFields)); ?>"></th>
        <?php
        foreach ($participationObjects as $participationObject) {
            echo '<th colspan="' . count($column_filter) . '">' . $participationObject->getDisplayName() . '</th>';
        }
        ?>
    </tr>
    <tr>
        <?php
        foreach (array_merge(['name', 'username', 'email'], $reportProfileFields) as $key) {
            echo '<th>' . elgg_echo('membership:reports:profileFields:'.$key) . '</th>';
        }
        foreach ($columns as $key => $label) {
            if (in_array($key, $column_filter)) {
                echo "<th>$label</th>";
            }
        }
        ?>
    </tr>
    </thead>
    <tbody>
    <?php
        foreach ($report as $username => $user_report) {
            echo '<tr>';
            foreach (array_merge(['name', 'username', 'email'], $reportProfileFields) as $key) {
                echo '<td>' . $user_report['_userInfo'][$key] . '</td>';
            }
            foreach ($participationObjects as $participationObject) {
                foreach (array_keys($columns) as $key) {
                    if (in_array($key, $user_report[$participationObject->getDisplayName()])) {
                        echo '<td style="text-align:center">'.elgg_view_icon('check').'</td>';
                    }
                }
            }
            echo '</tr>';
        }
    ?>
    </tbody>
</table>
