<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Training extends Model implements Auditable
{
    use HasFactory;
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = ['title','description','trainer','attachment'];

    // one training belongs to a user - FK
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    // getter $training->attachment_url
    public function getAttachmentUrlAttribute()
    {
        if ($this->attachment) {
            return asset('storage/'.$this->attachment);
        } else {
            return 'https://www.google.com/url?sa=i&url=https%3A%2F%2Fen.wikipedia.org%2Fwiki%2FFile%3ANo_image_available.svg&psig=AOvVaw38IAAKm935bKpMHly0RIX0&ust=1608696789316000&source=images&cd=vfe&ved=0CAIQjRxqFwoTCJCN6tjc4O0CFQAAAAAdAAAAABAD';
        }
    }
}
