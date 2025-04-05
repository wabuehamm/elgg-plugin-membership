<?php

use Wabue\Membership\Tools;

/** @var ElggUser $user */
$user = elgg_get_page_owner_entity();

$content = '';

if ($user->getProfileData('member_since') != null) {
    $content .= elgg_view('object/elements/field', [
        'label' => elgg_echo('membership:profile:member_since:label'),
        'value' => elgg_format_element('span', [], $user->getProfileData('member_since')),
        'name' => 'member_since',
    ]);

    $content .= elgg_view('object/elements/field', [
        'label' => elgg_echo('membership:profile:away_years:label'),
        'value' => elgg_format_element('span', [], Tools::calculateAwayYears($user)),
        'name' => 'away_years_calculated',
    ]);

    $content .= elgg_view('object/elements/field', [
        'label' => elgg_echo('membership:profile:active_years:label'),
        'value' => elgg_format_element('span', [], Tools::calculateActiveYears($user)),
        'name' => 'active_years',
    ]);

    echo elgg_view_module(
        'info',
        elgg_echo('membership:profile:title'),
        elgg_format_element('div', ['class' => 'elgg-profile-fields'], $content)
    );
}

