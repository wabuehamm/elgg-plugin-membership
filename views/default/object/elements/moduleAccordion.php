<?php

/**
 * Elgg module accordion feature
 *
 * @uses $vars['id']              The id of this module
 * @uses $vars['classes']  The CSS classes of the other modules
 */

$module_id = elgg_extract('id', $vars, null);
$module_classes = elgg_extract('classes', $vars, null);

echo elgg_format_element(
    'a',
    [
        'class' => 'elgg-non-link module-accordion-toggle',
        'data-classes' => $module_classes,
        'data-module-id' => $module_id,
    ],
    elgg_view_icon(
        'minus',
        'hidden minus-icon'
    ) .
    elgg_view_icon(
        'plus',
        'plus-icon'
    ) .' '
);

elgg_require_js('membership/moduleAccordion');
