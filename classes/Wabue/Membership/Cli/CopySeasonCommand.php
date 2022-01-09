<?php

namespace Wabue\Membership\Cli;

use Symfony\Component\Console\Input\InputArgument;
use Wabue\Membership\Entities\Participation;
use Wabue\Membership\Entities\Production;
use Wabue\Membership\Entities\Season;
use Wabue\Membership\Tools;

class CopySeasonCommand extends \Elgg\Cli\Command
{
    protected static $defaultName = 'membership:copy-season';

    protected function configure()
    {
        $this->setHelp("This command allows to copy all participations from season to another one.");
        $this->setDescription('Copy a season to another one');
        $this->addArgument('sourceSeason', InputArgument::REQUIRED, 'Year of source season');
        $this->addArgument('targetSeason', InputArgument::REQUIRED, 'Year of target season');
    }

    protected function command()
    {
        $sourceSeasonYear = $this->argument('sourceSeason');
        $targetSeasonYear = $this->argument('targetSeason');

        $sourceSeason = Tools::getSeasonByYear($sourceSeasonYear);
        if ($sourceSeason == null) {
            $this->error("Can't find source season for year $sourceSeasonYear");
            return 1;
        }

        if (Tools::getSeasonByYear($targetSeasonYear) != null) {
            $this->error("Season of year $targetSeasonYear already exists.");
            return 1;
        }

        $targetSeason = Season::factory(
            $targetSeasonYear,
            mktime() + 14 * 60 * 60,
            mktime(null, null, null, 12, 31, $targetSeasonYear),
            $sourceSeason->getDepartments()->getParticipationTypesAsString()
        );

        $targetSeason->save();

        foreach ($sourceSeason->getDepartments()->getParticipations() as $sourceParticipation) {
            $newParticipation = Participation::factory(
                $sourceParticipation->getOwnerEntity(),
                $targetSeason,
                $targetSeason->getDepartments()
            );
            $newParticipation->participationTypes = $sourceParticipation->participationTypes;
            $newParticipation->save();
        }

        foreach ($sourceSeason->getProductions() as $production) {
            $targetProduction = Production::factory(
                $production->title,
                $targetSeason->getGUID(),
                $production->getParticipationTypesAsString()
            );
            $targetProduction->save();
            foreach ($production->getParticipations() as $sourceParticipation) {
                $newParticipation = Participation::factory(
                    $sourceParticipation->getOwnerEntity(),
                    $targetSeason,
                    $targetProduction
                );
                $newParticipation->participationTypes = $sourceParticipation->participationTypes;
                $newParticipation->save();
            }
        }
    }
}
