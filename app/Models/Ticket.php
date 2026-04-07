<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'user_id',
        'assigned_to',
        'full_name',
        'class_department',
        'category',
        'priority',
        'title',
        'description',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function attachments()
{
    return $this->hasMany(Attachment::class);
}
    public function comments()
{
    return $this->hasMany(Comment::class);
}


}

