<?php

use Wabue\Membership\Entities\Departments;
use Wabue\Membership\Entities\Production;
use Wabue\Membership\Entities\Season;
use Wabue\Membership\Tools;

/** @var $season Season */
$season = elgg_extract('season', $vars, null);

Tools::assert(!is_null($season));
Tools::assert($season instanceof Season);

$content = '';

/** @var $departments Departments */
$departments = $season->getDepartments();

Tools::assert(!is_null($departments));
Tools::assert($departments instanceof Departments);

$content .= elgg_view_module(
    'info',
    elgg_echo('membership:departments'),
    Tools::participationUpdate(
        "departments",
        array_flip($departments->getParticipationTypes()),
        $departments->getParticipations($owner_guid)
    )
);

/** @var $productions Production[] */
$productions = $season->getProductions();

$productions_content = '';

foreach ($productions as $production) {
    Tools::assert($production instanceof Production);

    $productions_content .= elgg_format_element(
        'h3',
        [],
        $production->getDisplayName()
    );
    $productions_content .= Tools::participationUpdate(
        "production:$production->guid",
        array_flip($production->getParticipationTypes()),
        $production->getParticipations($owner_guid)
    );
}

$content .= elgg_view_module(
    'info',
    elgg_echo('membership:productions'),
    $productions_content
);

elgg_set_form_footer(
    elgg_view_field([
        '#type' => 'submit',
        'text' => elgg_echo("membership:participations:participate"),
    ])
);

echo $content;