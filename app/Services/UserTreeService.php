<?php


namespace App\Services;


use App\Models\User;

class UserTreeService
{
    public function getUserTree($parentId = 0, $allUser = null)
    {
        if (is_null($allUser)) {
            // 从数据库中一次性取出所有类目
            $allUser = User::all();
        }
        $res = $allUser
            ->where('parent_id', $parentId)
            ->map(function (User $user) use ($allUser) {
                $data = ['id' => $user->id, 'name' => $user->phone];
                if (!$user->is_directory) {
                    return $data;
                }
                $data['children'] = array_values($this->getUserTree($user->id, $allUser)->toArray());
                return $data;
            });
        return $res;
    }
}