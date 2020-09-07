<?php
class Lunar2solar
{
    public function INT( $d )
    {
        return floor( $d ) ;
    }

    public function getLunarFromDay($date = null){
        $day = date('d',strtotime($date));
        $month = date('m',strtotime($date));
        $year = date('Y',strtotime($date));

        $arr = array_slice($this->convertSolar2Lunar($day, $month, $year, 7), 0, 3);
        $arr[0] = str_pad($arr[0], 2, '0', STR_PAD_LEFT);
        $arr[1] = str_pad($arr[1], 2, '0', STR_PAD_LEFT);
        return implode("-", $arr);
    }

    public function jdFromDate( $dd, $mm, $yy )
    {
        $a = $this::INT( ( 14 - $mm ) / 12 ) ;
        $y = $yy + 4800 - $a ;
        $m = $mm + 12 * $a - 3 ;
        $jd = $dd + $this::INT( ( 153 * $m + 2 ) / 5 ) + 365 * $y + $this::INT( $y / 4 ) - $this::INT( $y /
                100 ) + $this::INT( $y / 400 ) - 32045 ;
        if ( $jd < 2299161 )
        {
            $jd = $dd + $this::INT( ( 153 * $m + 2 ) / 5 ) + 365 * $y + $this::INT( $y / 4 ) -
                32083 ;
        }
        return $jd ;
    }

    public function jdToDate( $jd )
    {
        if ( $jd > 2299160 )
        { // After 5/10/1582, Gregorian calendar
            $a = $jd + 32044 ;
            $b = $this::INT( ( 4 * $a + 3 ) / 146097 ) ;
            $c = $a - $this::INT( ( $b * 146097 ) / 4 ) ;
        }
        else
        {
            $b = 0 ;
            $c = $jd + 32082 ;
        }
        $d = $this::INT( ( 4 * $c + 3 ) / 1461 ) ;
        $e = $c - $this::INT( ( 1461 * $d ) / 4 ) ;
        $m = $this::INT( ( 5 * $e + 2 ) / 153 ) ;
        $day = $e - $this::INT( ( 153 * $m + 2 ) / 5 ) + 1 ;
        $month = $m + 3 - 12 * $this::INT( $m / 10 ) ;
        $year = $b * 100 + $d - 4800 + $this::INT( $m / 10 ) ;
        //echo "day = $day, month = $month, year = $year\n";
        return array(
            $day,
            $month,
            $year
        );
    }

    public function getNewMoonDay( $k, $timeZone )
    {
        $T = $k / 1236.85; // Time in Julian centuries from 1900 January 0.5
        $T2 = $T * $T;
        $T3 = $T2 * $T;
        $dr = M_PI / 180;
        $Jd1 = 2415020.75933 + 29.53058868 * $k + 0.0001178 * $T2 - 0.000000155 * $T3;
        $Jd1 = $Jd1 + 0.00033 * sin( ( 166.56 + 132.87 * $T - 0.009173 * $T2 ) * $dr); // Mean new moon
        $M = 359.2242 + 29.10535608 * $k - 0.0000333 * $T2 - 0.00000347 * $T3; // Sun's mean anomaly
        $Mpr = 306.0253 + 385.81691806 * $k + 0.0107306 * $T2 + 0.00001236 * $T3; // Moon's mean anomaly
        $F = 21.2964 + 390.67050646 * $k - 0.0016528 * $T2 - 0.00000239 * $T3; // Moon's argument of latitude
        $C1 = ( 0.1734 - 0.000393 * $T ) * sin( $M * $dr ) + 0.0021 * sin( 2 * $dr * $M );
        $C1 = $C1 - 0.4068 * sin( $Mpr * $dr ) + 0.0161 * sin( $dr * 2 * $Mpr);
        $C1 = $C1 - 0.0004 * sin( $dr * 3 * $Mpr);
        $C1 = $C1 + 0.0104 * sin( $dr * 2 * $F ) - 0.0051 * sin( $dr * ( $M + $Mpr));
        $C1 = $C1 - 0.0074 * sin( $dr * ( $M - $Mpr ) ) + 0.0004 * sin( $dr * ( 2 * $F + $M ));
        $C1 = $C1 - 0.0004 * sin( $dr * ( 2 * $F - $M ) ) - 0.0006 * sin( $dr * ( 2 * $F + $Mpr ));
        $C1 = $C1 + 0.0010 * sin( $dr * ( 2 * $F - $Mpr ) ) + 0.0005 * sin( $dr * ( 2 * $Mpr + $M ));
        if ( $T < -11 )
        {
            $deltat = 0.001 + 0.000839 * $T + 0.0002261 * $T2 - 0.00000845 * $T3 - 0.000000081 * $T * $T3 ;
        }
        else
        {
            $deltat = -0.000278 + 0.000265 * $T + 0.000262 * $T2;
        }

        $JdNew = $Jd1 + $C1 - $deltat;
        //echo "JdNew = $JdNew\n";
        return $this::INT( $JdNew + 0.5 + $timeZone / 24 );
    }

