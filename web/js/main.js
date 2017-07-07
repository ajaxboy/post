

//   updates the character count on the tweet post box
function countChars(el)
{
    $('#count').html($(el).val().length + 1);
}


//   toggles the tweet post box
function toggleBox()
{
    $('#box').toggle();

    countChars('#chars');
}

$(document).ready(function() {

    /**
     * Handle tweet post and tweet box
     */

    //make sure the box disappears when you click off else where.
    $('.container').click(function(evt) {
        if(evt.target.nodeName =='BUTTON') {
            return false;
        }
        switch(evt.target.id) {
            case 'box':
            case 'chars':
                return false;
        }

        $('#box').hide();
    });

    //initially disable the button
    $('#tweet').prop('disabled', true);

    //When the textarea value is changed
    $('#chars').on('input', function() {
        if ($(this).val().length > 0) {

            $('#tweet').prop('disabled', false);
        } else {

            $('#tweet').prop('disabled', true);
        }
    });


    $('#tweet').click(function(e) {

       e.preventDefault();

       var form = $(this).closest('form');


       //post tweet
        $.ajax({
            type: "POST",
            url: '/post',
            data: form.serialize(),
            success: function(response) {

                if(response.error) {
                    //keep it simple
                    alert(response.error);

                    return false;
                }

                //reset box count
                $('#chars').val('');
                $('#count').html('0');

                /**
                 * gets the last tweet and uses it as template to produce a new tweet
                 */

                //get template
                var html = $('.tweets').children().first().clone();

                $(html).find('p').replaceWith('<p>'+response.post+'</p>');
                $(html).find('.date').replaceWith('<span class="well-sm date">' + response.date + '</span>');

                $('.tweets').prepend(html);
                $( "#box" ).fadeOut( "slow");

            },
            dataType: 'json'
        });

    });
});