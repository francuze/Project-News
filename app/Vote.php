<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    protected $fillable = ['news_id', 'vote_type'];

    public function news()
    {
        return $this->belongsTo(News::class);
    }
}