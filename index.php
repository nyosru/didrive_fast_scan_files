<?php

ini_set('display_errors', 'On'); // сообщения с ошибками будут показываться
error_reporting(E_ALL); // E_ALL - отображаем ВСЕ ошибки
//date_default_timezone_set("Asia/Yekaterinburg");
date_default_timezone_set("Asia/Omsk");
define('IN_NYOS_PROJECT', true);

require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require $_SERVER['DOCUMENT_ROOT'] . '/all/ajax.start.php';

class T1 {

    public static $timer = 0;
    public static $all_size = 0;
    public static $size_to_delete = 0;
    public static $delete_file = false;
    public static $show = false;

    public static function clear() {
        self::$timer = 0;
        self::$all_size = 0;
        self::$size_to_delete = 0;
        self::$delete_file = false;
        self::$show = false;
    }

    public static function scanFiles(string $dir, int $max_day) {

        T1::$timer = $_SERVER['REQUEST_TIME'] - 3600 * 24 * $max_day;

        $dirs = glob($dir . DS . '*');

        $nn = 1;

        foreach ($dirs as $dir) {

            if (self::$show === true)
                echo '<br/>' . $dir;

            // ---------- 1 ---------------
            // \f\pa( glob( $dir . DS .'*' ) , 2 );
            // ----------- 2 ----------------

            $ar = array_filter(glob($dir . DS . '*'), function( $v ) {

                // return filectime( $dir . DS . $v ) > $time_olded ;
                // return ( file_exists( $v ) && filectime( $v ) < T1::$timer );
                T1::$all_size += filesize($v);
                if (file_exists($v) && filectime($v) < T1::$timer) {

                    T1::$size_to_delete += filesize($v);

                    if (self::$delete_file === true) {
                        unlink($v);
                        if (self::$show === true)
                            echo '<br/>del' . $v;
                    }

                    return true;
                } else {
                    return false;
                }
            });

            if (self::$show === true) {
                echo '<Br/>файлов удаляем ' . sizeof($ar);
                echo '<Br/>to delete ' . round(T1::$size_to_delete / 1024 / 1024, 1) . 'Mb';
                echo '<Br/>all ' . round(T1::$all_size / 1024 / 1024, 1) . 'Mb / ' . round(T1::$all_size / 1024 / 1024 / 1024, 1) . 'Gb';
            }

            $nn++;
        }

        return \f\end3('ok', true, [
            'all_mb' => round(T1::$all_size / 1024 / 1024, 1)
            , 'to_delete_mb' => round(T1::$size_to_delete / 1024 / 1024, 1)
        ]);
    }

}

\f\timer_start(22);

$need_free_md = 25000;
$msg = 'ищем и удаляем старые фотки,'.PHP_EOL.'чтобы было занято до ' . number_format($need_free_md,1,'.','`') . ' Mb';

for ($i = 0; $i <= 10; $i++) {

    T1::clear();
    $we = T1::scanFiles(DR . DS . 'data_photo', ( 14 - $i));
    // \f\pa($we);

    if ($need_free_md >= ( $we['data']['all_mb'] - $we['data']['to_delete_mb'] )) {

        $days = PHP_EOL.'нашли нужный уровень очистки, оставляем дней ' . ( 14 - $i );
        
        if (T1::$show === true)
        echo '<br/>' . $days;

        $msg .= $days
                . '<br/>после удаления будет занято: ' 
                . number_format( $we['data']['all_mb'] ,1,'.','`') 
                .' - '. number_format( $we['data']['to_delete_mb'] ,1,'.','`')
                .' = '. number_format( ( $we['data']['all_mb'] - $we['data']['to_delete_mb'] ),1,'.','`') . ' Mb';

        T1::$delete_file = true;
        // T1::$show = true;
        $we = T1::scanFiles(DR . DS . 'data_photo', ( 14 - $i));
        break;
    }
}

