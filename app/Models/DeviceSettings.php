<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'temp1_threshold',
        'temp2_threshold',
        'alert_enabled',
        'email_notification',
        'data_collection_interval',
    ];

    protected $casts = [
        'temp1_threshold' => 'decimal:2',
        'temp2_threshold' => 'decimal:2',
        'alert_enabled' => 'boolean',
        'email_notification' => 'boolean',
        'data_collection_interval' => 'decimal:2',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function isTemperatureAlertTriggered($temp1, $temp2): bool
    {
        return ($temp1 > $this->temp1_threshold || $temp2 > $this->temp2_threshold) && $this->alert_enabled;
    }
}
