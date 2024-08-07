<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;

class TaskStatus extends Model
{
    use GlobalStatus;
    use HasFactory;
    public function scopePending($query)
    {
        $query->with(['task', 'user'])->where('status', Status::TASK_PENDING);
    }

    public function scopeApproved($query)
    {
        $query->with(['task', 'user'])->where('status', Status::TASK_SUCCESS);
    }

    public function scopeRejected($query)
    {
        $query->with(['task', 'user'])->where('status', Status::TASK_REJECT);
    }

    public function task(){
        return $this->belongsTo(Task::class);
    }
    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function statusBadge(): Attribute
    {
        return new Attribute(function () {
            $html = '';
            if($this->status== Status::TASK_PURCHASED){
                $html = '<span class="badge badge--primary">' . trans('Purchased') . '</span>';
            }
            elseif ($this->status == Status::TASK_PENDING) {
                $html = '<span class="badge badge--warning">' . trans('Pending') . '</span>';
            } elseif ($this->status == Status::TASK_SUCCESS) {
                $html = '<span><span class="badge badge--success">' . trans('Approved') . '</span><br>' . diffForHumans($this->updated_at) . '</span>';
            } elseif ($this->status == Status::TASK_REJECT) {
                $html = '<span><span class="badge badge--danger">' . trans('Rejected') . '</span><br>' . diffForHumans($this->updated_at) . '</span>';
            } elseif ($this->status == Status::TASK_GET_BONUS) {
                $html = '<span><span class="badge badge--success">' . trans('Get bonus') . '</span><br>' . diffForHumans($this->updated_at) . '</span>';
            } 
            return $html;
        });
    }
    
}
