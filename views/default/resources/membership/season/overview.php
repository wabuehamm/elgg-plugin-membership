<?php

$tabs = [
    [
        'name' => 'reports',
        'text' => elgg_echo('membership:overview:tabs:reports'),
        'content' => elgg_view('resources/membership/season/reports')
    ]
];

if (elgg_is_admin_logged_in()) {
    array_push($tabs,
        [
            'name' => 'seasons',
            'text' => elgg_echo('membership:overview:tabs:seasons'),
            'content' => elgg_view('resources/membership/season/list')
        ]
    );
}

echo elgg_view_page(
    elgg_echo('membership:title'),
    elgg_view(
        'page/components/tabs',
        [
            'tabs' => $tabs
        ]
    )
);
