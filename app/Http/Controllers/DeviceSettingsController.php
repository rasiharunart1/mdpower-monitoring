<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class DeviceSettingsController extends Controller
{
    public function index()
    {
        Log::info('DeviceSettings index accessed', [
            'user_id' => Auth::id(),
            'user_login' => Auth::user()->email ?? 'unknown',
            'timestamp' => now()->toDateTimeString()
        ]);

        $user = auth()->user();
        $device = $user->device;

        if (!$device) {
            Log::warning('User has no device', ['user_id' => Auth::id()]);
            return view('device_settings.index')->with('error', 'User tidak memiliki device');
        }

        $settings = $device->deviceSettings;

        if (!$settings) {
            Log::info('Creating default settings for device', ['device_id' => $device->id]);
            $settings = $device->deviceSettings()->create([
                'device_id' => $device->id,
                'temp1_threshold' => 25.0,
                'temp2_threshold' => 25.0,
                'email_notification' => false,
                'data_collection_interval' => 60.0,
            ]);
        }

        Log::info('DeviceSettings loaded successfully', [
            'device_id' => $device->id,
            'settings_id' => $settings->id,
            'current_settings' => $settings->toArray()
        ]);

        return view('device_settings.index', compact('settings', 'device'));
    }

  public function update(Request $request)
{
    Log::info('=== DEVICE SETTINGS UPDATE ===', [
        'user_id' => Auth::id(),
        'user_email' => Auth::user()->email ?? 'unknown',
        'timestamp' => now()->toDateTimeString(),
        'method' => $request->method(),
        'all_input' => $request->all(),
        'is_ajax' => $request->ajax()
    ]);

    try {
        $validated = $request->validate([
            'temp1_threshold' => 'required|numeric|min:-50|max:150',
            'temp2_threshold' => 'required|numeric|min:-50|max:150',

            'email_notification' => 'nullable',
            'data_collection_interval' => 'required|numeric|min:0.1|max:3600',
        ]);

        Log::info('Validation passed', $validated);

        $user = auth()->user();
        $device = $user->device;

        if (!$device) {
            Log::error('No device found for user', ['user_id' => $user->id]);

            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Device tidak ditemukan'], 404);
            }
            return redirect()->back()->with('error', 'Device tidak ditemukan untuk user ini');
        }

        $settings = $device->deviceSettings;

        if (!$settings) {
            Log::info('Creating new settings');
            $settings = $device->deviceSettings()->create([
                'device_id' => $device->id,
                'temp1_threshold' => 25.0,
                'temp2_threshold' => 25.0,
                'email_notification' => false,
                'data_collection_interval' => 60.0,
            ]);
        }

        $updateData = [
            'temp1_threshold' => (float) $request->temp1_threshold,
            'temp2_threshold' => (float) $request->temp2_threshold,
            'email_notification' => $request->has('email_notification') && $request->email_notification == '1',
            'data_collection_interval' => (float) $request->data_collection_interval,
        ];

        Log::info('Before update', $settings->toArray());

        $updated = $settings->update($updateData);

        Log::info('After update', [
            'updated_result' => $updated,
            'new_data' => $settings->fresh()->toArray()
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Pengaturan berhasil diperbarui!',
                'data' => $settings->fresh()
            ]);
        }

        return redirect()->back()->with('success', 'Pengaturan berhasil diperbarui!');

    } catch (\Exception $e) {
        Log::error('Update failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }

        return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}

    public function show($deviceCode)
    {
        Log::info('Device settings show accessed', [
            'device_code' => $deviceCode,
            'user_id' => Auth::id()
        ]);

        $device = Device::where('device_code', $deviceCode)->first();

        if (!$device) {
            Log::warning('Device not found', ['device_code' => $deviceCode]);
            return response()->json(['error' => 'Device not found'], 404);
        }

        $settings = $device->deviceSettings;

        if (!$settings) {
            Log::warning('Device settings not found', [
                'device_code' => $deviceCode,
                'device_id' => $device->id
            ]);
            return response()->json(['error' => 'Device settings not found'], 404);
        }

        return response()->json(['data' => $settings], 200);
    }
}
