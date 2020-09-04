<?php
    date_default_timezone_set('Asia/Ho_Chi_Minh');
    $currentMonth = date('n', time());
    $currentYear = date('Y', time());
    $currentDay = date('j');
    $calDayInMonth = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $currentYear);


    //Lấy thứ trong tuần theo dương lịch
    $dayOfWeekSolar = lad_getThu(date('Y-m-d'));

    //Tính ngày tháng năm âm lịch
    $dateTimeLunar = lad_getLunarFromDay(date('Y-m-d'));

    //Tính can chi của ngày âm lịch
    $canChiNgay = lad_getCanChiNgay(date('Y-m-d'));

    //Tính can chi của tháng
    $canChiThang = lad_getCanChiThang(date('Y-m-d'));

    //Tinh can chi của năm âm lịch
    $canChiNam = lad_getCanChiNam(date('Y-m-d'));


?>

<form method="get" id="lichAmDuong">
    <p>
        Ngày:
        <select name="day" id="getDay">
            <option value="0">Ngày</option>
            <?php for ( $day = 1; $day <= $calDayInMonth; $day++ ) : ?>
                <option value="<?php echo  $day ?>" <?php echo ( $day === (int) $currentDay ) ? 'selected="selected"' : '' ?> ><?php echo $day ?></option>
            <?php endfor; ?>
        </select>
    </p>

    <p>
        Tháng:
        <select name="month" id="getMonth">\
            <option value="0">Tháng</option>
            <?php for ( $month = 1; $month <= 12; $month++ ) : ?>
                <option value="<?php echo $month ?>" <?php echo ( $month === (int) $currentMonth )  ? 'selected="selected"' : ''?> ><?php echo $month ?></option>
            <?php endfor; ?>
        </select>
    </p>
    <p>
        Năm:
        <select name="year" id="getYear">
            <option value="0">Năm</option>
            <?php for ( $year = 1930; $year <= 2050; $year++ ) : ?>
                <option value="<?php echo $year ?>" <?php echo ($year === (int) $currentYear ? 'selected="selected"' : '' ) ?> ><?php echo $year ?></option>
            <?php endfor; ?>
        </select>
    </p>
    <p>
        <select name="type" id="getType">
            <option value="1">Chuyến dương lịch -> âm lịch</option>
            <option value="2">Chuyển âm lịch -> dương lịch</option>
        </select>
    </p>
    <button type="submit" class="jeg_readmore">Xem kết quả</button>
</form>

<table class="tbl-lad">
    <tbody>
        <tr>
            <td colspan="2" class="lad-bg-primary">
                <span class="lad-table-title"><b id="lad-text-1">Dương lịch - âm lịch ngày <?php echo date('j/n/Y', time()) ?></b></span>
            </td>
        </tr>
        <tr>
            <td class="lad-bg-primary w-50">
                <span class="lad-table-title"><b>Dương lịch</b></span>
            </td>
            <td class="lad-bg-primary w-50">
                <span class="lad-table-title"><b>Âm lịch</b></span>
            </td>
        </tr>
        <tr>
            <td class="w-50">
                <span>
                    <b class="lad-text-2">Tháng <?php echo date('n', time()) ?> năm <?php echo date('Y', time()) ?></b>
                </span>
            </td>
            <td class="w-50">
                <span>
                    <b class="lad-text-3">Tháng <?php echo date('n', strtotime($dateTimeLunar)) ?> năm <?php echo date('Y', strtotime($dateTimeLunar)) ?> (<?php echo $canChiNam ?>)</b>
                </span>
            </td>
        </tr>
        <tr>
            <td class="w-50">
                <p style="font-size: 22px"><b class="lad-text-4"><?php echo date('j') ?></b></p>
                <p><b class="lad-text-5"><?php echo $dayOfWeekSolar; ?></b></p>
            </td class=w-50>
            <td>
                <p style="font-size: 22px"><b class="lad-text-6"><?php echo date('j', strtotime($dateTimeLunar)) ?></b></p>
                <p>
                    <span>Ngày: <b><?php echo $canChiNgay ?></b></span>
                </p>
                <p>
                    <span>Tháng: <b><?php echo $canChiThang ?></b></span>
                </p>
            </td>
        </tr>
        <tr class="lad-bg-primary">
            <td colspan="2">
                <span class="lad-table-title"><b class="lad-text-7">Giờ hoàng đạo (Giờ tốt)</b></span>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <span><b class="lad-text-8">Tý (23h-01h), Dần (03h-05h), Mão (05h-07h), Ngọ (11h-13h), Mùi (13h-15h), Dậu (17h-19h)</b></span>
            </td>
        </tr>
    </tbody>
</table>

