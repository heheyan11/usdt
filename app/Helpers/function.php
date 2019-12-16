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


function get_conf()
{
    $conf = \App\Models\Config::first()->toArray();
    return $conf;
}

function str_phone($str){
    return substr_replace($str,'****',4,4);
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