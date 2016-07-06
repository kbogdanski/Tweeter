/**
 * Created by Kamil on 2016-07-03.
 */

$(document).ready(function() {
    var areaComment = $('#addComment');
    var spanComment = $('#counterComment');

    var countComment = function (event) {
        var counter = areaComment.val().length;
        var color;

        spanComment.text(counter+'/60');
        if (counter < 21) {
            color = 'green';
        } else if (counter > 40) {
            color = 'red';
        } else {
            color = 'blue';
        }
        spanComment.css('color',color);
    };

    areaComment.on('keyup',countComment);
    countComment();
});