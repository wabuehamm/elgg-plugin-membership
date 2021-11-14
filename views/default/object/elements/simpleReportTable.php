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

?>
<div class="membershipReport">
    <table>
        <thead>
        <tr>
            <?php
            foreach ($columns as $column) {
                echo "<th>" . $column . "</th>";
            }
            ?>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($report as $row) {
            echo '<tr>';
            foreach ($row as $column) {
                echo "<td>" . $column . "</td>";
            }
            echo '</tr>';
        }
        ?>
        </tbody>
    </table>
</div>
