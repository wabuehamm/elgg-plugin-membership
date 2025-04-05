import hooks from 'elgg/hooks'

hooks.register('ready', 'system', () => {
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
