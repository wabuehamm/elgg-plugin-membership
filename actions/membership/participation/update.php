<?php

use Wabue\Membership\Entities\Participation;
use Wabue\Membership\Entities\Production;
use Wabue\Membership\Entities\Season;
use Wabue\Membership\Tools;

$season_guid = get_input('season_guid');

Tools::assert(!is_null($season_guid));

$owner_guid = get_input('owner_guid');

Tools::assert(!is_null($owner_guid));

/** @var ElggUser $owner */
$owner = get_entity($owner_guid);

Tools::assert(!is_null($owner));
Tools::assert($owner instanceof ElggUser);

/** @var Season $season */
$season = get_entity($season_guid);

Tools::assert(!is_null($season));
Tools::assert($season instanceof Season);

$departments = $season->getDepartments();
$departments_participation = $departments->getParticipations($owner_guid);

if (count($departments_participation) == 0) {
    $departments_participation = Participation::factory(
        $owner,
        $season,
        $departments
    );
} else {
    $departments_participation = $departments_participation[0];
}

if (get_input('departments', []) != 0) {
    $departments_participation->setParticipationTypes(get_input('departments', []));
    $departments_participation->save();
}


/** @var Production[] $productions */
$productions = $season->getProductions();

foreach ($productions as $production) {
    $production_participations = $production->getParticipations($owner_guid);
    if (count($production_participations) == 0) {
        $production_participations = Participation::factory(
            $owner,
            $season,
            $production
        );
    } else {
        $production_participations = $production_participations[0];
    }
    if (get_input('production:' . $production->getGUID()) != 0) {
        $production_participations->setParticipationTypes(get_input('production:' . $production->getGUID(), []));
        $production_participations->save();
    }
}

return elgg_ok_response(
    '',
    elgg_echo('membership:participations:saved'),
    elgg_generate_url(
        'view:participations:seasons',
        [
            'guid' => $owner_guid,
        ]
    )
);
