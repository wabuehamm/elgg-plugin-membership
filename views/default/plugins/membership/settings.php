<?php

use Wabue\Membership\Entities\ParticipationObject;

$departmentsParticipations = ParticipationObject::cleanParticipationSetting(elgg_get_plugin_setting('departments_participations', 'membership'));
$productionParticipations = ParticipationObject::cleanParticipationSetting(elgg_get_plugin_setting('production_participations', 'membership'));
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
];

foreach ($fields as $field) {
    echo elgg_view_field($field);
}