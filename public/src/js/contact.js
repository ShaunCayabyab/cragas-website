$(document).ready(function () {
    $('#submit').click(function () {
        let $modal_box = $('.remodal');
        let post_data  = {
            'name'   : $('#form-name').val(),
            'email'  : $('#form-email').val(),
            'subject': $('#form-subject').val(),
            'message': $('#form-message').val(),
        }

        $.ajax({
            url     : '../php/contact-handler.php',
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

                let $remodal = $("[data-remodal-id='errors']").remodal();
                $remodal.open();
            } else {
                console.log(data);
            }
        })
        .fail(function (data) {
            $modal_box.html("<h2>Oops! We couldn't send your message.</h2><p>Try sending the message again, or email contact@connor-ragas.com directly.</p>");

            let $remodal = $("[data-remodal-id='errors']").remodal();
            $remodal.open();
        });
    });

    $(document).on('closed', '.remodal', function (e) {
        $(this).html('');
    });
});