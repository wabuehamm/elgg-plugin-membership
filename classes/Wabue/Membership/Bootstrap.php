<?php

namespace Wabue\Membership;

/**
 * Plugin Bootstrap
 * Check out http://learn.elgg.org/en/stable/guides/plugins/bootstrap.html for details
 */

use Elgg\DefaultPluginBootstrap;

class Bootstrap extends DefaultPluginBootstrap
{
    public function extendViews()
    {
        elgg_extend_view('elements/components.css', 'elements/membership/components/progressbar.css');
    }

    public function init()
    {
        parent::init();
        $this->extendViews();
    }

}