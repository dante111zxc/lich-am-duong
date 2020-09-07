<?php

/*
Plugin Name: Lịch Âm Dương
Description: Lịch âm dương, chuyển đổi ngày âm sang ngày dương, xem ngày âm lịch, xem giờ hoàng đạo, hắc đạo
Author: Dante
Version: 1.0
*/
/**
 * @package Lịch âm dương
 * @version 1.0
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
define( 'LAD__FILE__', __FILE__ );
define( 'LAD__DIR__', plugin_dir_path( __FILE__ ) );
define( 'LAD_URL__', plugin_dir_url( __FILE__ ) );
define( 'LAD__VERSION', '1.0' );

require LAD__DIR__ . 'Lunar2solar.php';
require LAD__DIR__ . 'function.php';
require LAD__DIR__ . 'widget.php';

function lad_creat_table (){
    global $wpdb;
    $table_name = $wpdb->prefix . 'data_gio_hoang_dao';
    $charset_collate  = $wpdb->get_charset_collate();

    //creat table
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      ngay_can_chi varchar(255) NOT NULL,
      noi_dung longtext NOT NULL,
      created_time datetime DEFAULT current_timestamp NOT NULL,
      updated_time datetime DEFAULT current_timestamp ON UPDATE current_timestamp NOT NULL,
      PRIMARY KEY  (id),
      FULLTEXT KEY `FULLTEXT` (`ngay_can_chi`)
    ) $charset_collate;";
    $wpdb->query($sql);
    $success = empty($wpdb->last_error);
    return $success;
}
function lad_insert_data (){
    global $wpdb;
    $table_name = $wpdb->prefix . 'data_gio_hoang_dao';

    $data_json = file_get_contents(LAD__DIR__ . 'wp_data_gio_hoang_dao.json');
    $data = json_decode($data_json);
    if (!empty($data)) {
        foreach ($data as $item) {
            $wpdb->insert($table_name, (array) $item);
            /*$wpdb->insert_id*/
        }
    }
}
function lad_remove_table (){
    global $wpdb;
    $table_name = $wpdb->prefix . 'data_gio_hoang_dao';
    $sql = "DROP TABLE IF EXISTS $table_name";
    $wpdb->query($sql);
    $success = empty($wpdb->last_error);
    return $success;
}
register_activation_hook( __FILE__, 'lad_creat_table');
register_activation_hook(__FILE__, 'lad_insert_data');
register_uninstall_hook( __FILE__, 'lad_remove_table' );

if (!function_exists('lad_load_script')) {
    function lad_load_script (){
        wp_enqueue_style( 'lad-style', LAD_URL__ . 'css/style.css' );
        wp_enqueue_script( 'lad-script', LAD_URL__ . 'js/custom.js', '', '', true );
        wp_localize_script( 'lad-script', 'lad', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
        ) );
    }
    add_action( 'wp_enqueue_scripts', 'lad_load_script' );
}


if ( ! function_exists( 'get_data' ) ) {
    function get_data() {
        if ( isset($_POST) && isset($_POST['query']) )  {
            $query = [];
            parse_str($_POST['query'], $query);
            $day = $query['day'];
            $month = $query['month'];
            $year = $query['year'];

            //1: Dương lịch -> âm lịch
            //2: Âm lịch -> dương lịch
            $type = $query['type'];


            switch ($type) {
                case 1:
                    $result = getDataLunarDay("$year-$month-$day");
                    $result['type'] = (int) $type;
                    break;
                case 2:
                    $result = getDataSolarDay("$year-$month-$day");
                    $result['type'] = (int) $type;
                    break;
            }

            wp_send_json_success($result);
        } else {
            wp_send_json_error('Err');
        }
        wp_die();
    }

    // Load lunar month ajax
    add_action( 'wp_ajax_get_data', 'get_data' );
    add_action( 'wp_ajax_nopriv_get_data', 'get_data' );
}

//lấy dữ liệu ngày tháng âm lịch -> dương lịch
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


//lấy dữ liệu ngày tháng dương lịch -> âm lịch
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