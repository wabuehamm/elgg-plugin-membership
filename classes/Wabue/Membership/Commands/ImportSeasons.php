<?php

namespace Wabue\Membership\Commands;

use Elgg\Cli\Command;
use SQLite3;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Wabue\Membership\Entities\Participation;
use Wabue\Membership\Entities\ParticipationObject;
use Wabue\Membership\Entities\Production;
use Wabue\Membership\Entities\Season;
use Wabue\Membership\Tools;

class ImportSeasons extends Command
{

    protected static $defaultName = 'membership:importseasons';

    protected function configure()
    {
        parent::configure();

        $this->setDescription('Imports seasons exported from the old membership database')
            ->addArgument('databaseFile', InputArgument::REQUIRED, 'The participations sqlite database file')
            ->addOption('nodelete', 'd', InputOption::VALUE_OPTIONAL, 'Do not delete all seasons before importing', false);
    }

    protected function command()
    {
        $database = new SQLite3($this->argument('databaseFile'));

        $seasons = $database->query("select DISTINCT season, participationObject from participations where participationObject != 'Departments'");

        $seasonProductions = [];
        while ($season = $seasons->fetchArray(SQLITE3_ASSOC)) {
            $seasonProductions[] = $season;
        }

        if (!$this->option('nodelete')) {
            /** @var Season[] $existingSeasons */
            $existingSeasons = Tools::getAllSeasons();

            foreach ($existingSeasons as $season) {
                $season->delete();
            }
        }

        foreach ($seasonProductions as $seasonProduction) {
            $season = Tools::getSeasonByYear($seasonProduction['season']);
            if (is_null($season)) {
                $season = Season::factory(
                    $seasonProduction['season'],
                    0,
                    0,
                    elgg_get_plugin_setting('departments_participations', 'membership')
                );
            }

            $production = new Production();
            $production->title = $seasonProduction['participationObject'];
            $production->owner_guid = 0;
            $production->access_id = ACCESS_LOGGED_IN;
            $production->container_guid = $season->getGUID();
            $production->setParticipationTypes(
                ParticipationObject::participationSettingToArray(elgg_get_plugin_setting('production_participations', 'membership'))
            );
            $production->save();
        }

        $participations = $database->query("SELECT displayname, strftime('%d.%m.%Y', birthday) as birthday, street, zip, mail, season, participationObject, participationType FROM participations");

        $unknownUsers = [];

        while ($participation = $participations->fetchArray(SQLITE3_ASSOC)) {
            $user = Tools::getUserByDisplayname($participation['displayname']);

            if (is_null($user)) {
                $users = get_user_by_email($participation['mail']);

                if (count($users) == 1) {
                    $users = $users[0];
                } else {
                    $user = Tools::getUserByPrivateData($participation['birthday'], $participation['street'], $participation['zip']);
                }
            }

            if (is_null($user)) {
                $unknownUsers[] = $participation;
            } else {
                /** @var Season $season */
                $season = Tools::getSeasonByYear($participation['season']);
                $participationObject = $season->getDepartments();

                if ($participation['participationObject'] != 'Departments') {
                    $productions = $season->getProductions();

                    foreach ($productions as $production) {
                        if ($production->title == $participation['participationObject']) {
                            $participationObject = $production;
                        }
                    }
                }

                /** @var Participation $existingParticipation */
                $existingParticipation = null;
                $existingParticipations = $participationObject->getParticipations($user->getGUID());
                if (count($existingParticipations) == 0) {
                    $existingParticipation = Participation::factory(
                        $user,
                        $season,
                        $participationObject
                    );
                } else {
                    $existingParticipation = $existingParticipations[0];
                }
                $existingParticipation->addParticipationType($participation['participationType']);
            }
        }

    }
}
