<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\VerifyException;
use App\Models\Active;
use App\Models\Article;
use App\Models\Crowdfunding;
use App\Models\Notice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class IndexController
{

    /**
     * showdoc
     * @catalog 主页
     * @title 主页
     * @description 主页
     * @method get
     * @url index/index
     * @return {"code":200,"data":{"slide": [{"thumb": "images/6af5ae244499851139294811eba2fc77.png","id": 28}],"overall":{"BTC":{"current_price":52987.44,"current_price_usd":7563.8,"change_percent":6.09},"ETH":{"current_price":935.5,"current_price_usd":133.54,"change_percent":4.96},"BCH":{"current_price":1370.81,"current_price_usd":195.68,"change_percent":4.67}},"notice":[{"id":2,"title":"\u901a\u77e52"},{"id":1,"title":"\u901a\u77e51"}],"crow":[{"id":1,"code":"28635","title":"\u8ba1\u52121\u53f7","target_amount":"10000.0000","total_amount":"100.0000","status":"funding","run_status":"stop","created_at":"2019-12-19 10:04:53","start_at":null,"end_at":null,"loading":1},{"id":2,"code":"93857","title":"\u8ba1\u52122","target_amount":"10000.0000","total_amount":"10000.0000","status":"end","run_status":"run","created_at":"2019-12-20 17:23:59","start_at":"2019-12-20","end_at":1607937914,"loading":100,"diff_day":357}]},"message":"ok"}
     * @return_param slide string 幻灯
     * @return_param notice string 通知
     * @return_param crow string 最后一页
     * @return_param per_page string 每页显示数
     * @remark 文章单独请求index/article
     * @number 1
     */
    public function index()
    {

        $slide = Article::query()->where('article_cate_id', 3)->select('thumb','id')->get();
        $notice = Notice::query()->select('id', 'title')->orderByDesc('id')->get();

        $crow = Crowdfunding::query()
            ->select('id', 'code', 'title', 'target_amount', 'total_amount','income', 'status', 'run_status', 'created_at', 'start_at', 'end_at')
            ->where('status', Crowdfunding::STATUS_FUNDING)
            ->orWhere('run_status', Crowdfunding::RUN_START)
            ->limit(2)->get()->map(function ($value) {

                $value->loading = $value->percent;

                if ($value->run_status == Crowdfunding::RUN_START && $value->status == Crowdfunding::STATUS_END) {
                    //$value->created_at = Carbon::parse($value->created_at)->toDateString();

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
            'overall' => $this->overall(),
            'notice' => $notice,
            'crow' => $crow
        ];
        return response()->json(['code' => 200, 'data' => $data, 'message' => 'ok']);
    }

    private function overall()
    {

        return Cache::remember('hangqing', 15, function () {
            $eth = 'https://dncapi.bqiapp.com/api/coin/web-coinrank?page=1&type=-1&pagesize=100&webp=1';
            $guzzle = new \GuzzleHttp\Client();
            $response = $guzzle->get($eth);
            $eth = json_decode($response->getBody()->getContents(), true);
            $arr = [];
            foreach ($eth['data'] as $value) {
                if (in_array($value['name'], ['BTC', 'ETH', 'BCH' ,'USDT'])) {
                    if($value['name']=='USDT'){
                         Cache::forever('usdt',$value['current_price']);
                    }
                    $arr[] = [
                        'name'=>$value['name'],
                        'current_price' => $value['current_price'],
                        'current_price_usd' => $value['current_price_usd'],
                        'change_percent' => $value['change_percent']
                    ];
                }
            }
            return $arr;
        });
    }

    /**
     * showdoc
     * @catalog 主页
     * @title 文章
     * @description 主页文章
     * @method get
     * @url index/article
     * @param page string 可选 页数
     * @param page_size string 可选 分页数
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
    /**
     * showdoc
     * @catalog 主页
     * @title 帮助反馈
     * @description 帮助反馈
     * @method get
     * @url index/help
     * @return {"code":200,"data":[{"id":25,"title":"\u767b\u5f55\u65b9\u5f0f","content":"<p><\/p><p>\u767b\u5f55\u65b9\u5f0f\u767b\u5f55\u65b9\u5f0f\u767b\u5f55\u65b9\u5f0f\u767b\u5f55\u65b9\u5f0f<\/p><p>\u767b\u5f55\u65b9\u5f0f\u767b\u5f55\u65b9\u5f0f\u767b\u5f55\u65b9\u5f0f<\/p><p>\u767b\u5f55\u65b9\u5f0f\u767b\u5f55\u65b9\u5f0f<\/p>"},{"id":26,"title":"\u94b1\u5305\u65e0\u6cd5\u5145\u5e01","content":"<p><\/p><p>\u94b1\u5305\u65e0\u6cd5\u5145\u5e01\u94b1\u5305\u65e0\u6cd5\u5145\u5e01\u94b1\u5305\u65e0\u6cd5\u5145\u5e01\u94b1\u5305\u65e0\u6cd5\u5145\u5e01<\/p><p>\u94b1\u5305\u65e0\u6cd5\u5145\u5e01\u94b1\u5305\u65e0\u6cd5\u5145\u5e01\u94b1\u5305\u65e0\u6cd5\u5145\u5e01<\/p>"}]}
     * @return_param id int id
     * @return_param title string 标题
     * @return_param content string 内容
     * @remark 无
     * @number 1
     */
    public function help()
    {
        $res = Article::query()->where('article_cate_id', 2)->select('id', 'title', 'content')->get();
        return response()->json(['code' => 200, 'data' => $res]);
    }

    /**
     * showdoc
     * @catalog 主页
     * @title 提交反馈
     * @description 提交反馈
     * @method post
     * @param type string 必选 类型0功能异常1体验问题2功能建议3其他问题
     * @param content string 必选 提交内容
     * @url index/feedback
     * @return {"code":200,"message":"\u63d0\u4ea4\u6210\u529f,\u611f\u8c22\u60a8\u7684\u53cd\u9988"}
     * @remark 无
     * @number 1
     */
    public function feedback()
    {
        $param = request()->input();
        if (!isset($param['type']) || !in_array($param['type'], [0, 1, 2, 3])) {
            throw  new VerifyException('请检查提交类型');
        }
        if (!isset($param['content'])) {
            throw new VerifyException('内容不能为空');
        }
        Active::create($param);

        return response()->json(['code' => 200, 'message' => '提交成功,感谢您的反馈']);
    }

    /**
     * showdoc
     * @catalog 主页
     * @title 版本号
     * @description 版本号
     * @method get
     * @url version
     * @return {"version":"1.0.1","type":"v1"}
     * @return_param version string 版本号
     * @return_param type string 请求版本号
     * @remark 无
     * @number 1
     */
    public function version(){
        return ['version' => '1.0.1', 'type' => 'v1'];
    }

}