<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SensorDataAggregate extends Model
{
    use HasFactory;


    protected $fillable = [
        'device_id',
        'aggregate_type', // daily, weekly, monthly, yearly
        'period_start',
        'period_end',
        'data' // JSON data for aggregated values
    ];

    protected $casts = [
        'period_start' => 'datetime',
        'period_end' => 'datetime',
        'data' => 'array', // Cast JSON data to array
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];



    const TYPE_DAILY = 'daily';
    const TYPE_WEEKLY = 'weekly';
    const TYPE_MONTHLY = 'monthly';
    const TYPE_YEARLY = 'yearly';


    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function scopeDaily($query)
    {
        return $query->where('aggregate_type', self::TYPE_DAILY);
    }
    public function scopeWeekly($query)
    {
        return $query->where('aggregate_type', self::TYPE_WEEKLY);
    }
    public function scopeMonthly($query)
    {
        return $query->where('aggregate_type', self::TYPE_MONTHLY);
    }
    public function scopeYearly($query)
    {
        return $query->where('aggregate_type', self::TYPE_YEARLY);
    }



    public function getAvgTemperature1()
    {
        return $this->data['temperature_1'] ?? null;

    }
    public function getAvgTemperature2()
    {
        return $this->data['temperature_2'] ?? null;
    }
    public function getMaxTemperature1()
    {
        return $this->data['max_temperature_1'] ?? null;
    }
    public function getMaxTemperature2()
    {
        return $this->data['max_temperature_2'] ?? null;
    }
    public function getMinTemperature1()
    {
        return $this->data['min_temperature_1'] ?? null;
    }
    public function getMinTemperature2()
    {
        return $this->data['min_temperature_2'] ?? null;
    }
    public function getAvgBatteryVoltage()
    {
        $batteries = [
            $this->data['avg_battery_a'] ?? 0,
            $this->data['avg_battery_b'] ?? 0,
            $this->data['avg_battery_c'] ?? 0,
            $this->data['avg_battery_d'] ?? 0,
        ];
        return array_sum($batteries) / count($batteries);
    }

    public function getTotalEnergyConsumed()
    {
        return $this->data['total_energy_consumed'] ?? 0;
    }
    public function getUptimePercentage()
    {
        return $this->data['uptime_percentage'] ?? 0;
    }
    public function getTotalRecords()
    {
        return $this->data['total_records'] ?? 0;
    }



    public static function createDailyAggregate($deviceId, $date)
    {
        $startOfDay = Carbon::parse($date)->startOfDay();
        $endOfDay = Carbon::parse($date)->endOfDay();

        $sensorData = SensorData::where('device_id', $deviceId)
        ->whereBetween('created_at', [$startOfDay, $endOfDay])
        ->get();

        if($sensorData->isEmpty()){
            return null;

        }

        $aggregatedData = [
            'avg_battery_a' =>round($sensorData->avg('battery_a'),2),
            'avg_battery_b' =>round($sensorData->avg('battery_b'),2),
            'avg_battery_c' =>round($sensorData->avg('battery_c'),2),
            'avg_battery_d' =>round($sensorData->avg('battery_d'),2),
            'avg_temperature_1' =>round($sensorData->avg('temperature_1'), 2),
            'avg_temperature_2' =>round($sensorData->avg('temperature_2'), 2),
            'max_avg_temperature_1' =>round($sensorData->max('temperature_1'), 2),
            'max_avg_temperature_2' =>round($sensorData->max('temperature_2'), 2),
            'min_avg_temperature_1' =>round($sensorData->min('temperature_1'), 2),
            'min_avg_temperature_2' =>round($sensorData->min('temperature_2'), 2),
            'avg_pln_volt' =>round($sensorData->avg('pln_volt'),2),
            'avg_pln_current' =>round($sensorData->avg('pln_current'),2),
            'avg_pln_watt' =>round($sensorData->avg('pln_watt'),2),
            'total_energy_consumed' =>round($sensorData->sum('pln_watt')/1000,2),
            'total_records' =>$sensorData->count(),
            'uptime_percentage' =>100
        ];
        return self::updateOrCreate([
            'device_id'=>$deviceId,
            'aggregation_type'=>self::TYPE_DAILY,
            'period_start'=>$startOfDay,
            'period_end'=>$endOfDay
        ],[
            'data'=>$aggregatedData
        ]);
    }

    public function createWeeklyAggregate($deviceId, $weekStart)
    {
        $startOfWeek = Carbon::parse($weekStart)->startOfWeek();
        $endOfWeek = Carbon::parse($weekStart)->endOfWeek();

        $dailyData = SensorData::where('device_id', $deviceId)
        ->where('aggregation_type', self::TYPE_DAILY)
        ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
        ->get();

        if($dailyData->isEmpty()){
            return null;

        }

        $aggregatedData = [
            'avg_battery_a'=>round($dailyData->avg(function($item){return $item->data['avg_battery_a'];}),2),
            'avg_battery_b'=>round($dailyData->avg(function($item){return $item->data['avg_battery_b'];}),2),
            'avg_battery_c'=>round($dailyData->avg(function($item){return $item->data['avg_battery_c'];}),2),
            'avg_battery_d'=>round($dailyData->avg(function($item){return $item->data['avg_battery_d'];}),2),
            'avg_temperature_1'=>round($dailyData->avg(function($item){return $item->data['avg_temperature_1'];}),2),
            'avg_temperature_2'=>round($dailyData->avg(function($item){return $item->data['avg_temperature_2'];}),2),
            'max_avg_temperature_1'=>round($dailyData->max(function($item){return $item->data['max_avg_temperature_1'];}),2),
            'max_avg_temperature_2'=>round($dailyData->max(function($item){return $item->data['max_avg_temperature_2'];}),2),
            'min_avg_temperature_1'=>round($dailyData->min(function($item){return $item->data['min_avg_temperature_1'];}),2),
            'min_avg_temperature_2'=>round($dailyData->min(function($item){return $item->data['min_avg_temperature_2'];}),2),
            'avg_pln_volt'=>round($dailyData->avg(function($item){return $item->data['avg_pln_volt'];}),2),
            'avg_pln_current'=>round($dailyData->avg(function($item){return $item->data['avg_pln_current'];}),2),
            'avg_pln_watt'=>round($dailyData->avg(function($item){return $item->data['avg_pln_watt'];}),2),
            'total_energy_consumed'=>round($dailyData->sum(function($item){return $item->data['total_energy_consumed'];})/1000,2),
            'total_records'=>$dailyData->count(),
            'uptime_percentage'=>100
        ];
        return self::updateOrCreate([
            'device_id'=>$deviceId,
            'aggregation_type'=>self::TYPE_WEEKLY,
            'period_start'=>$startOfWeek,
            'period_end'=>$endOfWeek
        ],[
            'data'=>$aggregatedData
        ]);
    }

    public function createMonthlyAggregate($deviceId, $monthStart)
    {
        $startOfMonth = Carbon::parse($monthStart)->startOfMonth();
        $endOfMonth = Carbon::parse($monthStart)->endOfMonth();

        $weeklyData = SensorData::where('device_id', $deviceId)
        ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
        ->get();

        if($weeklyData->isEmpty()){
            return null;

        }
        $aggregatedData = [
            'avg_battery_a' => round($weeklyData->avg(function($item){return $item->data['avg_battery_a'];}),2),
            'avg_battery_b' => round($weeklyData->avg(function($item){return $item->data['avg_battery_b'];}),2),
            'avg_battery_c' => round($weeklyData->avg(function($item){return $item->data['avg_battery_c'];}),2),
            'avg_battery_d' => round($weeklyData->avg(function($item){return $item->data['avg_battery_d'];}),2),
            'avg_temperature_1' => round($weeklyData->avg(function($item){return $item->data['avg_temperature_1'];}),2),
            'avg_temperature_2' => round($weeklyData->avg(function($item){return $item->data['avg_temperature_2'];}),2),
            'max_avg_temperature_1' => round($weeklyData->max(function($item){return $item->data['max_avg_temperature_1'];}),2),
            'max_avg_temperature_2' => round($weeklyData->max(function($item){return $item->data['max_avg_temperature_2'];}),2),
            'min_avg_temperature_1' => round($weeklyData->min(function($item){return $item->data['min_avg_temperature_1'];}),2),
            'min_avg_temperature_2' => round($weeklyData->min(function($item){return $item->data['min_avg_temperature_2'];}),2),
            'avg_pln_volt' => round($weeklyData->avg(function($item){return $item->data['avg_pln_volt'];}),2),
            'avg_pln_current' => round($weeklyData->avg(function($item){return $item->data['avg_pln_current'];}),2),
            'avg_pln_watt' => round($weeklyData->avg(function($item){return $item->data['avg_pln_watt'];}),2),
            'total_energy_consumed' => round($weeklyData->sum(function($item){return $item->data['total_energy_consumed'];})/1000,2),
            'total_records' => $weeklyData->count(),
            'uptime_percentage' => 100
        ];
    }

}

