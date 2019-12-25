<?php


function badd($left_operand, $right_operand)
{

    return bcadd($left_operand, $right_operand, 4);
}

function bsub($left_operand, $right_operand)
{

    return bcsub($left_operand, $right_operand, 4);
}

function bmul($left_operand, $right_operand, $scale = '4')
{
    return bcmul($left_operand, $right_operand, $scale);
}

function bdiv($left_operand, $right_operand, $scale = '4')
{
    return bcdiv($left_operand, $right_operand, $scale);
}

function bcomp($left_operand, $right_operand, $scale = '4')
{
    return bccomp($left_operand, $right_operand, $scale);
}


function get_conf()
{
    $conf = \App\Models\Config::first()->toArray();
    return $conf;
}

function str_phone($str)
{
    return substr_replace($str, '****', 4, 4);
}


function str_xing($str)
{

    $len = mb_strlen($str, 'utf-8');
    if ($len == 2) {
        return mb_substr($str, 0, 1, 'utf-8') . '*';
    } else {
        $str2 = mb_substr($str, -1, 1, 'utf-8');
    }


    $xing = '';
    for ($i = 0; $i < ($len - 2); $i++) {
        $xing .= '*';
    }

    return mb_substr($str, 0, 1, 'utf-8') . $xing . $str2;
}

function order_number()
{
    static $ORDERSN = array();                                        //静态变量
    $ors = date('ymd') . substr(time(), -5) . substr(microtime(), 2, 5);     //生成16位数字基本号
    if (isset($ORDERSN[$ors])) {                                    //判断是否有基本订单号
        $ORDERSN[$ors]++;                                           //如果存在,将值自增1
    } else {
        $ORDERSN[$ors] = 1;
    }
    return $ors . str_pad($ORDERSN[$ors], 2, '0', STR_PAD_LEFT);     //链接字符串
}

/*
 *  缓存并发送紧急短息
 */
function sendErr($message)
{
    if (!\Illuminate\Support\Facades\Cache::has('error')) {
        app(\App\Services\SmsService::class)->sendSMSTemplate('14836549', [13379246424], [$message]);
        \Illuminate\Support\Facades\Cache::put('error', 1, 60);
    }
}


function getMonthDays($month = "this month", $format = "Y-m-d")
{
    $start = strtotime("first day of $month");
    $end = strtotime("last day of $month");

    for ($i = $start; $i <= $end; $i += 24 * 3600) {

        if ($i == $start) {
            $start = $i;
        }
        $stop = $i;
    }

    return ['start' => $start, 'end' => $stop];
}