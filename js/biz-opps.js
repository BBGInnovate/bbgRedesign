(function($) {
    $('document').ready(function() {
        let loadingSpinner = $('#loading-biz-opps');
        if (!loadingSpinner.length) {
            return;
        }

        $.ajax({
            method: 'POST',
            url: ajax_object.ajax_url,
            type: 'JSON',
            data: {
                action: 'get_biz_opps'
            },
            success: function(response) {
                if (response.data.length != 0) {
                    loadingSpinner.hide();
                    $('#biz-opps').append(response.data);
                }
            }
        });
    });
})(jQuery);