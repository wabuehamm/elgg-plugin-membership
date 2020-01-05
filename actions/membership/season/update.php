<?php

use Wabue\Membership\Entities\Departments;
use Wabue\Membership\Entities\Season;

$guid = intval(get_input('guid', -1));
$year = intval(get_input('year', 0));
$lockdate = get_input('lockdate', '');
$enddate = get_input('enddate', '');

if (
    !is_int($guid) ||
    !is_int($year) ||
    $year == 0 ||
    $lockdate == '' ||
    $enddate == ''
) {
    return elgg_error_response(elgg_echo('BadRequestException'));
}

if ($guid != -1) {
    $entity = get_entity($guid);
    assert($entity instanceof Season);
} else {
    $entity = new Season();
}

$entity->year = $year;
$entity->lockdate = $lockdate;
$entity->enddate = $enddate;
$entity->save();

// Add departments to the new season

$departments = new Departments();
$departments->container_guid = $entity->guid;
$departments->setParticipationTypes([
    'Marketing',
    'Geländepflege',
    'Bühnenbau',
    'Nähgruppe',
    'Fundus',
    'Kostüme',
    'Requisite'
]);
$departments->save();

return elgg_ok_response(
    ['entity' => $entity],
    elgg_echo('save:success'),
    elgg_generate_url('default:object:season')
);