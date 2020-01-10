<?php

use Wabue\Membership\Entities\ParticipationObject;
use Wabue\Membership\Entities\Production;
use Wabue\Membership\Entities\Season;

$season_guid = elgg_extract('season_guid', $vars, null);
assert($season_guid != null);

$season = get_entity($season_guid);
assert($season instanceof Season);

$guid = elgg_extract('guid', $vars, null);

if ($guid != null) {
    $entity = get_entity($guid);
    assert($entity instanceof Production);
}

echo elgg_view_field([
    '#type' => 'hidden',
    'name' => 'guid',
    'value' => $guid ? $guid : '-1',
]);

echo elgg_view_field([
    '#type' => 'hidden',
    'name' => 'season_guid',
    'value' => $season_guid
]);

echo elgg_view_field([
    '#type' => 'text',
    'name' => 'title',
    'value' => $guid ? $entity->title : '',
    '#label' => elgg_echo('membership:production:form:label'),
    '#help' => elgg_echo('membership:production:form:help'),
]);

$participationTypes = ParticipationObject::cleanParticipationSetting($guid ? $departments->getParticipationTypesAsString() : elgg_get_plugin_setting('production_participations', 'membership'));

echo elgg_view_field([
    '#type' => 'longtext',
    '#label' => elgg_echo('membership:production:form:participationtypes:label'),
    '#help' => elgg_echo('membership:production:form:participationtypes:help'),
    'editor' => false,
    'name' => 'participationTypes',
    'required' => true,
    'value' => $participationTypes,
    'disabled' => $guid
]);

$mode = elgg_extract('mode', $vars, 'add');

elgg_set_form_footer(
    elgg_view_field([
        '#type' => 'submit',
        'text' => elgg_echo("membership:season:production:$mode"),
    ])
);
