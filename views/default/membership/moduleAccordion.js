/*global define*/
define(function(require) {
    const $ = require('jquery')
    require('jquery-ui')
    const elgg = require('elgg')
    require('elgg/ready')

    elgg.register_hook_handler('ready', 'system', () => {
        let classes = ''
        $('.module-accordion-toggle').each(function() {
            const togglerButton = $(this)
            classes = togglerButton.data('classes')
            togglerButton
                .parent()
                .parent()
                .addClass('link')
            togglerButton
                .parent()
                .parent()
                .click(() => {
                    const modules = $(`.${togglerButton.data('classes')}`)
                    const currentModule = $(`#${togglerButton.data('module-id')}`)

                    if ($('.elgg-body', currentModule).hasClass('hidden')) {
                        $('.elgg-body', modules).addClass('hidden')
                        $('.minus-icon', modules).addClass('hidden')
                        $('.plus-icon', modules).removeClass('hidden')
                        $('.elgg-body', currentModule).removeClass('hidden')
                        $('.minus-icon', currentModule).removeClass('hidden')
                        $('.plus-icon', currentModule).addClass('hidden')
                    } else {
                        $('.elgg-body', currentModule).addClass('hidden')
                        $('.minus-icon', currentModule).addClass('hidden')
                        $('.plus-icon', currentModule).removeClass('hidden')
                    }
                })
        })
        $(`.${classes} .elgg-body`).addClass('hidden')
    })
})
