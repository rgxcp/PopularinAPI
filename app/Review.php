<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

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

    protected $appends = [
        'timestamp',
        'is_liked',
        'is_commented',
        'total_comment',
        'total_like',
        'total_report',
        'is_nsfw'
    ];

    public function film()
    {
        return $this->belongsTo(Film::class, 'tmdb_id', 'tmdb_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getTimestampAttribute()
    {
        return $this->created_at->diffForHumans(null, true);
    }

    public function getIsLikedAttribute()
    {
        $authID = Auth::check() ? Auth::id() : 0;

        return ReviewLike::where(['user_id' => $authID, 'review_id' => $this->id])->exists();
    }

    public function getIsCommentedAttribute()
    {
        $authID = Auth::check() ? Auth::id() : 0;

        return Comment::where(['user_id' => $authID, 'review_id' => $this->id])->exists();
    }

    public function getTotalCommentAttribute()
    {
        return Comment::where('review_id', $this->id)->count();
    }

    public function getTotalLikeAttribute()
    {
        return ReviewLike::where('review_id', $this->id)->count();
    }

    public function getTotalReportAttribute()
    {
        return ReviewReport::where('review_id', $this->id)->count();
    }

    public function getIsNSFWAttribute()
    {
        return $this->total_report >= env('SFW_THRESHOLD');
    }
}
