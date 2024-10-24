<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Comment extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'description', 'ticket_id', 'user_id',
    ];

    protected function ticket()
    : BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    protected function user()
    : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function registerMediaCollections()
    : void
    {
        $this->addMediaCollection('files');
    }
}
