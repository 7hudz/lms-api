<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'price', 'instructor_id'];

    public function instructor() {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function subscriptions() {
        return $this->hasMany(Subscription::class);
    }
}