$timer = \f\timer_stop(22);
// echo '<br/>таймер1: ' . $timer;
$msg .= '<Br/>timer ' . $timer;

try {
//if (!empty($msg) && 1 == 1 && class_exists('\\Nyos\\Msg')) {
//
//    if (!isset($vv['admin_ajax_job']))
//        require_once DR . '/sites/' . \Nyos\nyos::$folder_now . '/config.php';

    \nyos\Msg::sendTelegramm($msg, null, 2);

//    if (!empty($vv['admin_ajax_job'])) {
//        foreach ($vv['admin_ajax_job'] as $k => $v) {
//            \Nyos\Msg::sendTelegramm($msg, $v);
//        }
//    }
//}
} catch (\Exception $exc) {
    \f\pa($exc);
}


die( '<br/>закончили');

//$ee = disk_total_space("/");
//\f\pa($ee);
//\f\pa(round($ee/1024/1024,1).'gb');
// \f\pa(glob( "$folder/*" ));
// \f\pa(glob( DR . DS . 'data_photo' . DS .'*' ));
// $dirs = glob(DR . DS . 'data_photo' . DS . '*');

$we = T1::scanFiles(DR . DS . 'data_photo',  13);
        \f\pa($we);

//echo '<Br/>10: ' . round(T1::$all_size / 1024 / 1024, 1) . 'Mb';
//echo '<Br/>10-delete: ' . round(T1::$size_to_delete / 1024 / 1024, 1) . 'Mb';
//echo '<Br/>10-after: ' . round(( T1::$all_size - T1::$size_to_delete ) / 1024 / 1024, 1) . 'Mb';


echo '<Br/>';
echo '<Br/>';
echo '<Br/>';
echo '<Br/>';

T1::clear();
$we = T1::scanFiles(DR . DS . 'data_photo',  12);
        \f\pa($we);
echo '<br/>таймер1: ' . \f\timer_stop(22);

//echo '<Br/>11: ' . round(T1::$all_size / 1024 / 1024, 1) . 'Mb';
//echo '<Br/>11-delete: ' . round(T1::$size_to_delete / 1024 / 1024, 1) . 'Mb';
//echo '<Br/>11-after: ' . round(( T1::$all_size - T1::$size_to_delete ) / 1024 / 1024, 1) . 'Mb';









exit;

//function folder_has_older_file($folder, $time)
//{
//return count(
//        array_filter( 
//                array_map( 'filemtime', glob( "$folder/*" ) )
//                , function ($a) use ($time) { return $a < $time; }
//                )
//        ) > 0;
//}

/**
 * удаляем старые файлы и те что не влазят в ограничение по максимальному размеру хранилища
 */
$t = \f\File::deleteOldTimeAndAllSize(DR . DS . 'data_photo' . DS,  14,( 25 * 1024 * 1024 * 1024 ) );

if (!empty($t['msg']) && 1 == 1 && class_exists ( '\\Nyos\\Msg')) {

if (!isset($vv['admin_ajax_job']))
    require_once DR . '/sites/' . \Nyos\nyos::$folder_now . '/config.php';

\nyos\Msg::sendTelegramm($t['msg'], null, 1 );

if (!empty($vv['admin_ajax_job'])) {
foreach ($vv['admin_ajax_job'] as $k  => $v)  {
\Nyos\Msg::sendTelegramm($t['msg'], $v);
}
}
}

die();

