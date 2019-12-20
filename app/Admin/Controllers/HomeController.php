<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ChongOrder;
use App\Models\Crowdfunding;
use App\Models\LogCrow;
use App\Models\LogForm;
use App\Models\OrderCancel;
use App\Models\OrderTi;
use App\Models\User;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Echarts\Echarts;
use Encore\Admin\Widgets\InfoBox;

class HomeController extends Controller
{

    public function da($content)
    {
        return $content
            ->header('Dashboard')
            ->description('Description...')
            ->row(Dashboard::title())
            ->row(function (Row $row) {

                $row->column(12, function (Column $column) {
                    $column->append(Dashboard::environment());
                });

                /* $row->column(4, function (Column $column) {
                     $column->append(Dashboard::extensions());
                 });

                 $row->column(4, function (Column $column) {
                     $column->append(Dashboard::dependencies());
                 });*/
            });
    }

    public function index(Content $content)
    {
        //  return $this->da($content);
        // admin_toastr('sdf','success');
        $start = strtotime('-7 days');
        $order = ChongOrder::query()->where('created_at', '>=', $start)->select('amount', 'created_at', 'id')->get();

        $jsonArr = [];
        for ($i = 0; $i < 7; $i++) {
            $jsonArr[$i] = [
                'count_date' => date('m-d', $start + 86400 * ($i + 1)),
                'order_count' => 0,
                'money' => 0,
            ];
            foreach ($order as $value) {
                if ($value->created_at->timestamp >= $start + 86400 * $i && $value->created_at->timestamp < $start + (86400 * ($i + 1))) {
                    $jsonArr[$i]['money'] += $value->amount;
                    $jsonArr[$i]['order_count']++;
                }
            }
        }
        // bindData
        $head = [
            'count_date' => '日期',
            'money' => '充值额',
            'order_count' => '交易量'
        ];
        $echarts = (new Echarts('充币记录'))
            ->setData($jsonArr)
            ->bindLegend($head);


       /* $res = LogCrow::query()->select('amount','sub','send','created_at')->orderByDesc('id')->limit(6)->get();
        $arr = [];
        foreach ($res as $value){
            $arr[] = [
              'count_date' => date('m-d',$value->created_at->timestamp),
              'amount' => $value->amount,
              'sub' => $value->sub,
              'send' => $value->send,
            ];
        }

        $head = [
            'count_date' => '日期',
            'amount' => '发放数量',
            'sub' => '剩余',
            'send' => '真实发放',
        ];
        $echarts2 = (new Echarts('发放红利', ''))
            ->setSeriesType('bar')
            ->setData($arr)
            ->bindLegend($head)
            ->setDataZoom(1)
            ->setShowToolbox(1);*/

/*
        $names = '[{"name":"李花平","value":68900},{"name":"鲍奚汤·马","value":35082},{"name":"成李·苏","value":94194},{"name":"孙计","value":84937},{"name":"滕和伏","value":59329},{"name":"黄孟","value":76689},{"name":"汪苗云","value":46175},{"name":"谈谈褚","value":71813}]';

        $echarts3 = (new Echarts('饼形图'))
            ->setData(json_decode($names, 1))
            ->setSeries([
                ['type' => 'pie', 'name' => '姓名',],
            ])
            ->setSeriesType('pie');

        $echarts4 = (new Echarts('雷达图'))
            ->setIndicator([
                ['name' => '销售', 'max' => 100],
                ['name' => '管理', 'max' => 100],
                ['name' => '信息', 'max' => 100],
                ['name' => '客服', 'max' => 100],
                ['name' => '研发', 'max' => 100],
            ])
            ->setSeries([
                ['type' => 'radar',
                    'data' => [
                        [
                            'value' => [99, 86.4, 65.2, 82.5, 87],
                            'name' => 2017,
                        ],
                        [
                            'value' => [75, 76, 98, 72.4, 53.9],
                            'name' => 2016,
                        ]
                    ],
                ],

            ])
            ->setSeriesType('radar');*/

        $box = new Box('折线图', $echarts);
     //   $box2 = new Box('柱状图', $echarts2);
       /* $box3 = new Box('饼形图', $echarts3);
        $box4 = new Box('雷达图', $echarts4);*/

        return $content

            //->description(' 33')
            ->row(function (Row $row) use ($box) {
                $row->column(3, function (Column $column) {
                    $count = User::count();
                    $column->append(new InfoBox('用户', 'users', 'aqua', config('admin.route.prefix') . '/users', $count));
                });
                $row->column(3, function (Column $column) {
                    $count = ChongOrder::query()->sum('amount');
                    $column->append(new InfoBox('充值', 'shopping-cart', 'green', config('admin.route.prefix') .'/orderchong', $count));
                });

                $row->column(3, function (Column $column) {
                    $count = OrderCancel::query()->where('status',OrderCancel::STATUS_WAIT)->count();
                    $column->append(new InfoBox('撤销', 'exchange', 'blue', config('admin.route.prefix') .'/ordercancel', $count));
                });

                $row->column(3, function (Column $column) {
                    $count = OrderTi::query()->where('status',OrderTi::STATUS_WAIT)->count();
                    $column->append(new InfoBox('提币', 'exchange', 'yellow', config('admin.route.prefix') .'/orderti', $count));
                });


                $row->column(12, function (Column $column) use ($box) {
                    $column->append($box);
                });

                /*$row->column(12, function (Column $column) use ($box2) {

                    $column->append($box2);
                });
                $row->column(6, function (Column $column) use ($box3) {

                    $column->append($box3);
                });
                $row->column(6, function (Column $column) use ($box4) {

                    $column->append($box4);
                });*/
            });

    }

}
