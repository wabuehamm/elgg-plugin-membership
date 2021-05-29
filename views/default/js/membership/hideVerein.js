/**
 * Hides the "Verein" elgg info module for users
 */
function hideVerein() {
    $('.elgg-module-info .elgg-head:contains(Verein)')
        .parent()
        .hide()
}

$.when($.ready).then(hideVerein);
