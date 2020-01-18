<?php

use Wabue\Membership\Entities\Departments;
use Wabue\Membership\Entities\ParticipationObject;
use Wabue\Membership\Tools;

/** @var $entity ParticipationObject */
$entity = elgg_extract('entity', $vars, null);

Tools::assert(!is_null($entity));
Tools::assert($entity instanceof ParticipationObject);

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
        Tools::assert($participation instanceof ParticipationObject);
        if (in_array($participationType, $participation->getParticipationTypes())) {
            $count++;
        }
    }
    $item .= $count;
    $items .= elgg_format_element('li', [], $item);
}

$content = elgg_format_element(
    'ul',
    [],
    $items
);

$content .= elgg_format_element(
    'div',
    [
        'id' => 'participationProgressbar',
        'class' => 'progressbar',
        'data-value' => count($participations),
        'data-max' => count($users)
    ]
);

elgg_require_js('membership/progressbar');

echo elgg_view('object/elements/summary', [
    'entity' => $entity,
    'title' => $entity->getDisplayName(),
    'content' => $content,
    'icon' => false,
    'metadata' => $entity instanceof Departments ? false : null,
    'subtitle' => false,
    'show_social_menu' => false,
]);