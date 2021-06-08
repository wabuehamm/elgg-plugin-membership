<?php

use Wabue\Membership\Entities\Participation;
use Wabue\Membership\Entities\Season;
use Wabue\Membership\Tools;

/** @var Season $season */
$season_guid = get_input('season_guid', null);

Tools::assert(
    !is_null($season_guid)
);

$season = get_entity($season_guid);

Tools::assert(
    $season instanceof Season
);

$departments = $season->getDepartments();
$productions = $season->getProductions();

$rows = get_input('rows', 0);
$columns = json_decode(get_input('columns', '[]'));

for ($i = 0; $i < $rows; $i++) {
    $username = get_input("${i}__username",null);
    if ($username != null) {
        $participations = [];
        $user = get_user_by_username($username);
        if ($user) {
            foreach ($columns as $column) {
                if (get_input("${i}_${column}", 'off') == "on") {
                    [$guid, $key] = preg_split("/_/", $column);
                    if (!key_exists($guid, $participations)) {
                        $participations[$guid] = [];
                    }
                    array_push($participations[$guid], $key);
                }
            }
            foreach ($participations as $guid => $types) {
                foreach ([$departments, $productions] as $participationObject) {
                    if ($participationObject->guid == $guid) {
                        $participation = $participationObject->getParticipations($user->guid);
                        if (count($participation) > 0) {
                            $participation = $participation[0];
                        } else {
                            $participation = Participation::factory(
                                $user,
                                $season,
                                $participationObject
                            );
                        }
                        $participation->addParticipationType($types);
                        $participation->save();
                    }
                }
            }
        }
    }
}

return elgg_ok_response(
    '',
    elgg_echo('save:success'),
    elgg_generate_url(
        'view:object:season',
        [
            'guid' => $season_guid,
        ]
    )
);
