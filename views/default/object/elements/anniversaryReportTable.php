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

?>
<div class="membershipReport">
    <table>
        <thead>
        <tr>
            <th>
                <?php print elgg_echo('membership:reports:profileFields:displayname') ?>
            </th>
            <th>
                <?php print elgg_echo('membership:reports:profileFields:years') ?>
            </th>
            <th>
                <?php print elgg_echo('membership:reports:profileFields:anniversary') ?>
            </th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($report as $displayName => $user_report) {
            echo '<tr>';
            echo "<td>${displayName}</td>";
            echo "<td>${user_report['years']}</td>";
            echo "<td>${user_report['anniversary']}</td>";
            echo '</tr>';
        }
        ?>
        </tbody>
    </table>
</div>
