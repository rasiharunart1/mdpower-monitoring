<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $device = $user->device;

        if(!$device){
            return view('dashboard.index')->with('error', 'user tidak memiliki device');
        }

        $latestData = $device->getLatestSensorData();
        $settings = $device->deviceSettings;

        $todayAggregate = $device->sensorDataAggregates()
            ->daily()->whereDate('period_start', Carbon::today())->first();

        return view('dashboard.index', compact(
            'device',
            'latestData',
            'settings',
            'todayAggregate'
        ));
    }

    public function getRealtimeData(){
        try {
            $user = auth()->user();
            $device = $user->device;

            if(!$device) {
                return response()->json(['error'=>'device tidak ditemukan'], 404);
            }

            $latestData = $device->getLatestSensorData();
            $settings = $device->deviceSettings;

            if(!$latestData){
                return response()->json(['error'=>'tidak ada data sensor'], 404);
            }

            $device->checkOnlineStatus();

            $response = [
                'data' => $latestData,
                'settings' => $settings,
                'device_status' => $device->status,
                'last_seen' => $device->last_seen?->diffForHumans(),
                'timestamp' => now()->format('Y-m-d H:i:s'),
                'data_id' => $latestData->id
            ];

            return response()->json($response);

        } catch (\Exception $e) {
            \Log::error('Realtime data error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    // Perbaiki method ini
    // public function getLastDataChange(Request $request)
    // {
    //     try {
    //         $user = auth()->user();
    //         $device = $user->device;
    //         $lastKnownId = $request->get('last_id', 0);

    //         if(!$device) {
    //             return response()->json(['error'=>'device tidak ditemukan'], 404);
    //         }

    //         $latestData = $device->getLatestSensorData();

    //         if(!$latestData) {
    //             return response()->json(['has_change' => false, 'message' => 'No sensor data available']);
    //         }

    //         // Cek apakah ada perubahan
    //         $hasChange = (int)$latestData->id !== (int)$lastKnownId;

    //         if($hasChange) {
    //             $device->checkOnlineStatus();

    //             return response()->json([
    //                 'has_change' => true,
    //                 'data' => $latestData,
    //                 'device_status' => $device->status,
    //                 'last_seen' => $device->last_seen?->diffForHumans(),
    //                 'timestamp' => now()->format('Y-m-d H:i:s'),
    //                 'data_id' => $latestData->id
    //             ]);
    //         }

    //         return response()->json([
    //             'has_change' => false,
    //             'current_id' => $latestData->id,
    //             'timestamp' => now()->format('Y-m-d H:i:s')
    //         ]);

    //     } catch (\Exception $e) {
    //         \Log::error('Check changes error: ' . $e->getMessage());
    //         return response()->json(['error' => 'Internal server error'], 500);
    //     }
    // }

    // Method lainnya tetap sama...
    public function getChartData(Request $request)
    {
        $user = auth()->user();
        $device = $user->device;
        $period = $request->get('period', '24h');

        if(!$device) {
            return response()->json(['error'=>'device tidak ditemukan'], 404);
        }

        $data = [];
        switch ($period) {
            case '24h':
                $data = $this->getLast24HoursData($device);
                break;
            case '7d':
                $data = $this->getLast7DaysData($device);
                break;
            case '30d':
                $data = $this->getLast30DaysData($device);
                break;
        }

        return response()->json($data);
    }

    private function getLast24HoursData($device)
    {
        $sensorData = $device->sensorData()
            ->where('created_at', '>=', Carbon::now()->subHours(24))
            ->orderBy('created_at')
            ->get();

        return [
            'labels' => $sensorData->pluck('created_at')->map(function ($time){
                return $time->format('H:i');
            }),
            'temperature1' => $sensorData->pluck('temperature_1'),
            'temperature2' => $sensorData->pluck('temperature_2'),
            'battery_avg' => $sensorData->map(function ($data){
                return $data->getAverageBatteryA();
            }),
            'pln_watt' => $sensorData->pluck('pln_watt')
        ];
    }

    private function getLast7DaysData($device)
    {
        $aggregatedData = $device->sensorDataAggregates()
            ->daily()
            ->where('period_start', '>=', Carbon::now()->subDays(7))
            ->orderBy('period_start')
            ->get();

        return [
            'labels' => $aggregatedData->pluck('period_start')->map(function ($date){
                return $date->format('M d');
            }),
            'temperature1' => $aggregatedData->map(function ($data){
                return $data->getAvgTemperature1();
            }),
            'temperature2' => $aggregatedData->map(function ($data){
                return $data->getAvgTemperature2();
            }),
            'battery_avg' => $aggregatedData->map(function ($data){
                return $data->getAvgBatteryVoltage();
            }),
            'energy_consumed' => $aggregatedData->map(function ($data){
                return $data->getTotalEnergyConsumed();
            })
        ];
    }

    private function getLast30DaysData($device)
    {
        $aggregatedData = $device->sensorDataAggregates()
            ->daily()
            ->where('period_start', '>=', Carbon::now()->subDays(30))
            ->orderBy('period_start')
            ->get();

        return [
            'labels' => $aggregatedData->pluck('period_start')->map(function ($date){
                return $date->format('M d');
            }),
            'temperature1' => $aggregatedData->map(function ($data){
                return $data->getAvgTemperature1();
            }),
            'temperature2' => $aggregatedData->map(function ($data){
                return $data->getAvgTemperature2();
            }),
            'battery_avg' => $aggregatedData->map(function ($data){
                return $data->getAvgBatteryVoltage();
            }),
            'energy_consumed' => $aggregatedData->map(function ($data){
                return $data->getTotalEnergyConsumed();
            })
        ];
    }
}
