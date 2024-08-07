<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Constants\Status;

class PackageTransaction extends Model
{
    use HasFactory;
    use GlobalStatus;
    public function statusBadge(): Attribute
    {
        return new Attribute(function () {
            $html = '';
            if($this->mode== Status::PACKAGE_PURCHASED){
                $html = '<span class="badge badge--primary">' . trans('Purchased') . '</span>';
            }
            elseif ($this->mode == Status::PACKAGE_RELEASED) {
                $html = '<span class="badge badge--warning">' . trans('Released') . '</span>';
            } elseif ($this->mode == Status::PACKAGE_NETWORK_BONUS) {
                $html = '<span><span class="badge badge--success">' . trans('Get Network Bonus') . '</span><br>' . diffForHumans($this->updated_at) . '</span>';
            } elseif ($this->mode == Status::PACKAGE_INVITE_BONUS) {
                $html = '<span><span class="badge badge--success">' . trans('Get Invite Bonus') . '</span><br>' . diffForHumans($this->updated_at) . '</span>';
            } 
            return $html;
        });
    }
}
