<?php

/**
 * Renders an insurance report.
 *
 * @uses $vars['report'] The report
 */

use Wabue\Membership\Tools;

$report = elgg_extract('report', $vars, []);

$member = elgg_get_plugin_setting('insuranceMember', 'membership');

Tools::assert(
    !is_null($report)
);

?>
<h1><?php print elgg_echo('membership:reports:insurance:title'); ?></h1>
<p><?php
    print elgg_echo('membership:reports:insurance:date', [
        elgg_view('output/date', ['value' => time()])
    ]);
?></p>
<hr />
<p>
    <?php
        print preg_replace(
            "/\n/",
            '<br />',
            elgg_get_plugin_setting('insuranceMember', 'membership')
        );
    ?>
</p>
<hr />
<p>
    <?php
    print preg_replace(
        "/\n/",
        '<br />',
        elgg_get_plugin_setting('insuranceAddress', 'membership')
    );
    ?>
</p>
<div class="membershipReport">
    <table>
        <thead>
        <tr>
            <th>
                <?php print elgg_echo('membership:reports:insurance:year') ?>
            </th>
            <th>
                <?php print elgg_echo('membership:reports:insurance:common') ?>
            </th>
            <th>
                <?php print elgg_echo('membership:reports:insurance:teens') ?>
            </th>
            <th>
                <?php print elgg_echo('membership:reports:insurance:board') ?>
            </th>
            <th>
                <?php print elgg_echo('membership:reports:insurance:total') ?>
            </th>
        </tr>
        </thead>
        <tbody>
        <?php
        $theatre = elgg_get_plugin_setting('insuranceTheatre', 'membership');
        foreach ($report as $year => $insurance_report) {
            echo '<tr>';
            echo "<td>${theatre} ${year}</td>";
            echo "<td>${insurance_report['common']}</td>";
            echo "<td>${insurance_report['teens']}</td>";
            echo "<td>${insurance_report['board']}</td>";
            echo "<td>${insurance_report['total']}</td>";
            echo '</tr>';
        }
        ?>
        </tbody>
    </table>
</div>
