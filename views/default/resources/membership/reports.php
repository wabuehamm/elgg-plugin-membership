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

/** @var string[] $participationObjectGuids */
$participationObjectGuidsVar = elgg_extract('participation_object_guids', $vars, null);

if (!is_null($participationObjectGuidsVar)) {
    $participationObjectGuids = preg_split('/,/', $participationObjectGuidsVar);
} else {
    $participationObjectGuids = [];
    array_push($participationObjectGuids, $season->getDepartments()->getGUID());
    foreach ($season->getProductions() as $production) {
        array_push($participationObjectGuids, $production->getGUID());
    }
}

$participationObjects = [];

foreach ($participationObjectGuids as $participationObjectGuid) {

    Tools::assert(
        is_numeric($participationObjectGuid)
    );

    if ($participationObjectGuid == 0) {
        $participationObject = $season->getDepartments();
    } else {
        $participationObject = get_entity($participationObjectGuid);
    }

    Tools::assert(
        $participationObject instanceof \Wabue\Membership\Entities\ParticipationObject
    );

    Tools::assert(
        $participationObject->getContainerGUID() == $season_guid
    );

    array_push($participationObjects, $participationObject);
}

$participationTypesVar = elgg_extract('participation_types', $vars, null);
$participationTypes = [];

if ($participationTypesVar) {
    $participationTypes = preg_split('/,/', $participationTypesVar);
}


elgg_register_menu_item('title', [
    'name' => 'export_csv',
    'href' => elgg_generate_url(
        'view:report',
        [
            'season_guid' => $season_guid
        ]
    ),
    'icon' => 'file-csv',
    'text' => elgg_echo('membership:reports:export:csv'),
    'link_class' => 'elgg-button elgg-button-action event-calendar-button-add',
]);

$body = elgg_view_layout(
    'default',
    [
        'title' => elgg_echo('membership:reports:title', [
            $participationTypes[$participationTypes]
        ]),
        'content' => elgg_view(
            'page/components/reports/reportPage',
            [
                'participationTypes' => $participationTypes,
                'participationObjects' => $participationObjects
            ]
        ),
        'sidebar' => false,
    ]
);

echo elgg_view_page(
    elgg_echo('membership:reports:title'),
    $body
);
