const membershipAddLine = $ => {
    const row = $('<tr>')
    const columnKeys = ['_username'].concat($('div.membershipReport table').data('columns'))

    let memberInput = null

    for (const columnKey of columnKeys) {
        const columnInput = $('<input>')
        columnInput.attr('name', `${columnKey}[]`)
        if (columnKey === '_username') {
            columnInput.attr('type', 'text')
            columnInput.autocomplete({
                source: '/livesearch/users',
                minLength: 2,
                html: 'html',

                // turn off experimental live help - no i18n support and a little buggy
                messages: {
                    noResults: '',
                    results: () => {}
                }
            })
            memberInput = columnInput
        } else {
            columnInput.attr('type', 'checkbox')
            columnInput.keyup(ev => {
                if (ev.key === 'Enter') {
                    membershipAddLine($)
                }
            })
        }

        if (columnKey === columnKeys[columnKeys.length - 1]) {
            columnInput.keydown(ev => {
                if (ev.key === 'Tab') {
                    ev.preventDefault()
                    membershipAddLine($)
                }
            })
        }

        if (memberInput) {
            memberInput.focus()
        }

        row.append($('<td>').append(columnInput))
    }

    $('div.membershipReport table tbody').append(row)
    memberInput.focus()
}

/*global define*/
define(function(require) {
    const $ = require('jquery')
    require('jquery-ui')
    const elgg = require('elgg')
    require('elgg/ready')
    require('jquery.ui.autocomplete.html')

    elgg.register_hook_handler('ready', 'system', () => {
        membershipAddLine($)
    })
})
