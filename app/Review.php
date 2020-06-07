<?php

namespace App;

use Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use SoftDeletes;

    protected $guarded = [
        // All columns are guarded
    ];

    protected $hidden = [
        'user_id',
        'tmdb_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public $appends = [
        'timestamp',
        'is_liked',
        'total_comment',
        'total_like'
    ];

    public function film() {
        return $this->belongsTo(Film::class, 'tmdb_id', 'tmdb_id');
    }

    public function user() {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function getTimestampAttribute() {
        return $this->created_at->diffForHumans();
    }

    public function getIsLikedAttribute() {
        if (Auth::check()) {
            $authID = Auth::id();
        } else {
            $authID = 0;
        }

        return Like::where(['user_id' => $authID, 'review_id' => $this->id])->exists();
    }

    public function getTotalCommentAttribute() {
        return Comment::where('review_id', $this->id)->count();
    }

    public function getTotalLikeAttribute() {
        return Like::where('review_id', $this->id)->count();
    }
}
