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
