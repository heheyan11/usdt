<?php


namespace App\Http\Controllers\Api;


use App\Exceptions\BusException;
use App\Exceptions\InternalException;
use App\Http\Requests\PariseRequest;
use App\Models\Article;
use App\Models\ArticleParise;

class ArticleController
{
    /**
     * showdoc
     * @catalog 文章
     * @title 详情
     * @description 通过id传值过来
     * @method get
     * @url article/detail
     * @param id string 必选 文章id
     * @return {"code": 200,"data": {"id": 3,"title": "这是一篇新闻","thumb": "images/000946c934c9bab205665e7ad1551075.jpg","content": "<p></p><p>泰仕达集团公司致力于各大企业的人才解决方案招聘服务，业务领域包括中高端人才寻访、招聘流程外包、人力资源外包等一站式人才解决方案。我们的客户来自各行各业，为了共同目标，我们在工作上密切配合。从泰莱特到泰仕达，感谢他们对我们的高要求，感谢他们从不同领域给我们带来的挑战，让我们激情的团队有机会用头脑与智慧不断的给客户带来惊喜。</p><p>泰仕达以改变人才，改变世界为己任，不断提升客户的体验与服务，感谢您的支持与信任！如有任何疑问，请联</p> ","zan": 1,"clicks": 0,"created_at": "2019-12-10 08:41:59","is_parise": 1},"message": "ok"}
     * @return_param clicks string 浏览数
     * @return_param zan string 点赞数
     * @return_param is_parise string 是否点赞 1 已点赞 0 没有点赞
     * @remark 无
     * @number 1
     */
    public function detail()
    {
        $id = request()->input('id');
        if (!$id) {
            throw new BusException('缺少参数', 422);
        }
        $art = Article::query()->where('id', $id)
            ->select('id', 'title', 'thumb', 'content', 'zan', 'clicks', 'created_at')
            ->first();

        if ($user = \Auth::guard('api')->user()) {
            $exits = $art->parise()->where('user_id', $user->id)->where('status', ArticleParise::STATUS_YES)->exists();
            $art->is_parise = (int)$exits;
        } else {
            $art->is_parise = ArticleParise::STATUS_NO;
        }
        $art->increment('clicks');

        return response()->json(['code' => 200, 'data' => $art, 'message' => 'ok']);
    }

    /**
     * showdoc
     * @catalog 文章
     * @title 文章点赞
     * @description 用户给文章点赞
     * @method post
     * @url article/parise
     * @param id int 必选 文章id
     * @param status int 必选 1点赞0取消点赞
     * @return {"code": 200,"message": "点赞成功"}
     * @remark 前端判断是否登录，没有登录不能点赞
     * @number 1
     */
    public function parise(PariseRequest $request)
    {
        $param = $request->input();
        $art = Article::find($param['id']);
        if (!$art) {
            throw new BusException('查无内容');
        }
        $user = \Auth::guard('api')->user();

        $parse = ArticleParise::query()->where('article_id', $art->id)->where('user_id', $user->id)->first();
        if ($parse) {
            if ($parse->status != $param['status']) {
                $parse->update(['status' => $param['status']]);
                $param['status'] == ArticleParise::STATUS_YES ? $art->increment('zan') : $art->decrement('zan');
            }
        } else {
            $art->parise()->create(['user_id' => $user->id, 'status' => ArticleParise::STATUS_YES]);
            $art->increment('zan');
        }

        return response()->json(['code' => 200, 'message' => '操作成功']);
    }
}