    public function getSunLongitude( $jdn, $timeZone )
    {
        $T = ( $jdn - 2451545.5 - $timeZone / 24 ) / 36525; // Time in Julian centuries from 2000-01-01 12:00:00 GMT
        $T2 = $T * $T;
        $dr = M_PI / 180; // degree to radian
        $M = 357.52910 + 35999.05030 * $T - 0.0001559 * $T2 - 0.00000048 * $T * $T2; // mean anomaly, degree
        $L0 = 280.46645 + 36000.76983 * $T + 0.0003032 * $T2; // mean longitude, degree
        $DL = ( 1.914600 - 0.004817 * $T - 0.000014 * $T2 ) * sin( $dr * $M );
        $DL = $DL + ( 0.019993 - 0.000101 * $T ) * sin( $dr * 2 * $M ) + 0.000290 * sin( $dr * 3 * $M );
        $L = $L0 + $DL; // true longitude, degree
        //echo "\ndr = $dr, M = $M, T = $T, DL = $DL, L = $L, L0 = $L0\n";
        // obtain apparent longitude by correcting for nutation and aberration
        $omega = 125.04 - 1934.136 * $T;
        $L = $L - 0.00569 - 0.00478 * sin( $omega * $dr );
        $L = $L * $dr;
        $L = $L - M_PI * 2 * ( $this::INT( $L / ( M_PI * 2 ) ) ); // Normalize to (0, 2*PI)
        return $this::INT( $L / M_PI * 6 );
    }

    public function getLunarMonth11( $yy, $timeZone )
    {
        $off = $this->jdFromDate( 31, 12, $yy ) - 2415021;
        $k = $this::INT( $off / 29.530588853 );
        $nm = $this::getNewMoonDay( $k, $timeZone );
        $sunLong = $this::getSunLongitude( $nm, $timeZone ); // sun longitude at local midnight
        if ( $sunLong >= 9 )
        {
            $nm = $this::getNewMoonDay( $k - 1, $timeZone );
        }
        return $nm;
    }

    public function getLeapMonthOffset( $a11, $timeZone )
    {
        $k = $this::INT( ( $a11 - 2415021.076998695 ) / 29.530588853 + 0.5 );
        $last = 0;
        $i = 1; // We start with the month following lunar month 11
        $arc = $this::getSunLongitude( $this::getNewMoonDay( $k + $i, $timeZone ), $timeZone );
        do
        {
            $last = $arc;
            $i = $i + 1;
            $arc = $this::getSunLongitude( $this::getNewMoonDay( $k + $i, $timeZone ), $timeZone );
        }
        while ( $arc != $last && $i < 14 );
        return $i - 1 ;
    }

