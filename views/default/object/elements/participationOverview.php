<?php

use Wabue\Membership\Entities\ParticipationObject;

$entity = elgg_extract('entity', $vars, null);

assert($entity != null);
assert($entity instanceof ParticipationObject);

$participations = $entity->getParticipations();
$users = elgg_get_entities([
    'type' => 'user',
    'metadata_name_value_pairs' => [
        [
            'name' => 'banned',
            'value' => 'no',
            'operand' => '=',
            'type' => ELGG_VALUE_STRING
        ]
    ]
]);

$items = '';

foreach ($entity->getParticipationTypes() as $participationType) {
    $item = "$participationType: ";
    $count = 0;
    foreach ($participations as $participation) {
        assert($participation instanceof ParticipationObject);
        if (in_array($participationType, $participation->getParticipationTypes())) {
            $count++;
        }
    }
    $item .= $count;
    $items .= elgg_format_element('li', [], $item);
}

echo elgg_format_element(
    'ul',
    [],
    $items
);

echo elgg_format_element(
    'div',
    [
        'id' => 'participationProgressbar',
        'class' => 'progressbar',
        'data-value' => count($participations),
        'data-max' => count($users)
    ]
);

elgg_require_js('membership/progressbar');