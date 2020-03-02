<?php

use Wabue\Membership\Entities\Departments;
use Wabue\Membership\Entities\Participation;
use Wabue\Membership\Entities\Production;
use Wabue\Membership\Entities\Season;
use Wabue\Membership\Tools;

/** @var Season $season */
$season = elgg_extract('season', $vars, null);

Tools::assert(!is_null($season));
Tools::assert($season instanceof Season);

echo elgg_view_field([
    '#type' => 'hidden',
    'name' => 'season_guid',
    'value' => $season->getGUID(),
]);

$owner_guid = elgg_extract('owner_guid', $vars, null);

Tools::assert(!is_null($owner_guid));

echo elgg_view_field([
    '#type' => 'hidden',
    'name' => 'owner_guid',
    'value' => $owner_guid
]);

$content = '';

/** @var Departments $departments */
$departments = $season->getDepartments();

Tools::assert(!is_null($departments));
Tools::assert($departments instanceof Departments);

/** @var Participation $departments_participations */
$departments_participations = $departments->getParticipations($owner_guid)[0];

$content .= elgg_view_module(
    'info',
    elgg_echo('membership:departments'),
    Tools::participationUpdate(
        "departments",
        array_flip($departments->getParticipationTypes()),
        $departments_participations ? $departments_participations->getParticipationTypes() : []
    )
);

/** @var Production[] $productions */
$productions = $season->getProductions();

$productions_content = '';

foreach ($productions as $production) {
    Tools::assert($production instanceof Production);

    /** @var Participation $production_participations */
    $production_participations = $production->getParticipations($owner_guid)[0];

    $productions_content .= elgg_format_element(
        'h3',
        [],
        $production->getDisplayName()
    );
    $productions_content .= Tools::participationUpdate(
        "production:$production->guid",
        array_flip($production->getParticipationTypes()),
        $production_participations ? $production_participations->getParticipationTypes() : []
    );
}

$content .= elgg_view_module(
    'info',
    elgg_echo('membership:productions'),
    $productions_content
);

elgg_set_form_footer(
    elgg_view_field([
        '#type' => 'submit',
        'text' => elgg_echo("membership:participations:participate"),
    ])
);

echo $content;
