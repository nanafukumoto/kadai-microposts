<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
     public function microposts()
    {
        return $this->hasMany(Micropost::class);
    }
    
    
    public function followings()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }
    
    
    public function follow($userId)
{
    // confirm if already following
    $exist = $this->is_following($userId);
    // confirming that it is not you
    $its_me = $this->id == $userId;

    if ($exist || $its_me) {
        // do nothing if already following
        return false;
    } else {
        // follow if not following
        $this->followings()->attach($userId);
        return true;
    }
}

    public function unfollow($userId)
{
    // confirming if already following
    $exist = $this->is_following($userId);
    // confirming that it is not you
    $its_me = $this->id == $userId;


    if ($exist && !$its_me) {
        // stop following if following
        $this->followings()->detach($userId);
        return true;
    } else {
        // do nothing if not following
        return false;
    }
}


     public function is_following($userId) {
        return $this->followings()->where('follow_id', $userId)->exists();
}
    
    public function feed_microposts()
    {
        $follow_user_ids = $this->followings()-> pluck('users.id')->toArray();
        $follow_user_ids[] = $this->id;
        return Micropost::whereIn('user_id', $follow_user_ids);
    }
    
        public function favorite()
    {
        return $this->belongsToMany(Micropost::class, 'favorite', 'user_id', 'micropost_id')->withTimestamps();
    }
    
        public function fav($micropostId)
    {
        //すでにお気に入りしているか確認
        $exist = $this ->is_favoriting($micropostId);
        
        if ($exist) {
            //既にお気に入りしていれば何もしない
            return false;
        } else{
            //お気に入りしていなければお気に入りする
            $this->favorite()->attach($micropostId);
            return true;
        }
    }
        
        public function unfav($micropostId)
        {
            //既にお気に入りしているか確認
            $exist = $this->is_favoriting($micropostId);
            
            if($exist) {
                //既にお気に入りしていれば外す
                $this->favorite()->detach($micropostId);
                return true;
            } else {
                //まだお気に入りしていなければ何もしない
                return false;
            }
        }
        
        public function is_favoriting($micropostId) {
            return $this->favorite()->where('micropost_id',$micropostId)->exists();
        }
     
     
     
     
}
