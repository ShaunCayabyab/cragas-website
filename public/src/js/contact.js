$(document).ready(function () {
    $('#submit').click(function () {
        let $modal_box = $('.remodal');
        let post_data  = {
            'name'   : $('#form-name').val(),
            'email'  : $('#form-email').val(),
            'subject': $('#form-subject').val(),
            'message': $('#form-message').val(),
        }

        $('input, textarea, button').prop('disabled', true);
        $('#submit').html('Processing...');

        $.ajax({
            url     : './php/contact-handler.php',
            method  : 'POST',
            dataType: 'json',
            data    : post_data,
        })
        .then(function (data) {
            if ('errors' in data) {
                let errors = "<ul>";

                for (let i = 0; i < data['errors'].length; i++) {
                    errors += `<li>${data['errors'][i]}</li>`;
                }

                errors += '</ul>';

                $modal_box.html(`<h2>There was a problem sending your message:</h2>${errors}`);
                $('input, textarea, button').prop('disabled', false);

                let $remodal = $("[data-remodal-id='errors']").remodal();
                $remodal.open();
            } else if ('success' in data) {
                $('.content-right').fadeOut(100, function () {
                    $(this).html("<h2>Message Sent!</h2><p>Thank you very much! A reply will be sent shortly.</p>")
                        .fadeIn(100);
                });
            } else if ('fail' in data) {
                failMessage($modal_box);

                if (data['fail'] !== false) {
                    console.warn(data);
                }
            }
        })
        .fail(function (data) {
            failMessage($modal_box);
        })
        .always(() => {
            $('#submit').html('Submit');
        });
    });

    $(document).on('closed', '.remodal', function (e) {
        $(this).html('');
    });

    function failMessage($modal_box) {
        $('input, textarea, button').prop('disabled', false);
        $modal_box.html("<h2>Oops! We couldn't send your message.</h2><p>Try sending the message again, or email contact@connor-ragas.com directly.</p>");

        let $remodal = $("[data-remodal-id='errors']").remodal();
        $remodal.open();
    }
});