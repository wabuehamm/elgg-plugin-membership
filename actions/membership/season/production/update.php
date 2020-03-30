<?php

use Wabue\Membership\Entities\ParticipationObject;
use Wabue\Membership\Entities\Production;

$guid = intval(get_input('guid', -1));
$seasonGuid = intval(get_input('season_guid', null));
$title = get_input('title', '');
$participationTypes = get_input('participationTypes', '');

if (
    !is_int($guid) ||
    !is_int($seasonGuid) ||
    $title == '' ||
    $participationTypes == ''
) {
    return elgg_error_response(elgg_echo('BadRequestException'));
}

if (!ParticipationObject::validateParticipationSetting($participationTypes)) {
    return elgg_error_response(elgg_echo('membership:errors:wrongParticipationTypes'));
}

if ($guid != -1) {
    $entity = get_entity($guid);
    if (!$entity instanceof Production) {
        return elgg_error_response();
    }
    $entity->title = $title;
    $entity->save();
} else {
    $entity = Production::factory($title, $seasonGuid, $participationTypes);
}

return elgg_ok_response(
    ['entity' => $entity],
    elgg_echo('save:success'),
    elgg_generate_url(
        'view:object:season',
        [
            'guid' => $seasonGuid,
        ]
    )
);
