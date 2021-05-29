<?php

/** @var ElggUser $user */

use Wabue\Membership\Tools;

$user = elgg_get_page_owner_entity();

$content = '';

$content .= elgg_view('object/elements/field', [
    'label' => elgg_echo('membership:profile:member_since:label'),
    'value' => elgg_format_element('span', [], $user->getProfileData('member_since')),
    'class' => 'group-profile-field',
    'name' => 'member_since',
]);

$content .= elgg_view('object/elements/field', [
    'label' => elgg_echo('membership:profile:away_years:label'),
    'value' => elgg_format_element('span', [], Tools::calculateAwayYears($user)),
    'class' => 'group-profile-field',
    'name' => 'away_years_calculated',
]);

$content .= elgg_view('object/elements/field', [
    'label' => elgg_echo('membership:profile:active_years:label'),
    'value' => elgg_format_element('span', [], Tools::calculateActiveYears($user)),
    'class' => 'group-profile-field',
    'name' => 'active_years',
]);

echo elgg_view_module(
    'info',
    elgg_echo('membership:profile:title'),
    $content
);
