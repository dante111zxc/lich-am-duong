<?php

if (!function_exists('lad_getLunarFromDay')) {
    function lad_getLunarFromDay ($date = null){
        $lunar2Solar = new Lunar2solar();
        return $lunar2Solar->getLunarFromDay($date);
    }
}

if (!function_exists('lad_getThu')) {
    function lad_getThu($date = null){
        $lunar2Solar = new Lunar2solar();
        return $lunar2Solar->getThu($date);
    }
}

if (!function_exists('lad_getDiaChiNgay')) {
    function lad_getDiaChiNgay ($date = null){
        $lunar2Solar = new Lunar2solar();
        return $lunar2Solar->getNgayDiaChi($date);
    }
}

if (!function_exists('lad_getCanChiNgay')) {
    function lad_getCanChiNgay ($date = null){
        $lunar2Solar = new Lunar2solar();
        return $lunar2Solar->getNgayCanChi($date);
    }
}

if (!function_exists('lad_getCanChiNam')) {
    function lad_getCanChiNam ($date = null){
        $lunar2Solar = new Lunar2solar();
        return $lunar2Solar->getNamCanChi($date);
    }
}

if (!function_exists('lad_getCanChiThang')) {
    function lad_getCanChiThang ($date = null){
        $lunar2Solar = new Lunar2solar();
        return $lunar2Solar->getThangCanChi($date);

    }
}

if (!function_exists('lad_gioHoangDao')) {
    function lad_gioHoangDao($date = null){
        global $wpdb;
        $table = $wpdb->prefix . 'data_gio_hoang_dao';
        $lunar2Solar = new Lunar2solar();
        $ngayCanChi = $lunar2Solar->getNgayCanChi($date);
        $ngayCanChi = mb_strtolower($ngayCanChi,'UTF-8');
        $sql = "SELECT * FROM $table WHERE LOWER(ngay_can_chi) = '$ngayCanChi'";
        $row = $wpdb->get_row($sql);
        if (!empty($row->noi_dung)) {
            $data = json_decode($row->noi_dung);
            $gio_hoang_dao = [];
            foreach ($data as $key => $item) {
                if (!empty($item) && $item->gio_hoang_dao === 'Giờ Hoàng Đạo') {
                    $gio_hoang_dao[$key]['gio_am'] = $item->gio_am;
                    $gio_hoang_dao[$key]['gio_duong'] = $item->gio_duong;
                }
            }

        }
        return array_values($gio_hoang_dao);
    }
}


//lấy dữ liệu ngày tháng âm lịch -> dương lịch
if ( !function_exists('getDataSolarDay')) {
    function getDataSolarDay($date){
        $lunar_day = date('j', strtotime($date));
        $lunar_month = date('n', strtotime($date));
        $lunar_year = date('Y', strtotime($date));

        $lunar2Solar = new Lunar2solar();
        $lunar = $lunar2Solar->convertLunar2Solar($lunar_day,$lunar_month, $lunar_year, 0, 7);


        $result = null;
        //Data dương lịch
        if (!empty($lunar)) {
            //data dương lịch
            $date = $lunar[2] .'-'.$lunar[1].'-'.$lunar[0];
            $result['solar_day'] = $lunar[0];
            $result['solar_month'] = $lunar[1];
            $result['solar_year'] = $lunar[2];
            $result['solar_day_of_week'] = lad_getThu(date('Y-m-d', strtotime($date)));

            //Data âm lịch
            $result['lunar_day'] = $lunar_day;
            $result['lunar_month'] = $lunar_month;
            $result['lunar_year'] = $lunar_year;

            //Can chi ngày tháng năm
            $result['can_chi_ngay'] = lad_getCanChiNgay($date);
            $result['can_chi_thang'] = lad_getCanChiThang($date);
            $result['can_chi_nam'] = lad_getCanChiNam($date);


            //giờ hoàng đạo
            $result['gio_hoang_dao'] = lad_gioHoangDao($date);

        }

        return $result;
    }
}

//lấy dữ liệu ngày tháng dương lịch -> âm lịch
if ( !function_exists('getDataLunarDay')) {
    function getDataLunarDay($date) {
        $lunarDay = lad_getLunarFromDay($date);
        $result = null;
        if (!empty($lunarDay)) {
            //data dương lịch
            $result['solar_day'] = date('j', strtotime($date));
            $result['solar_month'] = date('n', strtotime($date));
            $result['solar_year'] = date('Y', strtotime($date));
            $result['solar_day_of_week'] = lad_getThu($date);

            //Data âm lịch
            $result['lunar_day'] = date('j', strtotime($lunarDay));
            $result['lunar_month'] = date('n', strtotime($lunarDay));
            $result['lunar_year'] = date('Y', strtotime($lunarDay));

            //Can chi ngày tháng năm
            $result['can_chi_ngay'] = lad_getCanChiNgay($date);
            $result['can_chi_thang'] = lad_getCanChiThang($date);
            $result['can_chi_nam'] = lad_getCanChiNam($date);

            //giờ hoàng đạo
            $result['gio_hoang_dao'] = lad_gioHoangDao($date);
        }


        return $result;
    }
}