$(document).ready(function() {

    // Weight Buttons.
    $('.weight-button').button({
        icons: {
            primary: 'ui-icon-arrowthick-2-n-s'
        }
    });

    $('#sortable').sortable({
        handle: '.handle'
    });

});

// Drop handler.
$('#sortable').on('sortstop', function(event, ui)
{
    var weights = $('#sortable').sortable('toArray');

    // Loop through the sortable array.
    for (var i = 0; i < weights.length; i++)
    {
        var entityId = weights[i];
        var tableRow = document.getElementById(entityId);
        var entity = $(tableRow).attr('entity');
        var offset = parseInt($(tableRow).attr('offset'));
        var weightHandle = document.getElementById('weight-button-' + entityId);
        var tableForm = document.getElementById('weight-' +entityId);
        var actionLink = $(tableForm).attr('action');
        var csrf = $(document.getElementById('csrf-' + entityId)).val();
        //console.log(entity);
        $(weightHandle).children('.ui-button-text').html(i + offset + 1);
        // Run the $.post() function for each link.
        $.post(actionLink, { entity: entity, id: entityId, csrf: csrf, weight: i + offset + 1}, function(result) {
            switch (result.data)
            {
                case 'false':
                    $('#messages').prepend('<div class="columns twelve alert-box alert">\
                        Unable to update weight.<a href="" class="close">&times;</a><div>');
                    break;
                default:
                    console.log(result.data);
            }
        });
    }
});
