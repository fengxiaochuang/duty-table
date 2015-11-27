<?php
$duty = array('第一人','第二人','第三人','第四人','第五人','第六人');
$duty[-1] = '休息日';
$duty[-2] = '节假日';
$duty[-3] = '接口错误';

$filename = "last.json";
$last = file_get_contents($filename);
$nextMonthFirstDay = getNextMonthFirstDay();
$nextMonthFirstDay_date = date('Ymd', $nextMonthFirstDay);
$curMonthFirstDay = getCurMonthFirstDay();
$curMonthFirstDay_date = date('Ymd', $curMonthFirstDay);

if (empty($last)) {
    $list = create_json();
    $need_write = 1;
}else{
    $list = json_decode($last,true);
    // 判断下个月存在不存在
    if (!isset($list[$nextMonthFirstDay_date])) {
        $need_write = 1;
        $list = create_json();
    }
}
if (isset($need_write)) {
    echo "写入";
    file_put_contents($filename, json_encode($list));
}
$date_list = [];
foreach ($list as $key => $value) {
    $tmpdata = array(
        'title' =>  $duty[$value],
        'start' =>  date('Y-m-d',strtotime($key)),
        'className' =>  'event-text',
        );
    if ($value == -1) {
        // $tmpdata['rendering'] = 'background';
        // $tmpdata['color'] = '#ff9f89';
    }
    if ($value == -2) {
        $tmpdata['rendering'] = 'background';
        $tmpdata['color'] = '#ff9f89';
    }
    $data_list[] = $tmpdata;
}
?>
<!DOCTYPE html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<link href="./fullcalendar.css" rel="stylesheet">
<link href="./fullcalendar.print.css" rel="stylesheet" media="print">
<script src="./moment.min.js"></script><style type="text/css" adt="123"></style>
<script src="./jquery.min.js"></script>
<script src="./fullcalendar.min.js"></script>
<style>
    .box{
        width: 1000px;
        margin: 0 auto;
    }
    #calendar {
        max-width: 900px;
        margin: 0 auto;
    }
    .event-text{
        color: #000;
        font-size: 22px;
        text-align: center;
        font-size: 22px;
        background: none;
        border:none;
    }
    .event-text:hover{
        color: #000;
    }
    .fc-body .fc-day{
        background-color: #fcf8e3;
    }
    .fc-bg .fc-day.fc-other-month{
        background-color: #F1F1F1;
    }
    td.fc-day-number {
        font-size: 40px;
        position: relative;
        top: 10px;
        color: #949494;
        font-weight: bold;
    }
    td.fc-day.fc-widget-content.fc-fri.fc-today.fc-state-highlight {
        background-color: #CDD4FF;
    }
</style>
</head>
<body>
    <div id="calendar"></div>
