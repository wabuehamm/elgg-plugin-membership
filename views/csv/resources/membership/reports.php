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

header('Content-Type: text/csv');

echo elgg_view(
    'page/components/reports/reportPage',
    [
        'participationTypes' => $participationTypes,
        'participationObjects' => $participationObjects
    ]
);
