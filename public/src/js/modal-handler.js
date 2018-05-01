$(document).ready(function () {
    $('.thumbnail').click(function () {
        const thumbnail_id  = $(this).attr('id');
        const data_id       = $(this).data('thumbnail-id');
        let $modal_box      = $('.remodal');

        $modal_box.attr('data-remodal-id', thumbnail_id);
        $modal_box.html(media_data[data_id]["iframe"]);

        let $remodal = $(`[data-remodal-id=${thumbnail_id}]`).remodal();
        $remodal.open();
    });

    
    $(document).on('closed', '.remodal', function (e) {
        $(this).html('');
    });
});