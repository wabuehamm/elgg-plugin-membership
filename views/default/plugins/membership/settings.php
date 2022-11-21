<?php

use Wabue\Membership\Entities\ParticipationObject;

$departmentsParticipations = ParticipationObject::cleanParticipationSetting(elgg_get_plugin_setting('departments_participations', 'membership'));
$productionParticipations = ParticipationObject::cleanParticipationSetting(elgg_get_plugin_setting('production_participations', 'membership'));
$acl = elgg_get_plugin_setting('acl', 'membership');
$insuranceAddress = elgg_get_plugin_setting('insuranceAddress', 'membership');
$insuranceMember = elgg_get_plugin_setting('insuranceMember', 'membership');
$insuranceTheatre = elgg_get_plugin_setting('insuranceTheatre', 'membership');
$lockBlocklist = elgg_get_plugin_setting('lockBlocklist', 'membership');
$fields = [
    [
        '#type' => 'longtext',
        '#label' => elgg_echo('membership:settings:departments:participations:label'),
        '#help' => elgg_echo('membership:settings:departments:participations:help'),
        'editor' => false,
        'name' => 'params[departments_participations]',
        'value' => $departmentsParticipations,
    ],
    [
        '#type' => 'longtext',
        '#label' => elgg_echo('membership:settings:production:participations:label'),
        '#help' => elgg_echo('membership:settings:production:participations:help'),
        'editor' => false,
        'name' => 'params[production_participations]',
        'value' => $productionParticipations,
    ],
    [
        '#type' => 'longtext',
        '#label' => elgg_echo('membership:settings:acl:label'),
        '#help' => elgg_echo('membership:settings:acl:help'),
        'editor' => false,
        'name' => 'params[acl]',
        'value' => $acl,
    ],
    [
        '#type' => 'text',
        '#label' => elgg_echo('membership:settings:insuranceTheatre:label'),
        '#help' => elgg_echo('membership:settings:insuranceTheatre:help'),
        'editor' => false,
        'name' => 'params[insuranceTheatre]',
        'value' => $insuranceTheatre
    ],    [
        '#type' => 'longtext',
        '#label' => elgg_echo('membership:settings:insuranceAddress:label'),
        '#help' => elgg_echo('membership:settings:insuranceAddress:help'),
        'editor' => false,
        'name' => 'params[insuranceAddress]',
        'value' => $insuranceAddress
    ],
    [
        '#type' => 'longtext',
        '#label' => elgg_echo('membership:settings:insuranceMember:label'),
        '#help' => elgg_echo('membership:settings:insuranceMember:help'),
        'editor' => false,
        'name' => 'params[insuranceMember]',
        'value' => $insuranceMember
    ],
    [
        '#type' => 'longtext',
        '#label' => elgg_echo('membership:settings:lockblocklist:label'),
        '#help' => elgg_echo('membership:settings:lockblocklist:help'),
        'editor' => false,
        'name' => 'params[lockBlocklist]',
        'value' => $lockBlocklist
    ]
];

foreach ($fields as $field) {
    echo elgg_view_field($field);
}