    /* Comvert solar date dd/mm/yyyy to the corresponding lunar date */
    public function convertSolar2Lunar( $dd, $mm, $yy, $timeZone )
    {
        $dayNumber = $this::jdFromDate( $dd, $mm, $yy );
        $k = $this::INT( ( $dayNumber - 2415021.076998695 ) / 29.530588853 );
        $monthStart = $this::getNewMoonDay( $k + 1, $timeZone );
        if ($monthStart > $dayNumber)
        {
            $monthStart = $this::getNewMoonDay( $k, $timeZone );
        }
        $a11 = $this::getLunarMonth11( $yy, $timeZone ) ;
        $b11 = $a11 ;
        if ( $a11 >= $monthStart )
        {
            $lunarYear = $yy;
            $a11 = $this::getLunarMonth11( $yy - 1, $timeZone );
        }
        else
        {
            $lunarYear = $yy + 1;
            $b11 = $this::getLunarMonth11( $yy + 1, $timeZone );
        }
        $lunarDay = $dayNumber - $monthStart + 1 ;
        $diff = $this::INT( ( $monthStart - $a11 ) / 29 ) ;
        $lunarLeap = 0 ;
        $lunarMonth = $diff + 11 ;
        if ( $b11 - $a11 > 365 )
        {
            $leapMonthDiff = $this::getLeapMonthOffset( $a11, $timeZone ) ;
            if ( $diff >= $leapMonthDiff )
            {
                $lunarMonth = $diff + 10 ;
                if ( $diff == $leapMonthDiff )
                {
                    $lunarLeap = 1 ;
                }
            }
        }
        if ( $lunarMonth > 12 )
        {
            $lunarMonth = $lunarMonth - 12 ;
        }
        if ( $lunarMonth >= 11 && $diff < 4 )
        {
            $lunarYear -= 1 ;
        }
        return array(
            $lunarDay,
            $lunarMonth,
            $lunarYear,
            $lunarLeap ) ;
    }

    /* Convert a lunar date to the corresponding solar date */
    public function convertLunar2Solar( $lunarDay, $lunarMonth, $lunarYear, $lunarLeap, $timeZone )
    {
        if ( $lunarMonth < 11 )
        {
            $a11 = $this::getLunarMonth11( $lunarYear - 1, $timeZone ) ;
            $b11 = $this::getLunarMonth11( $lunarYear, $timeZone ) ;
        }
        else
        {
            $a11 = $this::getLunarMonth11( $lunarYear, $timeZone ) ;
            $b11 = $this::getLunarMonth11( $lunarYear + 1, $timeZone ) ;
        }
        $k = $this::INT( 0.5 + ( $a11 - 2415021.076998695 ) / 29.530588853 ) ;
        $off = $lunarMonth - 11 ;
        if ( $off < 0 )
        {
            $off += 12 ;
        }
        if ( $b11 - $a11 > 365 )
        {
            $leapOff = $this::getLeapMonthOffset( $a11, $timeZone ) ;
            $leapMonth = $leapOff - 2 ;
            if ( $leapMonth < 0 )
            {
                $leapMonth += 12 ;
            }
            if ( $lunarLeap != 0 && $lunarMonth != $leapMonth )
            {
                return array(
                    0,
                    0,
                    0 ) ;
            }
            else
                if ( $lunarLeap != 0 || $off >= $leapOff )
                {
                    $off += 1 ;
                }
        }
        $monthStart = $this::getNewMoonDay( $k + $off, $timeZone ) ;
        return $this::jdToDate( $monthStart + $lunarDay - 1 ) ;
    }


