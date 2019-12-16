<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use App\Models\Crowdfunding;
use App\Models\Notice;
use App\Models\Slide;
use Carbon\Carbon;

class IndexController
{

    /**
     * showdoc
     * @catalog 主页
     * @title 主页
     * @description 主页
     * @method get
     * @url index/index
     * @return {"code":200,"data":{"slide":["images\/d8e900942611cc96ce31dc3f97f72ef9.jpg","images\/d2f43208c379ddea0764bede954f6665.jpg"],"notice":[{"id":2,"title":"\u5173\u4e8e\u6cf0\u4ed5\u8fbe\u5b98\u7f51\u5168\u65b0\u6539\u7248\u4e0a\u7ebf\u7684\u901a\u77e5"},{"id":1,"title":"\u5173\u4e8e\u9f0e\u6602APP1.0\u4e0a\u7ebf\u901a\u77e5"}],"crow":[{"id":1,"code":"40621","title":"\u4f17\u7b792\u53f7","target_amount":"10000.0000","total_amount":"10000.0000","status":"end","run_status":"run","created_at":"2019-12-14 00:00:00","start_at":"2019-12-13","end_at":1607322304,"diff_day":359},{"id":2,"code":"10425","title":"\u4f17\u7b7932\u53f7","target_amount":"100000.0000","total_amount":"0.0000","status":"funding","run_status":"stop","created_at":"2019-12-12 08:24:50","start_at":null,"end_at":null,"loading":0}]},"message":"ok"}
     * @return_param slide string 幻灯
     * @return_param notice string 通知
     * @return_param crow string 最后一页
     * @return_param per_page string 每页显示数
     * @remark 文章单独请求index/article
     * @number 1
     */
    public function index()
    {

        $slide = Slide::query()->where('title', 0)->pluck('thumb');
        $notice = Notice::query()->select('id', 'title')->orderByDesc('id')->get();

        $crow = Crowdfunding::query()
            ->select('id', 'code', 'title', 'target_amount', 'total_amount', 'status', 'run_status', 'created_at', 'start_at', 'end_at')
            ->where('status',Crowdfunding::STATUS_FUNDING)
            ->orWhere('run_status',Crowdfunding::RUN_START)
            ->get()->map(function($value){

                $value->loading = $value->percent;
                if ($value->run_status == Crowdfunding::RUN_START && $value->status == Crowdfunding::STATUS_END) {
                    $value->created_at = Carbon::parse($value->created_at)->toDateString();
                    $value->start_at = date('Y-m-d', $value->start_at);
                    if ($value->end_at > time()) {
                        $cDate = Carbon::parse(date('Y-m-d', $value->end_at));
                        $value->diff_day = $cDate->diffInDays() + 1;
                    }
                }
                return $value;
        });

        $data = [
            'slide' => $slide,
            'notice' => $notice,
            'crow' => $crow
        ];
        return response()->json(['code' => 200, 'data' => $data, 'message' => 'ok']);
    }

    /**
     * showdoc
     * @catalog 主页
     * @title 文章
     * @description 主页文章
     * @method get
     * @url index/article
     * @param page string 可选 页数
     * @return {"current_page":2,"data":[{"id":22,"title":"\u8523\u4e0d\u7684\u76f8\u518c","thumb":"images\/7cb059e420705ed317d4c19a8afa4a97.jpg","short_content":"\u5ee2\u68c4\u5bfa\u5edf\uff0c\u4e0a\u6d77\uff0cC\u7d1a\uff0c\u9032\u5165\u96e3\u5ea61.5\uff0c\u7121\u4eba\u503c\u5b88\uff0c\u4f4e\u96e3\u5ea6\u7ffb\u58bb\u3002\u00a0\u5ee2\u68c4\u5bfa\u5edf\uff0c\u4e0a\u6d77\uff0cC\u7d1a\uff0c\u9032\u5165\u96e3\u5ea61.5\uff0c...","created_at":"2019-12-10 09:03:12"},{"id":21,"title":"\u8fd9\u662f\u4e00\u7bc7\u65b0\u95fb","thumb":"images\/000946c934c9bab205665e7ad1551075.jpg","short_content":"\u6cf0\u4ed5\u8fbe\u96c6\u56e2\u516c\u53f8\u81f4\u529b\u4e8e\u5404\u5927\u4f01\u4e1a\u7684\u4eba\u624d\u89e3\u51b3\u65b9\u6848\u62db\u8058\u670d\u52a1\uff0c\u4e1a\u52a1\u9886\u57df\u5305\u62ec\u4e2d\u9ad8\u7aef\u4eba\u624d\u5bfb\u8bbf\u3001\u62db\u8058\u6d41\u7a0b\u5916\u5305\u3001\u4eba\u529b\u8d44...","created_at":"2019-12-10 08:41:59"}],"first_page_url":"http:\/\/192.168.10.10\/api\/index\/article?page=1","from":3,"last_page":12,"last_page_url":"http:\/\/192.168.10.10\/api\/index\/article?page=12","next_page_url":"http:\/\/192.168.10.10\/api\/index\/article?page=3","path":"http:\/\/192.168.10.10\/api\/index\/article","per_page":2,"prev_page_url":"http:\/\/192.168.10.10\/api\/index\/article?page=1","to":4,"total":24}
     * @return_param current_page string 当前页
     * @return_param current_page string 分页数据
     * @return_param last_page string 最后一页
     * @return_param per_page string 每页显示数
     * @remark 无
     * @number 1
     */
    public function article()
    {
        $param = request()->input();
        $page_size = $param['page_size'] ?? 4;
        $res = Article::where('article_cate_id', 1)->select('id', 'title', 'thumb', 'short_content', 'created_at')->orderByDesc('id')->paginate($page_size);
        return response()->json($res);
    }

}