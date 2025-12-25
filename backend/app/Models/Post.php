<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];
    public $timestamps = false;
    public function attachments(){
        return $this->hasMany(PostAttachment::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
