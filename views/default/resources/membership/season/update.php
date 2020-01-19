<?php

$mode = elgg_extract('mode', $vars, 'add');

$body = elgg_view_layout(
    'default',
    [
        'title' => elgg_echo("membership:season:$mode"),
        'content' => elgg_view_form('membership/season/update'),
        'sidebar' => false,
    ]
);

echo elgg_view_page(
    elgg_echo("membership:season:$mode"),
    $body
);