// Functions.
replaceQuotes = function(string) {
    return string.replace(/"/g, '&quot;');
};

setQnaPanel = function(question) {
    var qnaPanel = document.getElementById('qna-panel');
    var showQuestionButton = document.getElementById('show-question-button');
    var showAnswerButton = document.getElementById('show-answer-button');
    $(qnaPanel).html($(question).val());
    $(qnaPanel).attr('name', $(question).attr('name'));
    $(qnaPanel).attr('question', $(question).val());
    $(qnaPanel).attr('answer', $(question).attr('answer'));
    $(qnaPanel).attr('note', $(question).attr('note'));
    $(qnaPanel).attr('sequence', $(question).attr('sequence'));
    $(showQuestionButton).hide();
    $(showAnswerButton).show();
};

clearQnaPanel = function() {
    var qnaPanel = document.getElementById('qna-panel');
    var showQuestionButton = document.getElementById('show-question-button');
    var showAnswerButton = document.getElementById('show-answer-button');
    $(qnaPanel).html('Question list is empty.');
    $(qnaPanel).attr('name', 'empty');
    $(qnaPanel).removeAttr('question');
    $(qnaPanel).removeAttr('answer');
    $(qnaPanel).removeAttr('note');
    $(qnaPanel).removeAttr('sequence');
    $(showQuestionButton).hide();
    $(showAnswerButton).show();
};

setQuickSelectList = function() {
    var questions = $('.question');
    var quickSelectList = document.getElementById('quick-select-list');
    var quickSelectListLi = quickSelectList.getElementsByTagName('li');
    $(quickSelectListLi).each(function() {
        $(this).remove();
    });
    for (var i = 0; i < questions.length; i++) {
        $(quickSelectList).append('<li><a href="#" sequence=' +
                i + ' class="quick-select-link">' +
                $(questions[i]).val() + '</a></li>');
    }
};

setBookmarked = function() {
    var bookmarkedQuestionList = document.getElementById('bookmarked-question-list')
    if (bookmarkedQuestionList) {
        $(bookmarkedQuestionList).html('');
        var bookmarkedQuestions = JSON.parse(localStorage.getItem('marked'));
        for (var i = 0; i < bookmarkedQuestions.length; i++) {
            var questionInput = '<input type="hidden" class="question"' +
                    ' name="' + bookmarkedQuestions[i].name + '"' +
                    ' value="' + replaceQuotes(bookmarkedQuestions[i].value) + '"' +
                    ' answer="' + replaceQuotes(bookmarkedQuestions[i].answer) + '"' +
                    ' note="' + replaceQuotes(bookmarkedQuestions[i].note) + '"' +
                    ' sequence="' + i + '" />';
            $('#bookmarked-question-list').append(questionInput);
        }
    }
};

setMarked = function() {
    var markedUl = document.getElementById('marked-ul');
    if (!localStorage.getItem('marked')) {
        var marked = [];
        localStorage.setItem('marked', JSON.stringify(marked));
    }
    else {
        var marked = JSON.parse(localStorage.getItem('marked'));
    }
    $(markedUl).html('');
    for (var i = 0; i < marked.length; i++) {
        $(markedUl).append('<li>' + marked[i].value +
                '&nbsp;<a href="#" class="label secondary small mark-it-remove" sequence="' +
                i + '">Remove</a></li>');
    }
};

// Document Raadey Event.
$(document).ready(function() {
    var showQuestionButton = document.getElementById('show-question-button');

    $(showQuestionButton).hide();

    setBookmarked();

    var questions = $('.question');
    if (questions.length > 0) {
        setQnaPanel(questions[0]);
    }
    else if (questions.length === 0 || !questions) {
        clearQnaPanel();
    }

    setQuickSelectList();

    setMarked();

});

// Show answer button.
$('#show-answer-button').on('click', function(event) {
    event.preventDefault();
    var qnaPanel = document.getElementById('qna-panel');
    if ($(qnaPanel).attr('name') !== 'empty') {
        var showQuestionButton = document.getElementById('show-question-button');
        var qnaPanelNote = $(qnaPanel).attr('note');
        $(qnaPanel).html($('#qna-panel').attr('answer'));
        if (qnaPanelNote) {
            $(qnaPanel).append('<br/><strong>Note:</strong><br/>' + $(qnaPanel).attr('note'));
        }
        $(this).hide();
        $(showQuestionButton).show();
    }
    else {
        alert('Question list is empty.');
    }
});

// Show question button.
$('#show-question-button').on('click', function(event) {
    event.preventDefault();
    var qnaPanel = document.getElementById('qna-panel');
    var showAnswerButton = document.getElementById('show-answer-button');
    $(qnaPanel).html($('#qna-panel').attr('question'));
    $(this).hide();
    $(showAnswerButton).show();
});

// Next Button.
$('#next-button').on('click', function(event) {
    event.preventDefault();
    var qnaPanel = document.getElementById('qna-panel');
    if ($(qnaPanel).attr('name') !== 'empty') {
        var nextSequence = parseInt($(qnaPanel).attr('sequence')) + 1;
        var nextQuestion = $('.question[sequence="' + nextSequence +'"]');

        if (parseInt($(qnaPanel).attr('sequence')) ===  $('.question').size() -1) {
            alert('That was the last question.  Your ninja skills have improved by ten points.');
        }

        if (parseInt($(qnaPanel).attr('sequence')) !==  $('.question').size() -1) {
            $(qnaPanel).attr('sequence', parseInt($(qnaPanel).attr('sequence')) + 1);
            setQnaPanel(nextQuestion);
        }

        var showAnswerButton = document.getElementById('show-answer-button');
        var showQuestionButton = document.getElementById('show-question-button');
        $(showAnswerButton).show();
        $(showQuestionButton).hide();
    }
    else {
        alert('Question list is empty.');
    }
});

// Previous Button.
$('#prev-button').on('click', function(event) {
    event.preventDefault();
    var qnaPanel = document.getElementById('qna-panel');
    if ($(qnaPanel).attr('name') !== 'empty') {
        var prevSequence = parseInt($(qnaPanel).attr('sequence')) - 1;
        var prevQuestion = $('.question[sequence="' + prevSequence +'"]');

        if ((parseInt($(qnaPanel).attr('sequence')) - 1) === -1) {
            alert('Oops...this is the first question.  Go the other way.');
        }

        if ((parseInt($(qnaPanel).attr('sequence')) - 1) !== -1) {
            $(qnaPanel).attr('sequence', parseInt($(qnaPanel).attr('sequence')) - 1);
            setQnaPanel(prevQuestion);
        }

        var showAnswerButton = document.getElementById('show-answer-button');
        var showQuestionButton = document.getElementById('show-question-button');

        $(showAnswerButton).show();
        $(showQuestionButton).hide();
    }
    else {
        alert('Question list is empty.');
    }
});

// Quick select list click.
$('#quick-select-list').on('click', '.quick-select-link', function(event) {
    //event.preventDefault();
    var sequence = parseInt($(this).attr('sequence'));
    var theQuestion = $('.question[sequence="' + sequence +'"]');
    var showAnswerButton = document.getElementById('show-answer-button');
    var showQuestionButton = document.getElementById('show-question-button');

    setQnaPanel(theQuestion);

    $(showAnswerButton).show();
    $(showQuestionButton).hide();
});

// Bookmark it.
$('#mark-it').on('click', function() {
    var qnaPanel = document.getElementById('qna-panel');

    if ($(qnaPanel).attr('name') !== 'empty') {
        var markedUl = document.getElementById('marked-ul');
        var marked = JSON.parse(localStorage.getItem('marked'));
        var questionName = $(qnaPanel).attr('name');
        var theQuestion = $('.question[name="'+ questionName + '"]');
        var questionValue = $(theQuestion).val();
        var questionAnswer = $(theQuestion).attr('answer');
        var questionNote = $(theQuestion).attr('note');

        $(markedUl).html('');
        marked.push({
            name: questionName,
            value: questionValue,
            answer: questionAnswer,
            note: questionNote
        });
        localStorage.setItem('marked', JSON.stringify(marked));

        var newMarked = JSON.parse(localStorage.getItem('marked'));
        for (var i = 0; i < newMarked.length; i++) {
            $(markedUl).append('<li>' + newMarked[i].value +
                    '&nbsp;<a href="#" class="label secondary small mark-it-remove" sequence="' +
                    i + '">Remove</a></li>');
        }
    }
    else {
        alert('Question list is empty.');
    }
});

// Remove marked.
$('#marked-ul').on('click', '.mark-it-remove',function(event) {
    event.preventDefault();

    var marked = JSON.parse(localStorage.getItem('marked'));
    var sequence = parseInt($(this).attr('sequence'));

    marked.splice(sequence, 1);

    localStorage.setItem('marked', JSON.stringify(marked));

    setBookmarked();

    setMarked();

    setQuickSelectList();

    var questions = $('.question');
    if (questions.length > 0) {
        setQnaPanel(questions[0]);
    }
    else if (questions.length === 0 || !questions) {
        clearQnaPanel();
    }
});