// 191222 обновил до того что выше
if (1 == 2 ) {

$day_to_old_photo = 14;
$deletetime = strtotime(date('Y-m-d 09:00:00', $_SERVER['REQUEST_TIME']) . ' -' . $day_to_old_photo . ' day' );

$e = \f\deleteFileOverDate(DR . DS . 'data_photo',  $deletetime);

\f\pa($e);

$e = \f\deleteFileOverSize(DR . DS . 'data_photo',  28.5 * 1024 * 1024 * 1024 );
$txt2 .= PHP_EOL . '----------';
$txt2 .= PHP_EOL . 'удалили ' . $kolvo_f . ' файла(ов) > ' . $del_size2 . ' мб';
\f\pa($e);
die();







if (strlen($txt2) > 10 && 1 == 1 && class_exists ( '\\Nyos\\Msg')) {

if (!isset($vv['admin_ajax_job'])) {
require_once DR . '/sites/' . \Nyos\nyos::$folder_now . '/config.php';
}

// $txt3 = 'файлы для удаления с 02:00 - 09:00 ' . PHP_EOL . substr($txt2, 0, 1800) . ' ... ' . PHP_EOL . ' общий размер ' . $mb_day_delete . ' Мб';

\nyos\Msg::sendTelegramm($txt2, null, 1 );

if (isset($vv['admin_ajax_job'])) {
foreach ($vv['admin_ajax_job'] as $k  => $v)  {
\nyos\Msg::sendTelegramm($txt2, $v);
//\Nyos\NyosMsg::sendTelegramm('Вход в управление ' . PHP_EOL . PHP_EOL . $e, $k );
}
}
}






if (1 == 2 ) {


$txt2 = 'удаляем фотки что лежат дольше срока хранения';

$day_to_old_photo = 14;

$dir = $_SERVER['DOCUMENT_ROOT'] . '/data_photo/';

$deletetime = strtotime(date('Y-m-d', $_SERVER['REQUEST_TIME']) . ' -' . $day_to_old_photo . ' day' );

//echo '<br/>' . date('d-m-Y', $deletetime);
//echo '<br/>------';

$d1 = scandir($dir);

$del_size = 0;
$kolvo_f = 0;

//$nn = 0;

foreach ($d1 as $k1  => $v1 ) {

//    if ($nn > 10)
//        break;

if ($v1 != '.' && $v1 != '..' && is_dir ( $dir . $v1 ) ) {

//        echo '<Br/>22 ' . $dir . $v1;

$d = scandir($dir . $v1 );

// \f\pa($d,2,'','$d');

foreach ($d as $k  => $v)  {

//            if ($nn > 10)
//                break;

$file2 = $dir . $v1 . '/' . $v;

if ($v != '.' && $v != '..' && file_exists ( $file2)) {

$ft = filemtime($file2);

if ($ft <= $deletetime ) {

//                    echo '<br/>' . $dir . $v1 . '/' . $v;
//                    echo '<br/>' . date('d-m-Y', $ft);

$del_size += filesize($file2);
$kolvo_f++;
unlink($file2);

//                    $nn ++;
}
}
}
}
}

$del_size2 = round($del_size / 1024 / 1024, 1 );
echo 'файлов удалено ' . $kolvo_f;
echo '<br/>' . $del_size2 . ' Мб';

$txt2 .= PHP_EOL . 'удалили ' . $kolvo_f . ' файла(ов) > ' . $del_size2 . ' мб';

if (strlen($txt2) > 10 && 1 == 1 && class_exists ( '\\Nyos\\Msg')) {

if (!isset($vv['admin_ajax_job'])) {
require_once DR . '/sites/' . \Nyos\nyos::$folder_now . '/config.php';
}

// $txt3 = 'файлы для удаления с 02:00 - 09:00 ' . PHP_EOL . substr($txt2, 0, 1800) . ' ... ' . PHP_EOL . ' общий размер ' . $mb_day_delete . ' Мб';

\nyos\Msg::sendTelegramm($txt2, null, 1 );

if (isset($vv['admin_ajax_job'])) {
foreach ($vv['admin_ajax_job'] as $k  => $v)  {
\nyos\Msg::sendTelegramm($txt2, $v);
//\Nyos\NyosMsg::sendTelegramm('Вход в управление ' . PHP_EOL . PHP_EOL . $e, $k );
}
}
}
}
}
