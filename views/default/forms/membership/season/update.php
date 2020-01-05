<?php

use Wabue\Membership\Entities\Season;

$guid = elgg_extract('guid', $vars, null);

if ($guid) {
    $entity = get_entity($guid);
    assert($entity instanceof Season);
}

echo elgg_view_field([
    '#type' => 'hidden',
    'name' => 'guid',
    'value' => $guid ? $guid : '-1',
]);

echo elgg_view_field([
        '#type' => 'number',
        '#label' => elgg_echo('membership:season:form:year'),
        '#help' => elgg_echo('membership:season:form:year:help'),
        'name' => 'year',
        'required' => true,
        'value' => $guid ? $entity->year : '',
]);

echo elgg_view_field([
        '#type' => 'date',
        '#label' => elgg_echo('membership:season:form:lockdate'),
        '#help' => elgg_echo('membership:season:form:lockdate:help'),
        'name' => 'lockdate',
        'required' => true,
        'value' => $guid ? $entity->lockdate : '',
]);

echo elgg_view_field([
        '#type' => 'date',
        '#label' => elgg_echo('membership:season:form:enddate'),
        '#help' => elgg_echo('membership:season:form:enddate:help'),
        'name' => 'enddate',
        'required' => true,
        'value' => $guid ? $entity->enddate : '',
]);

$mode = elgg_extract('mode', $vars, 'add');

elgg_set_form_footer(
    elgg_view_field([
        '#type' => 'submit',
        'text' => elgg_echo("membership:season:$mode"),
    ])
);
