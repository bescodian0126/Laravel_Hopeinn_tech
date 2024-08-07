<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskQuiz extends Model
{
    use HasFactory;
    protected $table = 'task_quizzes';
    public function task(){
        return $this->belongsTo(Task::class, 'task_id');
    }
    public function answers(){
        return $this->hasMany(Answer::class, 'question_id');
    }
}
