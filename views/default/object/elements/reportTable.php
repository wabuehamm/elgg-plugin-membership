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

$report = elgg_extract('report', $vars, []);

$basicInfoFields = ['displayname', 'name', 'givenname', 'username', 'email'];

$reportProfileFields = elgg_get_plugin_setting("reportProfileFields", "membership", []);

?>
<div class="membershipReport">
    <table>
        <thead>
        <?php
            if (count($participationObjects) > 0) {
        ?>
        <tr>
            <th colspan="<?php echo count(array_merge($basicInfoFields, $reportProfileFields)); ?>"></th>
            <?php
            foreach ($participationObjects as $participationObject) {
                echo '<th colspan="' . count($columns[$participationObject->getGUID()]) . '">' . $participationObject->getDisplayName() . '</th>';
            }
            ?>
        </tr>
        <?php
            }
        ?>
        <tr>
            <?php
            foreach (array_merge($basicInfoFields, $reportProfileFields) as $key) {
                echo '<th>' . elgg_echo('membership:reports:profileFields:' . $key) . '</th>';
            }
            foreach ($participationObjects as $participationObject) {
                foreach ($columns[$participationObject->getGUID()] as $key => $label) {
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
            foreach (array_merge($basicInfoFields, $reportProfileFields) as $key) {
                echo '<td>' . $user_report['_userInfo'][$key] . '</td>';
            }
            foreach ($participationObjects as $participationObject) {
                foreach (array_keys($columns[$participationObject->getGUID()]) as $key) {
                    if (in_array($key, $user_report[$participationObject->getDisplayName()])) {
                        echo '<td style="text-align:center">' . elgg_view_icon('check') . '</td>';
                    } else {
                        echo '<td></td>';
                    }
                }
            }
            echo '</tr>';
        }
        ?>
        </tbody>
    </table>
</div>
