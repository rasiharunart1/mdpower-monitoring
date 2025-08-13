<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\SensorData;
use Illuminate\Http\Request;

class SensorDataController extends Controller
{
    public function store(Request $request){
        $request->validate([
            'device_code' => 'required|string|exists:devices,device_code',
            'battery_a' => 'required|numeric',
            'battery_b' => 'required|numeric',
            'battery_c' => 'required|numeric',
            'temperature_1' => 'required|numeric',
            'temperature_2' => 'required|numeric',
            'pln_volt' => 'required|numeric',
            'pln_current' => 'required|numeric',
            'pln_watt' => 'required|numeric',
            'relay_1' => 'required|boolean',
            'relay_2' => 'required|boolean',
        ]);

        $device = Device::where('device_code', $request->device_code)->first();

        if(!$device) {
            return response()->json(['error' => 'Device not found'], 404);
        }

        $sensorData = SensorData::create([
            'device_id' => $device->id,
            'battery_a' => $request->battery_a,
            'battery_b' => $request->battery_b,
            'battery_c' => $request->battery_c,
            'battery_d' => $request->battery_d ?? 0.00, // Optional field
            'temperature_1' => $request->temperature_1,
            'temperature_2' => $request->temperature_2,
            'pln_volt' => $request->pln_volt,
            'pln_current' => $request->pln_current,
            'pln_watt' => $request->pln_watt,
            'relay_1' => $request->relay_1,
            'relay_2' => $request->relay_2,
        ]);

        return response()->json(['message' => 'Sensor data created successfully', 'data' => $sensorData], 201);
    }
    public function getDeviceSettings($deviceCode)
    {
        $device = Device::where('device_code', $deviceCode)->first();

        if (!$device) {
            return response()->json(['error' => 'Device not found'], 404);
        }

        $settings = $device->deviceSettings;

        if (!$settings) {
            return response()->json(['error' => 'Device settings not found'], 404);
        }

        return response()->json(['data' => $settings], 200);
    }


}