    //tính can chi ngày
    public function getNgayCanChi($dateDuongLich = null){
        if(empty($dateDuongLich)) $dateDuongLich = date('Y-m-d');
        $day = date('d',strtotime($dateDuongLich));
        $month = date('m',strtotime($dateDuongLich));
        $year = date('Y',strtotime($dateDuongLich));
        $a = floor((14 - $month) / 12);
        $y = $year+4800-$a;
        $m = $month+12*$a-3;
        $JDN = $day + floor((153*$m+2)/5) + 365*$y + floor($y/4) - floor($y/100) + floor($y/400) - 32045;
        if ($JDN < 2299161) {
            $JDN = $day + floor((153 * $m + 2) / 5) + 365 * $y + floor($y / 4) - 32083;
        }
        $JDN = floor($JDN);
        $list_thien_can = ["Giáp","Ất","Bính","Đinh","Mậu","Kỷ","Canh","Tân","Nhâm","Quý"];
        $list_dia_chi = ["Tý", "Sửu","Dần", "Mão", "Thìn", "Tỵ", "Ngọ", "Mùi", "Thân", "Dậu", "Tuất","Hợi"];
        return $list_thien_can[($JDN + 9) % 10] . " " . $list_dia_chi[($JDN + 1) % 12];
    }

    //tính can chi giờ
    public function getGioCanChi($dateDuongLich = null){
        if(empty($dateDuongLich)) $dateDuongLich = date('Y-m-d');
        if(strtotime($dateDuongLich) != strtotime(date('Y-m-d'))) return false;
        $hour = date('H');
        $day = date('d',strtotime($dateDuongLich));
        $month = date('m',strtotime($dateDuongLich));
        $year = date('Y',strtotime($dateDuongLich));
        $a = floor((14 - $month) / 12);
        $y = $year+4800-$a;
        $m = $month+12*$a-3;
        $JDN = $day + floor((153*$m+2)/5) + 365*$y + floor($y/4) - floor($y/100) + floor($y/400) - 32045;
        if ($JDN < 2299161) {
            $JDN = $day + floor((153 * $m + 2) / 5) + 365 * $y + floor($y / 4) - 32083;
        }
        $JDN = floor($JDN);
        $list_gio = [23,1,3,5,7,9,11,13,15,17,19,21];
        $list_thien_can = ["Giáp","Ất","Bính","Đinh","Mậu","Kỷ","Canh","Tân","Nhâm","Quý"];
        $list_dia_chi = ["Tý", "Sửu", "Dần", "Mão", "Thìn", "Tỵ", "Ngọ", "Mùi", "Thân", "Dậu", "Tuất", "Hợi"];
        if($hour % 2 == 0) $hour = $hour - 1;
        if($hour == -1) $hour = 23;
        $ngay_thien_can = $list_thien_can[($JDN + 9) % 10];
        $keyHour = array_search($hour,$list_gio);
        switch ($ngay_thien_can){
            case "Giáp": case "Kỷ":
            $list_gio_thien_can = ["Giáp","Ất","Bính","Đinh","Mậu","Kỷ","Canh","Tân","Nhâm","Quý","Giáp","Ất"];
            break;
            case "Ất": case "Canh":
            $list_gio_thien_can = ["Bính","Đinh","Mậu","Kỷ","Canh","Tân","Nhâm","Quý","Giáp","Ất","Bính","Đinh"];
            break;
            case "Bính": case "Tân":
            $list_gio_thien_can = ["Mậu","Kỷ","Canh","Tân","Nhâm","Quý","Giáp","Ất","Bính","Đinh","Mậu","Kỷ"];
            break;
            case "Đinh": case "Nhâm":
            $list_gio_thien_can = ["Canh","Tân","Nhâm","Quý","Giáp","Ất","Bính","Đinh","Mậu","Kỷ","Canh","Tân"];
            break;
            case "Mậu": case "Quý":
            $list_gio_thien_can = ["Nhâm","Quý","Giáp","Ất","Bính","Đinh","Mậu","Kỷ","Canh","Tân","Nhâm","Quý"];
            break;
            default: $list_gio_thien_can = [];
        }
        return  $list_gio_thien_can[$keyHour] ." " . $list_dia_chi[$keyHour];
    }