</body>
<script>
    $(document).ready(function() {

        $('#calendar').fullCalendar({
            header: {
                left: 'prev, today',
                center: 'title',
                right: 'next,'
                // right: 'month,agendaWeek,agendaDay'
            },
            defaultDate: '2015-11-12',
            businessHours: true,
            editable: false,
            events: <?php echo json_encode($data_list)?>,
            monthNames: ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月'],
            monthNamesShort: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
            dayNames: ['星期日', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六'],
            dayNamesShort: ['周日', '周一', '周二', '周三', '周四', '周五', '周六'],
            titleFormat: {
                month: 'YYYY年 MM月'
            },
            buttonText: {
                today: '今天',
                month: '月',
                week: '周',
                day: '天'
            }
        });
    });

</script>
</html>
<?
// $last = array('20151101'=>0);
// if ($need_write) {
//     echo "准备写入";
// }
// $date_list = aftoday();
// var_dump($date_list);
// 定义一个20151101的定量0


// $last_date = strtotime($last[0]);
// $last_duty = $last[1];
// $now = strtotime('today');
// $today = date("Ymd",$now);

// $diff_d = intval(($now - $last_date)/86400);
// $getd = get2day($last[0],$today);
// $days = $diff_d-$getd+$last[1];
// if ($diff_d > $getd) {
//     $days = $diff_d-$getd+$last[1];
//     $s_duty = $days%count($duty);
// }elseif ($diff_d == $getd){
//     $s_duty = $last[1];
// }
// $s_duty = $last[1];
// 写入文件
// $fwrite = array($today,$s_duty);
// file_put_contents($filename, json_encode($fwrite));
// header("Content-type: text/html; charset=utf-8");
// echo "<h1>今天是".date("Y年m月d日",$now)."</h1>";
// echo "<h1>今天值日生是".$duty[$s_duty]."</h1>";
// $CurMonthFirstDay = getCurMonthFirstDay();
// $NextMonthLastDay = getNextMonthLastDay();
// echo date("Ymd",$CurMonthFirstDay)."<br>";
// echo date("Ymd",$NextMonthLastDay);
// $date_list = aftoday();
// print_r($date_list);
function create_json(){
    // 默认20151101 是第0个人开始值日，这个是认为设定的。
    $default_date = '20151101';
    $default_time = strtotime($default_date);
    $default_duty = 0;
    $date_list = aftoday();
    $new_list = [];
    foreach ($date_list as $key => $value) {
        switch ($value) {
            case '0':
                $default_duty++;
                $tmpv = $default_duty%6;
                break;
            case '1':
                $tmpv = -1;
                break;
            case '2':
                $tmpv = -2;
                break;
            default:
                $tmpv = -3;
                break;
        }
        $new_list[$key] = $tmpv;
    }
    return $new_list;
}

// 根据两个日期生成所有日期的时间字符串
function get2day($start,$end){
    $diff_d = 0;
    $start = strtotime($start);
    $end = strtotime($end);
    $diff_inttime = round(($end-$start)/86400);
    $time_list = [];
    for ($i=0; $i < $diff_inttime; $i++) {
        $tmp_strtime = date("Ymd",$start+86400*$i);
        array_push($time_list,$tmp_strtime);
    }
    if (!empty($time_list)) {
        $get2day = implode(',',$time_list);
        $api = file_get_contents('http://www.easybots.cn/api/holiday.php?d='.$get2day);
        $api_arr = json_decode($api,true);
        foreach ($api_arr as $key => $value) {
            if ($value > 0) {
                $diff_d ++;
            }
        }
        $diff_d = 0;
    }
    return $diff_d;
}

// 返回两个月所有节假日情况
function aftoday(){
    $time_list = [];
    $CurMonthFirstDay = getCurMonthFirstDay();
    $NextMonthLastDay = getNextMonthLastDay();

    for ($i=$CurMonthFirstDay; $i <= $NextMonthLastDay; $i=$i+86400) {
        $tmp_strtime = date("Ymd",$i);
        array_push($time_list,$tmp_strtime);
    }

    $date_list = [];
    if (!empty($time_list)) {
        $get2day = implode(',',$time_list);
        $api = file_get_contents('http://www.easybots.cn/api/holiday.php?d='.$get2day);
        $date_list = json_decode($api,true);
    }
    return $date_list;
}

function getCurMonthFirstDay() {
    return strtotime(date('Y-m-01', time()));
}

function getCurMonthLastDay() {
    return strtotime(date('Y-m-d', strtotime(date('Y-m-01', time()) . ' +1 month -1 day')));
}

function getNextMonthFirstDay() {
    return strtotime(date('Y-m-d', strtotime(date('Y-m-01', time()) . ' +1 month')));
}

function getNextMonthLastDay() {
    return strtotime(date('Y-m-d', strtotime(date('Y-m-01', time()) . ' +2 month -1 day')));
}

function getPrevMonthFirstDay() {
    return strtotime(date('Y-m-d', strtotime(date('Y-m-01', time()) . ' -1 month')));
}

function getPrevMonthLastDay() {
    return strtotime(date('Y-m-d', strtotime(date('Y-m-01', time()) . ' -1 day')));
}
?>
