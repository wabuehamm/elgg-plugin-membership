<?php

use Wabue\Membership\Entities\ParticipationObject;
use Wabue\Membership\Entities\Season;

$guid = intval(get_input('guid', -1));
$year = intval(get_input('year', 0));
$lockdate = intval(get_input('lockdate', 0));
$enddate = intval(get_input('enddate', 0));
$participationTypes = get_input('participationTypes', '');

if (
    !is_int($guid) ||
    !is_int($year) ||
    $year == 0 ||
    $lockdate == 0 ||
    $enddate == 0 ||
    $participationTypes == ''
) {
    return elgg_error_response(elgg_echo('BadRequestException'));
}

if (!ParticipationObject::validateParticipationSetting($participationTypes)) {
    return elgg_error_response(elgg_echo('membership:errors:wrongParticipationTypes'));
}

if ($guid != -1) {
    $entity = get_entity($guid);
    if (!$entity instanceof Season) {
        return elgg_error_response();
    }
    $entity->owner_guid = 0;
    $entity->access_id = ACCESS_LOGGED_IN;
    $entity->setYear($year);
    $entity->setEnddate($enddate);
    $entity->setLockdate($lockdate);
    $entity->save();
} else {
    $entity = Season::factory($year, $enddate, $lockdate, $participationTypes);
}

return elgg_ok_response(
    ['entity' => $entity],
    elgg_echo('save:success'),
    elgg_generate_url('default:object:season')
);
