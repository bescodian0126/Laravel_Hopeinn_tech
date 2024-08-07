<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\GlobalStatus;

class Task extends Model
{
    use HasFactory;
    use GlobalStatus;
    public function taskQuizzes()
    {
        return $this->hasMany(TaskQuiz::class, 'task_id');
    }

    public function taskStatuses()
    {
        return $this->hasMany(TaskStatus::class, 'task_id');
    }
}
