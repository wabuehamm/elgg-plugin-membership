<?php

$entities = elgg_extract('entities', $vars, []);

if (count($entities) == 0) {
    echo elgg_extract('no_entities', $vars, 'No entities found');
} else {
    echo elgg_view_entity_list(
        $entities,
        [
            'item_view' => 'object/elements/seasonReport',
            'icon' => false,
            'subtitle' => false,
            'show_social_menu' => false,
            'show_entity_menu' => false,
        ]
    );
}
