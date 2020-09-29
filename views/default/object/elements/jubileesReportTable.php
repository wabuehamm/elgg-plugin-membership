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

?>
<div class="membershipReport">
    <table>
        <thead>
        <tr>
            <th>
                <?php print elgg_echo('membership:reports:profileFields:displayname') ?>
            </th>
            <th>
                <?php print elgg_echo('membership:profile:member_since:label') ?>
            </th>
            <th>
                <?php print elgg_echo('membership:profile:away_years:label') ?>
            </th>
            <th>
                <?php print elgg_echo('membership:profile:active_years:label') ?>
            </th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($report as $displayName => $user_report) {
            echo '<tr>';
            echo "<td>${displayName}</td>";
            foreach (['member_since', 'away_years', 'active_years'] as $key) {
                echo "<td>${user_report[$key]}</td>";
            }
            echo '</tr>';
        }
        ?>
        </tbody>
    </table>
</div>
