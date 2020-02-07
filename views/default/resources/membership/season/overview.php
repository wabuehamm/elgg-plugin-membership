<?php

echo elgg_view_page(
    elgg_echo('membership:title'),
    elgg_view(
        'page/components/tabs',
        [
            'tabs' => [
                [
                    'name' => 'reports',
                    'text' => elgg_echo('membership:overview:tabs:reports'),
                    'content' => elgg_view('resources/membership/season/reports')
                ],
                [
                    'name' => 'seasons',
                    'text' => elgg_echo('membership:overview:tabs:seasons'),
                    'content' => elgg_view('resources/membership/season/list')
                ]
            ]
        ]
    )
);