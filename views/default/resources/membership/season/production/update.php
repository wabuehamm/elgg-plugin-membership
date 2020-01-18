<?php

$mode = elgg_extract('mode', $vars, 'add');

$body = elgg_view_layout(
    'default',
    [
        'title' => elgg_echo("membership:season:production:$mode"),
        'content' => elgg_view_form('membership/season/production/update', [], $vars)
    ]
);

echo elgg_view_page(
    elgg_echo("membership:season:production:$mode"),
    $body
);