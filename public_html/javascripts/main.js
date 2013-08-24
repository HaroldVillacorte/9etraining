$(document).ready(function() {

    // Markitup
    $("textarea").markItUp(mySettings);

    var qnaPanel = document.getElementById('qna-panel');
    var showAnswerButton = document.getElementById('show-answer-button');
    var showQuestionButton = document.getElementById('show-question-button');

    $(showQuestionButton).hide();

    var questions = $('.question');
    if (questions.length > 0) {
        $(qnaPanel).html($(questions[0]).val());
        $(qnaPanel).attr('question', $(questions[0]).val());
        $(qnaPanel).attr('answer', $(questions[0]).attr('answer'));
        $(qnaPanel).attr('note', $(questions[0]).attr('note'));
        $(qnaPanel).attr('sequence', $(questions[0]).attr('sequence'));
    }

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

$('#show-answer-button').on('click', function(event) {
    event.preventDefault();
    var qnaPanel = document.getElementById('qna-panel');
    var showQuestionButton = document.getElementById('show-question-button');
    $(qnaPanel).html($('#qna-panel').attr('answer'));
    if ($(qnaPanel).attr('note') !== '') {
        $(qnaPanel).append('<br/><strong>Note:</strong><br/>' + $(qnaPanel).attr('note'));
    }
    $(this).hide();
    $(showQuestionButton).show();
});

$('#show-question-button').on('click', function(event) {
    event.preventDefault();
    var qnaPanel = document.getElementById('qna-panel');
    var showAnswerButton = document.getElementById('show-answer-button');
    $(qnaPanel).html($('#qna-panel').attr('question'));
    $(this).hide();
    $(showAnswerButton).show();
});

$('#next-button').on('click', function(event) {
    event.preventDefault();
    var qnaPanel = document.getElementById('qna-panel');
    var nextSequence = parseInt($(qnaPanel).attr('sequence')) + 1;
    var nextQuestion = $('.question[sequence="' + nextSequence +'"]');
    $(qnaPanel).html($(nextQuestion).val());

    if (parseInt($(qnaPanel).attr('sequence')) ===  $('.question').size() -1) {
        alert('That was the last question.  Your ninja skills have improved by ten points.');
    }

    $(qnaPanel).attr('question', $(nextQuestion).val());
    $(qnaPanel).attr('answer', $(nextQuestion).attr('answer'));
    $(qnaPanel).attr('note', $(nextQuestion).attr('note'));
    if (parseInt($(qnaPanel).attr('sequence')) !==  $('.question').size() -1) {
        $(qnaPanel).attr('sequence', parseInt($(qnaPanel).attr('sequence')) + 1);
    }

    var showAnswerButton = document.getElementById('show-answer-button');
    var showQuestionButton = document.getElementById('show-question-button');
    $(showAnswerButton).show();
    $(showQuestionButton).hide();
});

$('#prev-button').on('click', function(event) {
    event.preventDefault();
    var qnaPanel = document.getElementById('qna-panel');
    var prevSequence = parseInt($(qnaPanel).attr('sequence')) - 1;
    var prevQuestion = $('.question[sequence="' + prevSequence +'"]');

    if ((parseInt($(qnaPanel).attr('sequence')) - 1) === -1) {
        alert('Oops...this is the first question.  Go the other way.');
    }

    $(qnaPanel).html($(prevQuestion).val());
    $(qnaPanel).attr('question', $(prevQuestion).val());
    $(qnaPanel).attr('answer', $(prevQuestion).attr('answer'));
    $(qnaPanel).attr('note', $(prevQuestion).attr('note'));
    if ((parseInt($(qnaPanel).attr('sequence')) - 1) !== -1) {
        $(qnaPanel).attr('sequence', parseInt($(qnaPanel).attr('sequence')) - 1);
    }

    var showAnswerButton = document.getElementById('show-answer-button');
    var showQuestionButton = document.getElementById('show-question-button');

    $(showAnswerButton).show();
    $(showQuestionButton).hide();
});