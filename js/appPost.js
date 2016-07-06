/**
 * Created by Kamil on 2016-07-03.
 */

$(document).ready(function() {
    var areaPost = $('#addPost');
    var spanPost = $('#counterPost');

    var countPost = function (event) {
        var counter = areaPost.val().length;
        var color;

        spanPost.text(counter+'/140');
        if (counter < 51) {
            color = 'green';
        } else if (counter > 100) {
            color = 'red';
        } else {
            color = 'blue';
        }
        spanPost.css('color',color);
    };

    areaPost.on('keyup',countPost);
    countPost();
});