    //Tính can chi tháng
    public function getThangCanChi($dateDuongLich = null) {
        if (empty($dateDuongLich)) $dateDuongLich = time();
        else $dateDuongLich = strtotime($dateDuongLich);

        $year = date('Y',$dateDuongLich);
        $month = date('m',$dateDuongLich);
        $day = date('d',$dateDuongLich);
        $thisDayLunar = $this->convertSolar2Lunar($day, $month, $year, 7);
        $can = ['Canh', 'Tân', 'Nhâm', 'Quý', 'Giáp', 'Ất', 'Bính', 'Đinh', 'Mậu', 'Kỷ'];
        $chi = ['Dần', 'Mão', 'Thìn', 'Tỵ', 'Ngọ', 'Mùi', 'Thân', 'Dậu', 'Tuất', 'Hợi', 'Tý', 'Sửu'];
        $indexChi = (int) $thisDayLunar[1] - 1;
        $indexCan = ((((int) $thisDayLunar[2] % 5 ) * 2 + 8) % 10 + $indexChi) % 10;
        return $can[$indexCan] . ' ' . $chi[$indexChi];
    }


    //Tính can chi năm
    public function getNamCanChi($dateDuongLich = null) {
        if(empty($dateDuongLich)) $dateDuongLich = date('Y-m-d');
        $year_am_lich = date('Y',strtotime($this->getLunarFromDay($dateDuongLich)));
        if($year_am_lich >= 2020) $year_am_lich = $year_am_lich - 1960;
        $oneNumberLast = substr($year_am_lich, -1);
        $twoNumberLast = substr($year_am_lich, -2);
        $list_thien_can = ["Canh","Tân","Nhâm","Quý","Giáp","Ất","Bính","Đinh","Mậu","Kỷ"];
        $list_dia_chi = ["Tý", "Sửu", "Dần", "Mão", "Thìn", "Tỵ", "Ngọ", "Mùi", "Thân", "Dậu", "Tuất","Hợi"];
        return $list_thien_can[$oneNumberLast] . " " . $list_dia_chi[$twoNumberLast%12];
    }

    //tính địa chi ngày
    public function getNgayDiaChi($dateDuongLich = null){
        if(empty($dateDuongLich)) $dateDuongLich = date('Y-m-d');
        $day = date('d',strtotime($dateDuongLich));
        $month = date('m',strtotime($dateDuongLich));
        $year = date('Y',strtotime($dateDuongLich));
        $a = floor((14 - $month) / 12);
        $y = $year+4800-$a;
        $m = $month+12*$a-3;
        $JDN = $day + floor((153*$m+2)/5) + 365*$y + floor($y/4) - floor($y/100) + floor($y/400) - 32045;
        if ($JDN < 2299161) {
            $JDN = $day + floor((153 * $m + 2) / 5) + 365 * $y + floor($y / 4) - 32083;
        }
        $JDN = floor($JDN);
        $list_dia_chi = ["Tý", "Sửu","Dần", "Mão", "Thìn", "Tỵ", "Ngọ", "Mùi", "Thân", "Dậu", "Tuất","Hợi"];
        return $list_dia_chi[($JDN + 1) % 12];
    }

    //Tính thứ trong tuần
    public function getThu($date = null) {
        if (empty($date)) {
            $date = date('Y-m-d');
        }
        $day_of_week = date('N',strtotime($date));
        switch ($day_of_week) {
            case 1 : $day = 'Thứ hai'; break;
            case 2 : $day = 'Thứ ba'; break;
            case 3 : $day = 'Thứ tư'; break;
            case 4 : $day = 'Thứ năm'; break;
            case 5 : $day = 'Thứ sáu'; break;
            case 6 : $day = 'Thứ bảy'; break;
            default : $day = 'Chủ nhật';
        }
        return $day;
    }

}
?>