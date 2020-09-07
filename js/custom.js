jQuery(document).ready(function ($) {
    jQuery('#lichAmDuong').submit(function (e) {
        e.preventDefault();
        let data = {
            action: 'get_data',
            query: jQuery(this).serialize(),
        };
        jQuery.ajax({
            type : "POST",
            url  : lad.ajax_url,
            data :  data,
            dataType: "JSON",
            beforeSend: function () {
            },

            success: function (response) {
                console.log(response);
            }
        })
    });
});