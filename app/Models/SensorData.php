<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SensorData extends Model
{
    use HasFactory;


    protected $fillable = [
        'device_id',
        'battery_a',
        'battery_b',
        'battery_c',
        'battery_d',
        'temperature_1',
        'temperature_2',
        'pln_volt',
        'pln_current',
        'pln_watt',
        'relay_1',
        'relay_2',
    ];

    protected $casts = [
        'battery_a' => 'decimal:2',
        'battery_b' => 'decimal:2',
        'battery_c' => 'decimal:2',
        'battery_d' => 'decimal:2',
        'temperature_1' => 'decimal:2',
        'temperature_2' => 'decimal:2',
        'pln_volt' => 'decimal:2',
        'pln_current' => 'decimal:2',
        'pln_watt' => 'decimal:2',
        'relay_1' => 'boolean',
        'relay_2' => 'boolean',
        'updated_at' => 'datetime',
        'created_at' => 'datetime'
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', Carbon::today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
    }
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year);
    }


    //rata-rata sensor
    public function getAverageBatteryA(): float
    {
        return ($this->battery_a+ $this->battery_b+ $this->battery_c+ $this->battery_d)/4;
    }

    public function isCriticalTemperature()
    {
        $settings = $this->device->deviceSetting;
        if (!$settings) return false; // No settings found for the device
        return $this->temperature_1 > $settings->temp1_threshold || $this->temperature_2 > $settings->temp2_threshold;
    }


    //event aggregate data

    protected static function boot()
    {
        parent::boot();

        static::created(function($sensorData){
            $sensorData->device->updateLastSeen();
            // $sensorData->device->checkForAlerts();

        });
    }


}
