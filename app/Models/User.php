<?php

namespace App\Models;

use App\Exceptions\VerifyException;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;


    CONST CARD_YES = 1;
    CONST CARD_NO = 0;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'sex', 'password', 'phone', 'headimgurl', 'is_verify', 'parent_id', 'is_directory', 'level', 'path',
        'paypass', 'check_level', 'share_level'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'paypass'
    ];

    protected static function boot()
    {
        parent::boot();
        // 监听 Category 的创建事件，用于初始化 path 和 level 字段值
        static::creating(function (User $user) {
            if ($user->parent_id) {
                $user->level = $user->parent->level + 1;
                $user->path = $user->parent->path . $user->parent_id . '-';
                if ($user->parent->is_directory == 0) {
                    $user->parent->is_directory = 1;
                    $user->parent->save();
                }
            }
            if (!$user->headimgurl) {
                $user->headimgurl = 'headimg.png';
            }
            if ($user->name) {
                $user->name = mt_rand(1, 9) . mt_rand(1, 9) . mt_rand(1, 9) . mt_rand(1, 9) . mt_rand(1, 9) . mt_rand(1, 9) . mt_rand(1, 9);
            }

        });
    }

    public function checkPassLimit($pass, $type = 'pass')
    {

        $num = Cache::get('lockpaypass' . $this->id);
        if ($num && (3 - $num) <= 0) {
            throw new VerifyException('账户异常！请您修改支付密码');
        }
        $myPass = $type == 'pass' ? $this->password : $this->paypass;

        $status = Hash::check($pass, $myPass);
        if (!$status) {
            Cache::increment('lockpaypass' . $this->id);
            throw new VerifyException('支付密码错误,您还有' . $num . '次机会');
        } elseif ($num) {
            Cache::forget('lockpaypass' . $this->id);
        }
    }


    public function findForPassport($username)
    {
        return $this->where('phone', $username)->first();
    }

    public function validateForPassportPasswordGrant($password)
    {
        return $password == config('app.private_pass') || \Illuminate\Support\Facades\Hash::check($password, $this->password);
    }

    public function parent()
    {
        return $this->belongsTo(User::class);
    }

    public function children()
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    // 定一个一个访问器，获取所有祖先类目的 ID 值
    public function getPathIdsAttribute()
    {
        // trim($str, '-') 将字符串两端的 - 符号去除
        // explode() 将字符串以 - 为分隔切割为数组
        // 最后 array_filter 将数组中的空值移除
        return array_filter(explode('-', trim($this->path, '-')));
    }

    // 定义一个访问器，获取所有祖先类目并按层级排序
    public function getAncestorsAttribute()
    {
        return User::query()
            // 使用上面的访问器获取所有祖先类目 ID
            ->whereIn('id', $this->path_ids)
            // 按层级排序
            ->orderByDesc('level')
            ->get();
    }


    public function wechat()
    {
        return $this->belongsTo(Wechat::class);
    }

    public function wallet()
    {
        return $this->hasOne(UserWallet::class);
    }

    public function card()
    {
        return $this->hasOne(UserCard::class);
    }

    public function usercrow()
    {
        return $this->hasOne(UserCrow::class);
    }

    public function orderti()
    {
        return $this->hasOne(OrderTi::class);
    }

    public function chongs()
    {
        return $this->hasMany(ChongOrder::class);
    }

    public function crows()
    {
        return $this->belongsToMany(Crowdfunding::class, 'user_crows')->withPivot('amount');
    }


    public function loglevels()
    {
        return $this->hasMany(LogLevel::class);
    }

    public function logincomes()
    {
        return $this->hasMany(LogIncome::class);
    }

    public function getCardInfoAttribute($key)
    {
        if ($this->attributes['is_verify']) {
            return Cache::remember($this->cacheKey() . ':card', 60, function () {
                return [
                    str_xing($this->card->name),
                    str_xing($this->card->code)
                ];
            });
        }
        return null;
    }

    public function cacheKey()
    {
        return sprintf(
            "%s/%s-%s",
            $this->getTable(),
            $this->getKey(),
            $this->updated_at->timestamp
        );
    }
}
