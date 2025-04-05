<?php

if (!elgg_is_admin_logged_in()) {
    // disable certain profile fields for non-admins
    elgg_import_esm("js/membership/hideVerein");
}
