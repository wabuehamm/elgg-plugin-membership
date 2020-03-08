<?php

use Wabue\Membership\Entities\ParticipationObject;
use Wabue\Membership\Entities\Season;
use Wabue\Membership\Tools;

/** @var Season $season */
$season = elgg_extract('season', $vars, null);

Tools::assert(
    !is_null($season)
);

Tools::assert(
    $season instanceof Season
);

/** @var ParticipationObject[] $participationObjects */
$participationObjects = array_merge([$season->getDepartments()], $season->getProductions());

$topColumns = [
    elgg_format_element('th')
];
$columns = [
    elgg_format_element(
        'th',
        [],
        elgg_echo('membership:season:batch:member')
    )
];

$columnKeys = [];

foreach ($participationObjects as $participationObject) {
    array_push(
        $topColumns,
        elgg_format_element(
            'th',
            [
                'colspan' => count($participationObject->getParticipationTypes())
            ],
            $participationObject->getDisplayName()
        )
    );
    foreach ($participationObject->getParticipationTypes() as $key => $label) {
        array_push($columnKeys, $participationObject->guid . "_" . $key);
        array_push(
            $columns,
            elgg_format_element(
                'th',
                [],
                $label
            )
        );
    }
}

echo elgg_format_element(
    'input',
    [
        'type' => 'hidden',
        'name' => 'season_guid',
        'value' => $season->getGUID()
    ]
);

echo elgg_view_field([
    '#type' => 'hidden',
    'name' => 'columns',
    'value' => json_encode($columnKeys)
]);

echo elgg_format_element(
    'div',
    [
        'class' => 'membershipReport'
    ],
    elgg_format_element(
        'table',
        [
            'data-columns' => json_encode($columnKeys)
        ],
        elgg_format_element(
            'thead',
            [],

            elgg_format_element(
                'tr',
                [],
                join('', $topColumns)
            )
            .
            elgg_format_element(
                'tr',
                [],
                join('', $columns)
            ),
        )
        .
        elgg_format_element('tbody')

    )
);

elgg_set_form_footer(
    elgg_view_field([
        '#type' => 'button',
        'class' => 'elgg-button elgg-button-action',
        'onclick' => 'javascript:membershipAddLine($)',
        'text' => elgg_echo('membership:season:batch:add'),
    ])
    .
    elgg_view_field([
        '#type' => 'button',
        'class' => 'elgg-button elgg-button-action',
        'onclick' => 'javascript:submit();',
        'text' => elgg_echo("membership:season:batch:save"),
    ])
);
