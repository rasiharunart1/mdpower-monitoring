<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'device_code',
        'status',
        'last_seen',
    ];

    protected $casts = [
        'last_seen' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function deviceSettings()
    {
        return $this->hasOne(DeviceSettings::class, 'device_id');
    }

    public function sensorData()
    {
        return $this->hasMany(SensorData::class);
    }
    public function sensorDataAggregates()
    {
        return $this->hasMany(SensorDataAggregate::class);
    }


    //get sensor data
    public function getLatestSensorData()
    {
        return $this->sensorData()->latest()->first();
    }

    public function updateLastSeen()
    {
        $this->update(['last_seen' => now(), 'status' => 'online']);
    }

    public function checkOnlineStatus()
    {
        if ($this->last_seen) {
            $minutesOffline = $this->last_seen->diffInMinutes(now());
            $this->status = $minutesOffline > 5 ? 'offline' : 'online';
            $this->save();
        }
    }

    public function scopeOnline($query)
    {
        return $query->where('status', 'online');
    }

    public function scopeOffline($query)
    {
        return $query->where('status', 'offline');
    }
}
