<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Ticket extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;
    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'category_id',
        'user_id'
    ];


    public function registerMediaCollections()
    : void
    {
        $this->addMediaCollection('files');
    }


    protected function user()
    : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    : HasMany
    {
        return $this->hasMany(Comment::class, 'ticket_id');
    }

    public function assigned_agent()
    : BelongsToMany
    {
        return $this->belongsToMany(User::class, 'ticket_participants', 'ticket_id', 'user_id');
    }

    public function category()
    : BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function files()
    : HasMany
    {
        return $this->hasMany(File::class, 'ticket_id');
    }

    public function scopeAssignedStatus(Builder $query, bool $assigned)
    : Builder
    {
        if ($assigned) {
            return $query->whereHas('assigned_agent');
        } else {
            return $query->whereDoesntHave('assigned_agent');
        }
    }

    public function scopeAssignedAgent(Builder $query, $agentId)
    : Builder
    {
        return $query->whereHas('assigned_agent', function ($query) use ($agentId) {
            $query->where('user_id', $agentId);
        });
    }

    public function scopeAssigned(Builder $query)
    : Builder
    {
        return $query->whereHas('assigned_agent');
    }

    public function scopeUnassigned(Builder $query)
    : Builder
    {
        return $query->whereDoesntHave('assigned_agent');
    }
}
