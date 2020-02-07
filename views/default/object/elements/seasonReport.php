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
        $departments->getParticipations(),
        function ($participationType) use ($entity) {
            return elgg_generate_url(
                'report:object:departments',
                [
                    'season_guid' => $entity->getGUID(),
                    'participation_type' => $participationType
                ]
            );
        }
    )
);

/** @var $productions Production[] */
$productions = $entity->getProductions();

$module_content = '';

if (count($productions) == 0) {
    $module_content .= elgg_echo('membership:participations:none');
} else {
    $productions_content = '';
    $productions_participations = [];
    $productions_participationTypes = [];
    foreach ($productions as $production) {
        Tools::assert($production instanceof Production);

        $productions_content .= elgg_format_element(
            'h3',
            [],
            $production->getDisplayName()
        );
        $production_participations = $production->getParticipations();
        $productions_participations = array_merge(
            $productions_participations,
            $production_participations
        );

        $production_participationTypes = $production->getParticipationTypes();
        $productions_participationTypes = array_merge(
            $productions_participationTypes,
            $production_participationTypes
        );

        $productions_content .= Tools::participationList(
            $production_participationTypes,
            $production_participations,
            function ($participationType) use ($entity, $production) {
                return elgg_generate_url(
                    'report:object:production',
                    [
                        'season_guid' => $entity->getGUID(),
                        'production_guid' => $production->getGUID(),
                        'participation_type' => $participationType
                    ]
                );
            }
        );
    }

    $module_content .= elgg_format_element(
        'h3',
        [],
        elgg_echo('membership:productions:all')
    );

    $module_content .= Tools::participationList(
        $productions_participationTypes,
        $productions_participations,
        function ($participationType) use ($entity) {
            return elgg_generate_url(
                'report:object:production',
                [
                    'season_guid' => $entity->getGUID(),
                    'production_guid' => 0,
                    'participation_type' => $participationType
                ]
            );
        }
    );

    $module_content .= $productions_content;
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
        'class' => $class
    ]
);