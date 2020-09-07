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
                let solar_day = response.data.solar_day;
                let solar_month = response.data.solar_month;
                let solar_year = response.data.solar_year;
                let solar_day_of_week = response.data.solar_day_of_week;

                let lunar_day = response.data.lunar_day;
                let lunar_month = response.data.lunar_month;
                let lunar_year = response.data.lunar_year;

                let can_chi_nam = response.data.can_chi_nam;
                let can_chi_ngay = response.data.can_chi_ngay;
                let can_chi_thang = response.data.can_chi_thang;

                let gio_hoang_dao = response.data.gio_hoang_dao;
                let text_gio_hoang_dao = '';
                switch (response.data.type) {
                    /**Solar to lunar**/
                    case 1:

                        /**Data Solar**/
                        $('#lad-text-1').html('Dương lịch - âm lịch ngày ' +solar_day+ '/' +solar_month+ '/' +solar_year);
                        $('#lad-text-2').html('Tháng ' +solar_month+ ' năm ' +solar_year);
                        $('#lad-text-4').text(solar_day);
                        $('#lad-text-5').text(solar_day_of_week);



                        /**Data lunar**/
                        $('#lad-text-3').html('Tháng ' +lunar_month+ ' năm ' +lunar_year+ ' ( ' +can_chi_nam+ ' )' );
                        $('#lad-text-6').text(lunar_day);
                        $('#lad-text-7').html('<span>Ngày: <b>'+can_chi_ngay+'</b></span>');
                        $('#lad-text-8').html('<span>Tháng: <b>'+can_chi_thang+'</b></span>');
                        $.each(gio_hoang_dao, function (key, val) {
                            text_gio_hoang_dao += $(val.gio_am) + ' ( '+$(val.gio_duong)+' ), ';
                            if (key === (gio_hoang_dao.length() - 1)) {
                                text_gio_hoang_dao += $(val.gio_am) + ' ( '+$(val.gio_duong)+' )';
                            }
                        });

                        $('#lad-text-9').text(text_gio_hoang_dao);
                }
            }
        })
    });
});