<?php

use Wabue\Membership\Entities\Season;
use Wabue\Membership\Tools;

$season_guid = elgg_extract('season_guid', $vars, null);

Tools::assert(
    !is_null($season_guid)
);

/** @var Season $season */
$season = get_entity($season_guid);

Tools::assert(
    !is_null($season_guid),
    'Season not found'
);

Tools::assert(
    $season instanceof Season
);

elgg_call(ELGG_IGNORE_ACCESS, function () use ($season, &$report_array) {
    $report_array = Tools::generateBirthdayJubileeReport($season->getYear());
});

$columns = [
    elgg_echo('membership:reports:profileFields:displayname'),
    elgg_echo('membership:reports:profileFields:street'),
    elgg_echo('membership:reports:profileFields:zip'),
    elgg_echo('membership:reports:profileFields:city'),
    elgg_echo('membership:reports:profileFields:telephone'),
    elgg_echo('membership:reports:profileFields:mobile'),
    elgg_echo('membership:reports:profileFields:email'),
    elgg_echo('membership:reports:profileFields:birthday'),
    elgg_echo('membership:reports:age'),
];

header('Content-Type: text/csv');

echo elgg_view(
    'object/elements/simpleReportTable',
    [
        'columns' => [
            elgg_echo('membership:reports:profileFields:displayname'),
            elgg_echo('membership:reports:profileFields:street'),
            elgg_echo('membership:reports:profileFields:zip'),
            elgg_echo('membership:reports:profileFields:city'),
            elgg_echo('membership:reports:profileFields:telephone'),
            elgg_echo('membership:reports:profileFields:mobile'),
            elgg_echo('membership:reports:profileFields:email'),
            elgg_echo('membership:reports:profileFields:birthday'),
            elgg_echo('membership:reports:age'),
        ],
        'report' => $report_array
    ]
);
