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


//$today = date('Y-m-d', time());
//dd(getLunarFromDay($today));