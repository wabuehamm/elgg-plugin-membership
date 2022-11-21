<?php

namespace Wabue\Membership\Cli;

use Symfony\Component\Console\Input\InputArgument;
use Wabue\Membership\Entities\Participation;
use Wabue\Membership\Entities\Production;
use Wabue\Membership\Entities\Season;
use Wabue\Membership\Tools;

class UnlockAllUsers extends \Elgg\Cli\Command
{
    protected static $defaultName = 'membership:unlock-all-users';

    protected function configure()
    {
        $this->setHelp("This command allows to unlock all users in the system.");
        $this->setDescription('Unlock all users');
    }

    protected function command()
    {
        /** @var ElggUser[] $allBannedUsers */
        $allBannedUsers = elgg_get_entities([
            'type' => 'user',
            'subtype' => 'user',
            'metadata_name_value_pairs' => [
                [
                    'name' => 'banned',
                    'value' => 'yes',
                    'operand' => '='
                ]
            ],
            'limit' => '0'
        ]);
        foreach ($allBannedUsers as $bannedUser) {
            echo 'Unlocking ' . $bannedUser->username . PHP_EOL;
            $bannedUser->unban();
            $bannedUser->save();
        }
    }
}
