<?php

use Wabue\Membership\Entities\Production;
use Wabue\Membership\Entities\Season;
use Wabue\Membership\Tools;

/** @var $entity Season */
$entity = elgg_extract('entity', $vars, null);

$owner_guid = elgg_get_page_owner_guid();

Tools::assert(!is_null($entity));

$departments = $entity->getDepartments();

Tools::assert(!is_null($departments));

$content = '';

$content .= elgg_view_module(
    'info',
    elgg_echo('membership:participations:departments'),
    Tools::participationList(
        $departments->getParticipationTypes(),
        $departments->getParticipations($owner_guid)
    )
);

/** @var $productions Production[] */
$productions = $entity->getProductions();

$module_content = '';

if (count($productions) == 0) {
    $module_content .= elgg_echo('membership:participations:none');
} else {
    foreach ($productions as $production) {
        Tools::assert($production instanceof Production);
        $module_content .= elgg_format_element('h3', [
        ], $production->getDisplayName());
        $module_content .= Tools::participationList(
            $production->getParticipationTypes(),
            $production->getParticipations($owner_guid)
        );
    }
}

$content .= elgg_view_module(
    'info',
    elgg_echo('membership:participations:productions'),
    $module_content,
    []
);

$titleprefix = '';
$class = '';

if ($entity->getEnddate() < time()) {
    $titleprefix .= elgg_view('object/elements/moduleAccordion', [
        'id' => 'season' . $entity->guid,
        'classes' => 'seasonModule'
    ]);
    $class = 'seasonModule';
}

if ($entity->getLockdate() < time()) {
    $titleprefix .= elgg_view_icon('lock') . ' ';
}

echo elgg_view_module(
    'info',
    $titleprefix . $entity->getDisplayName(),
    $content,
    [
        'id' => 'season' . $entity->guid,
        'class' => $class,
        'menu' => $entity->getLockdate() < time() ? null : elgg_view_menu(
            'season_participate',
            [
                'entity' => $entity
            ]
        ),
    ]
);