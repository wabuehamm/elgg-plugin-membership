<?php

use Wabue\Membership\Entities\ParticipationObject;
use Wabue\Membership\Entities\Season;
use Wabue\Membership\Tools;

$guid = elgg_extract('guid', $vars, null);

if ($guid) {
    $entity = get_entity($guid);
    Tools::assert($entity instanceof Season);
    $departments = $entity->getDepartments();
}

echo elgg_view_field([
    '#type' => 'hidden',
    'name' => 'guid',
    'value' => $guid ? $guid : '-1',
]);

echo elgg_view_field([
        '#type' => 'number',
        '#label' => elgg_echo('membership:season:form:year:label'),
        '#help' => elgg_echo('membership:season:form:year:help'),
        'name' => 'year',
        'required' => true,
        'value' => $guid ? $entity->year : '',
]);

echo elgg_view_field([
        '#type' => 'date',
        '#label' => elgg_echo('membership:season:form:lockdate:label'),
        '#help' => elgg_echo('membership:season:form:lockdate:help'),
        'name' => 'lockdate',
        'timestamp' => true,
        'required' => true,
        'value' => $guid ? $entity->lockdate : '',
]);

echo elgg_view_field([
        '#type' => 'date',
        '#label' => elgg_echo('membership:season:form:enddate:label'),
        '#help' => elgg_echo('membership:season:form:enddate:help'),
        'name' => 'enddate',
        'timestamp' => true,
        'required' => true,
        'value' => $guid ? $entity->enddate : '',
]);

$participationTypes = ParticipationObject::cleanParticipationSetting($guid ? $departments->getParticipationTypesAsString() : elgg_get_plugin_setting('departments_participations', 'membership'));

echo elgg_view_field([
    '#type' => 'longtext',
    '#label' => elgg_echo('membership:season:form:participationtypes:label'),
    '#help' => elgg_echo('membership:season:form:participationtypes:help'),
    'editor' => false,
    'name' => $guid ? 'participationTypesReadOnly' : 'participationTypes',
    'required' => true,
    'value' => $participationTypes,
    'disabled' => $guid
]);

if ($guid) {
    echo elgg_view_field([
        '#type' => 'hidden',
        'name' => 'participationTypes',
        'value' => $participationTypes,
    ]);
}

$mode = elgg_extract('mode', $vars, 'add');

elgg_set_form_footer(
    elgg_view_field([
        '#type' => 'submit',
        'text' => elgg_echo("membership:season:$mode"),
    ])
);
