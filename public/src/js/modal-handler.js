function setThumbnailListeners(data_selector = 'media') {
    $('.thumbnail').click(function () {
        const thumbnail_id  = $(this).attr('id');
        const data_id       = $(this).data('thumbnail-id');
        const data          = media_data[data_selector][data_id]['iframe'] || null;
        let $modal_box      = $('.remodal');

        $modal_box.attr('data-remodal-id', thumbnail_id);
        $modal_box.find('.content')
            .first()
            .html(`<div class="video-responsive">${data}</div>`);

        let $remodal = $(`[data-remodal-id=${thumbnail_id}]`).remodal();
        $remodal.open();
    });
}

$(document).ready(function () {    
    $(document).on('closed', '.remodal', function (e) {
        $(this).find('.content').first().html('');
    });
});