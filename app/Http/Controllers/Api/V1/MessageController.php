<?php


namespace App\Http\Controllers\Api\V1;


use App\Exceptions\VerifyException;
use App\Models\Message;
use App\Models\Notice;

class MessageController
{

    /**
     * showdoc
     * @catalog 消息
     * @title 消息首页
     * @description 消息首页
     * @method get
     * @url message/index
     * @return {"code":200,"data":{"count":3,"bus":{"content":"\u91cf\u5316\u5df2\u7ed3\u675f","created_at":"2019-12-18 17:11:21"},"notice":{"title":"\u5173\u4e8e\u6cf0\u4ed5\u8fbe\u5b98\u7f51\u5168\u65b0\u6539\u7248\u4e0a\u7ebf\u7684\u901a\u77e5","created_at":"2019-12-10 08:34:01"}}}
     * @return_param count string 业务提醒未读数量
     * @return_param bus string 业务提醒消息内容
     * @return_param notice string 系统通知消息内容
     * @remark 业务提醒显示未读数量，系统通知不显示
     * @number 1
     */
    public function index()
    {
        $user = \Auth::guard('api')->user();
        $count = 0;
        $bus = [];
        if ($user) {
            $count = Message::query()->where('user_id', $user->id)->where('is_read', 0)->count();

            $bus = Message::query()->where('user_id', $user->id)->where('is_read', 0)->orderByDesc('id')->select('content', 'created_at')->first();
            if(!$bus){
                $bus = [];
            }
        }
        $notice = Notice::query()->orderByDesc('id')->select('title', 'created_at')->first();
        return response()->json(['code' => 200, 'data' => ['count' => $count, 'bus' => $bus, 'notice' => $notice]]);
    }

    /**
     * showdoc
     * @catalog 消息
     * @title 业务消息
     * @description 业务消息列表
     * @method get
     * @url message/buslist
     * @return {"current_page":1,"data":[{"id":7,"user_id":13,"title":"\u4f17\u7b792\u53f7","content":"\u91cf\u5316\u5df2\u7ed3\u675f","is_read":0,"created_at":"2019-12-18 17:11:21","updated_at":"2019-12-23 14:47:52"},{"id":5,"user_id":13,"title":"\u4f17\u7b792\u53f7","content":"\u91cf\u5316\u5df2\u7ed3\u675f","is_read":0,"created_at":"2019-12-18 17:10:18","updated_at":"2019-12-23 14:47:52"},{"id":3,"user_id":13,"title":"\u4f17\u7b792\u53f7","content":"\u91cf\u5316\u5df2\u542f\u52a8","is_read":0,"created_at":"2019-12-18 15:47:59","updated_at":"2019-12-23 14:47:52"}],"first_page_url":"http:\/\/192.168.10.10\/api\/message\/buslist?page=1","from":1,"last_page":1,"last_page_url":"http:\/\/192.168.10.10\/api\/message\/buslist?page=1","next_page_url":null,"path":"http:\/\/192.168.10.10\/api\/message\/buslist","per_page":30,"prev_page_url":null,"to":3,"total":3}
     * @remark 业务提醒显示未读数量，系统通知不显示
     * @number 1
     */
    public function buslist()
    {
        $param = request()->input();
        $user = \Auth::guard('api')->user();
        $page_size = $param['page_size'] ?? 30;

        $res = Message::query()->where('user_id', $user->id)->orderBy('is_read')->orderByDesc('id')->paginate($page_size);

        $data = $res->getCollection();
        Message::query()->whereIn('id', $data->pluck('id'))->update(['is_read' => 1]);
        return response()->json($res);
    }

    /**
     * showdoc
     * @catalog 消息
     * @title 系统消息列表
     * @description 系统消息列表
     * @method get
     * @url message/notice
     * @return {"current_page":1,"data":[{"id":2,"title":"\u5173\u4e8e\u6cf0\u4ed5\u8fbe\u5b98\u7f51\u5168\u65b0\u6539\u7248\u4e0a\u7ebf\u7684\u901a\u77e5"},{"id":1,"title":"\u5173\u4e8e\u9f0e\u6602APP1.0\u4e0a\u7ebf\u901a\u77e5"}],"first_page_url":"http:\/\/192.168.10.10\/api\/message\/notice?page=1","from":1,"last_page":1,"last_page_url":"http:\/\/192.168.10.10\/api\/message\/notice?page=1","next_page_url":null,"path":"http:\/\/192.168.10.10\/api\/message\/notice","per_page":30,"prev_page_url":null,"to":2,"total":2}
     * @remark 无
     * @number 1
     */
    public function notice()
    {
        $param = request()->input();
        $page_size = $param['page_size'] ?? 30;
        $res = Notice::query()->orderByDesc('id')->select('id', 'title')->paginate($page_size);
        return response()->json($res);
    }

    /**
     * showdoc
     * @catalog 消息
     * @title 系统通知
     * @description 系统通知详情
     * @method get
     * @param id int 必填 id
     * @url message/noticedetail
     * @return {"code":200,"data":{"id":2,"title":"\u5173\u4e8e\u6cf0\u4ed5\u8fbe\u5b98\u7f51\u5168\u65b0\u6539\u7248\u4e0a\u7ebf\u7684\u901a\u77e5","content":"<p><\/p><p>\u6cf0\u4ed5\u8fbe\u4ee5\u6539\u53d8\u4eba\u624d\uff0c\u6539\u53d8\u4e16\u754c\u4e3a\u5df1\u4efb\uff0c\u4e0d\u65ad\u63d0\u5347\u5ba2\u6237\u7684\u4f53\u9a8c\u4e0e\u670d\u52a1\uff0c\u611f\u8c22\u60a8\u7684\u652f\u6301\u4e0e\u4fe1\u4efb\uff01\u5982\u6709\u4efb\u4f55\u7591\u95ee\uff0c\u8bf7\u8054\u7cfb\u54a8\u8be2\u987e\u95ee\u3002\u8c22\u8c22\uff01<\/p><p>\u7279\u6b64\u516c\u544a\u3002<\/p>","created_at":"2019-12-10 08:34:01","updated_at":"2019-12-10 08:34:01"}}
     * @remark 无
     * @number 1
     */
    public function noticedetail()
    {
        $id = request()->input('id');
        if (!$id) {
            throw new VerifyException('缺少参数');
        }
        $res = Notice::find($id);
        return response()->json(['code' => 200, 'data' => $res]);
    }

}