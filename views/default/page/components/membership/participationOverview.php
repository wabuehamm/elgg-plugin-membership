<?php

$entities = elgg_extract('entities', $vars, []);

if (count($entities) == 0) {
    echo elgg_extract('no_entities', $vars, 'No entities found');
} else {
    foreach ($entities as $entity) {
        echo elgg_view(
            'object/elements/summary',
            [
                'entity' => $entity,
                'title' => $entity->getDisplayName(),
                'content' => elgg_view(
                    'object/elements/participationOverview',
                    [
                        'entity' => $entity
                    ]
                ),
                'icon' => false,
                'metadata' => false,
                'subtitle' => false,
            ]
        );
    }
}
