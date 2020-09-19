<?php

namespace Wabue\Membership\Commands;

use Elgg\Cli\Command;
use ElggUser;
use SQLite3;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Wabue\Membership\Entities\Participation;
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
        $this->getLogger()->setLevel('debug');
        $this->notice(
            sprintf('Connecting to database %s', $this->argument('databaseFile'))
        );
        $database = new SQLite3($this->argument('databaseFile'));

        $this->notice('Querying seasons');

        $seasons = $database->query("select DISTINCT season, participationObject from participations where participationObject != 'Departments'");

        $seasonProductions = [];
        while ($season = $seasons->fetchArray(SQLITE3_ASSOC)) {
            $seasonProductions[] = $season;
        }

        if (!$this->option('nodelete')) {
            /** @var Season[] $existingSeasons */
            $existingSeasons = Tools::getAllSeasons();

            $this->notice('Deleting Seasons');

            foreach ($existingSeasons as $season) {
                $this->notice(
                    sprintf('Deleting Season %s', $season->getYear())
                );

                $season->delete();
            }
        }

        foreach ($seasonProductions as $seasonProduction) {
            $season = Tools::getSeasonByYear($seasonProduction['season']);
            if (is_null($season)) {
                $this->notice(
                    sprintf('Creating season %s', $seasonProduction['season'])
                );
                $season = Season::factory(
                    $seasonProduction['season'],
                    0,
                    0,
                    elgg_get_plugin_setting('departments_participations', 'membership')
                );
            }

            $this->notice(
                sprintf(
                    'Creating production %s',
                    $seasonProduction['participationObject']
                )
            );

            Production::factory(
                $seasonProduction['participationObject'],
                $season->getGUID(),
                elgg_get_plugin_setting('production_participations', 'membership')
            );
        }

        $this->notice('Fetching participations');

        $participations = $database->query("SELECT displayname, strftime('%d.%m.%Y', birthday) as birthday, timeout, street, zip, mail, season, participationObject, participationType FROM participations");

        $unknownUsers = [];

        while ($participation = $participations->fetchArray(SQLITE3_ASSOC)) {
            $this->notice(
                sprintf(
                    'Searching for user %s',
                    $participation['displayname']
                )
            );
            /** @var ElggUser $user */
            $user = Tools::getUserByDisplayname($participation['displayname']);

            if (is_null($user)) {
                $this->notice(
                    sprintf('Not found. Searching for mail %s', $participation['mail'])
                );
                $users = get_user_by_email($participation['mail']);

                if (count($users) == 1) {
                    $users = $users[0];
                } else {
                    $this->notice(
                        sprintf(
                            'Not found. Searching for private data %s, %s, %s',
                            $participation['birthday'],
                            $participation['street'],
                            $participation['zip']
                        )
                    );
                    $user = Tools::getUserByPrivateData(
                        $participation['birthday'] || '',
                        $participation['street'] || '',
                        $participation['zip'] || ''
                    );
                }
            }

            if (is_null($user)) {
                $this->notice(
                    sprintf(
                        'User %s not found',
                        $participation['displayname']
                    )
                );
                $unknownUsers[] = $participation;
            } else {
                $this->notice(
                    sprintf(
                        'Setting away_years to %s',
                        $participation['timeout']
                    )
                );

                $statement = $database->prepare('select count from participationCount where displayname=:name');
                $statement->bindValue(':name', $participation['displayname']);
                $participationCount = $statement->execute()->fetchArray(SQLITE3_ASSOC);

                $alreadyCalculated = 0;
                if(count($participationCount) > 0) {
                    $alreadyCalculated = 10 - intval($participationCount['count']);
                }

                $awayYears = $participation['timeout'] - $alreadyCalculated;
                $user->setProfileData('away_years', $awayYears >= 0 ? $awayYears : 0);

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

                if ($participation['participationType'] == 'rg') {
                    $participation['participationType'] = 'ra';
                }

                $this->notice(
                    sprintf(
                        'Participating in %s of %s in season %s',
                        $participation['participationType'],
                        $participation['participationObject'],
                        $participation['season']
                    )
                );

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
