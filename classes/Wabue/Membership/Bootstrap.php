<?php

namespace Wabue\Membership;

use Elgg\Collections\Collection;
use Elgg\DefaultPluginBootstrap;
use Elgg\Hook;
use ElggMenuItem;
use ElggUser;
use Psr\Log\LogLevel;
use Wabue\Membership\Entities\Production;
use Wabue\Membership\Entities\Season;

/**
 * Membership bootstrap class
 */
class Bootstrap extends DefaultPluginBootstrap
{

    /**
     * Extend some views
     */
    public function extendViews()
    {
        // Season report progressbar CSS
        elgg_extend_view('elements/components.css', 'elements/membership/components/progressbar.css');
        // Report table CSS
        elgg_extend_view('elements/components.css', 'elements/membership/components/reporttable.css');
        // Add calculated membership statistics
        elgg_extend_view('profile/wrapper', 'membership/profile/awayYears');
        // Hide the internal membership profile fields
        elgg_extend_view('profile/wrapper', 'membership/profile/hideVerein');
    }

    /**
     * Register hooks (see the hook functions for details)
     */
    public function registerHooks()
    {
        elgg_register_plugin_hook_handler('register', 'menu:title', 'Wabue\Membership\Bootstrap::titleMenuHook');
        elgg_register_plugin_hook_handler('register', 'menu:season_participate', 'Wabue\Membership\Bootstrap::seasonParticpateMenuHook');
        elgg_register_plugin_hook_handler('container_permissions_check', 'object', 'Wabue\Membership\Bootstrap::containerPermissionsCheckHook');
        elgg_register_plugin_hook_handler('cron', 'daily', 'Wabue\Membership\Bootstrap::dailyCron');
    }

    /**
     * Register cli commands
     */
    public function registerCommands()
    {
        elgg_register_plugin_hook_handler('commands', 'cli', function($hook, $type, $return) {
            $return[] = Cli\CopySeasonCommand::class;
            return $return;
        });
    }

    /**
     * Add the membership buttons to the profile menu
     * @param Hook $hook
     * @return mixed|null
     */
    public static function titleMenuHook(Hook $hook)
    {
        $user = $hook->getEntityParam();
        if (!($user instanceof ElggUser) || !$user->canEdit()) {
            return null;
        }

        $return = $hook->getValue();

        $return[] = ElggMenuItem::factory([
            'name' => 'participations',
            'href' => elgg_generate_url('view:participations:seasons', [
                'guid' => $user->getGUID(),
            ]),
            'text' => elgg_echo('membership:participations:button'),
            'icon' => 'theater-masks',
            'class' => ['elgg-button', 'elgg-button-action'],
            'contexts' => ['profile', 'profile_edit'],
        ]);

        $return[] = \ElggMenuItem::factory([
            'name' => 'membership',
            'text' => elgg_echo('membership:membercard'),
            'href' => elgg_generate_url('view:user:membercard', [
                'username' => $user->username
            ]),
            'link_class' => 'elgg-button elgg-button-action',
            'icon' => 'address-card',
        ]);

        return $return;
    }

    /**
     * Add the participate button to the seasons menu
     * @param Hook $hook
     * @return Collection|mixed
     */
    public static function seasonParticpateMenuHook(Hook $hook)
    {
        $entity = $hook->getEntityParam();

        if ($entity instanceof Season) {
            /** @var Collection $menuItems */
            $menuItems = $hook->getValue();
            $menuItems->add(ElggMenuItem::factory([
                'name' => 'participate',
                'text' => elgg_echo('membership:participations:participate'),
                'href' => elgg_generate_url('edit:participations:seasons', [
                    'guid' => elgg_get_page_owner_guid(),
                    'season_guid' => $entity->getGUID()
                ]),
                'link_class' => 'elgg-button elgg-button-action',
            ]));
            return $menuItems;
        }

        return $hook->getValue();
    }

    /**
     * Override the report gatekeeper for the participation selections
     * @param Hook $hook
     * @return bool|void
     */
    public static function containerPermissionsCheckHook(Hook $hook)
    {
        /* @var ElggUser $user */
        $user = $hook->getUserParam();
        if ($hook->getParam('subtype') == 'participation' and $user->isEnabled()) {
            return true;
        }
    }

    /**
     * A hook for running daily tasks
     * @param Hook $hook Cron hook
     * @return void
     */
    public static function dailyCron(Hook $hook) {
        Bootstrap::lockUsers();
    }

    /**
     * Lock all users not participating in the most current active season
     * @return void
     */
    private static function lockUsers() {
        $lockBlockList = preg_split(
            '/\r\n/',
            elgg_get_plugin_setting(
                'lockBlocklist', 'membership'
            )
        );
        /** @var ElggUser[] $allUnbannedUsers */
        $allUnbannedUsers = elgg_get_entities([
            'type' => 'user',
            'subtype' => 'user',
            'metadata_name_value_pairs' => [
                [
                    'name' => 'banned',
                    'value' => 'no',
                    'operand' => '='
                ]
            ],
            'limit' => '0'
        ]);
        /** @var Season[] $mostCurrentSeason */
        $mostCurrentSeason = elgg_get_entities([
            'type' => 'object',
            'subtype' => 'season',
            'metadata_name_value_pairs' => [
                [
                    'name' => 'lockdate',
                    'value' => time(),
                    'operand' => '<'
                ],
                [
                    'name' => 'enddate',
                    'value' => time(),
                    'operand' => '>'
                ]
            ],
            'metadata_name_value_pairs_operator' => 'AND',
            'limit' => '1'
        ]);

        if (count($mostCurrentSeason) == 0) {
            elgg_log("[Lock Users] No current season to lock found", LogLevel::NOTICE);
            return;
        }

        $currentSeason = $mostCurrentSeason[0];

        elgg_log("[Lock Users] Current season is " . $currentSeason->getDisplayName(), LogLevel::NOTICE);

        $lockList = [];

        foreach ($allUnbannedUsers as $user) {
            if (in_array($user->username, $lockBlockList)) {
                continue;
            }

            if ($currentSeason->getDepartments()->getParticipations($user->getGUID())) {
                continue;
            }

            /** @var Production $production */
            foreach ($currentSeason->getProductions() as $production) {
                if ($production->getParticipations($user->getGUID())) {
                    continue 2;
                }
            }

            $lockList[] = $user;
        }

        foreach ($lockList as $userToLock) {
            elgg_log("Locking user " . $userToLock->username, LogLevel::NOTICE);
            $userToLock->ban(elgg_echo('membership:lockuser:reason'));
            $userToLock->save();
        }
    }

    /**
     * Initialize the plugin
     */
    public function init()
    {
        parent::init();
        $this->extendViews();
        $this->registerHooks();
        $this->registerCommands();
    }

}
