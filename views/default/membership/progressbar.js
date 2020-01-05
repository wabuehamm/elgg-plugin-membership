/*global define*/
define(function(require) {
    const $ = require('jquery')
    require('jquery-ui')
    const elgg = require('elgg')
    require('elgg/ready')

    elgg.register_hook_handler('ready', 'system', () => {
        $('.progressbar').each(function() {
            const progressbar = $(this)
            let value = progressbar.data('value')
            let max = progressbar.data('max')
            progressbar.progressbar({
                value: value,
                max: max
            })
        })
    })
})
