<?php

namespace Wabue\Membership;

/**
 * Plugin Bootstrap
 * Check out http://learn.elgg.org/en/stable/guides/plugins/bootstrap.html for details
 */

use Elgg\Collections\Collection;
use Elgg\DefaultPluginBootstrap;
use Elgg\Hook;
use ElggMenuItem;
use ElggUser;
use Wabue\Membership\Commands\ImportSeasons;
use Wabue\Membership\Entities\Season;

class Bootstrap extends DefaultPluginBootstrap
{
    public function extendViews()
    {
        elgg_extend_view('elements/components.css', 'elements/membership/components/progressbar.css');
        elgg_extend_view('elements/components.css', 'elements/membership/components/reporttable.css');
        elgg_extend_view('profile/fields', 'membership/profile/awayYears');
    }

    public function registerHooks()
    {
        elgg_register_plugin_hook_handler('register', 'menu:title', 'Wabue\Membership\Bootstrap::titleMenuHook');
        elgg_register_plugin_hook_handler('register', 'menu:season_participate', 'Wabue\Membership\Bootstrap::seasonParticpateMenuHook');

        elgg_register_plugin_hook_handler('commands', 'cli', function ($hook, $type, $return) {
            $return[] = ImportSeasons::class;
            return $return;
        });
    }

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

    public function init()
    {
        parent::init();
        $this->extendViews();
        $this->registerHooks();
    }

}
