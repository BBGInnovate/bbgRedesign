(function($) {
    $('document').ready(function() {
        $.ajax({
            method: 'POST',
            url: ajax_object.ajax_url,
            type: 'JSON',
            data: {
                action: 'get_biz_opps'
            },
            success: function(response) {
                if (response.data.length != 0) {
                    $('#loading-biz-opps').hide();
                    $('#biz-opps').append(response.data);
                }
            }
        });
    });
})(jQuery);