<?php

namespace App\Http\Controllers;

use App\Exports\SensorDataExport;
use App\Models\SensorData;
use App\Models\SensorDataAggregate;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LogsController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $device = $user->device;

        if (!$device) {
            return view('logs.index')->with('error', 'tidak ada device untuk user ini');
        }

        $viewType = $request->get('view', 'raw');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $logs = collect();
        $pagination = null;

        switch ($viewType) {
            case 'raw':
                $query = $device->sensorData();
                if ($startDate) {
                    $query->whereDate('created_at', '>=', $startDate);
                }
                if ($endDate) {
                    $query->whereDate('created_at', '<=', $endDate);
                }
                $pagination = $query->latest()->paginate(50);
                $logs = $pagination->items();
                break;

            case 'daily':
                $query = SensorDataAggregate::where('device_id', $device->id)->daily();
                if ($startDate) {
                    $query->whereDate('period_start', '>=', $startDate);
                }
                if ($endDate) {
                    $query->whereDate('period_start', '<=', $endDate);
                }
                $pagination = $query->latest('period_start')->paginate(31);
                $logs = $pagination->items();
                break;

            case 'weekly':
                $query = SensorDataAggregate::where('device_id', $device->id)->weekly();
                if ($startDate) {
                    $query->whereDate('period_start', '>=', $startDate);
                }
                if ($endDate) {
                    $query->whereDate('period_start', '<=', $endDate);
                }
                $pagination = $query->latest('period_start')->paginate(20);
                $logs = $pagination->items();
                break;

            case 'monthly':
                $query = SensorDataAggregate::where('device_id', $device->id)->monthly();
                if ($startDate) {
                    $query->whereDate('period_start', '>=', $startDate);
                }
                if ($endDate) {
                    $query->whereDate('period_start', '<=', $endDate);
                }
                $pagination = $query->latest('period_start')->paginate(12);
                $logs = $pagination->items();
                break;

            default:
                // fallback
                break;
        }

        return view('logs.index', compact(
            'logs',
            'device',
            'viewType',
            'startDate',
            'endDate',
            'pagination'
        ));
    }

    public function export(Request $request)
    {
        $user = auth()->user();
        $device = $user->device;

        if (!$device) {
            return redirect()->back()->with('error', 'Device tidak ditemukan');
        }

        $viewType = $request->get('view', 'raw');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $filename = "data-sensor-{$viewType}-" . now()->format('Y-m-d-H-i-s') . '.xlsx';

        return Excel::download(
            new SensorDataExport($device->id, $startDate, $endDate, $viewType),
            $filename
        );
    }

    public function destroy(Request $request)
    {
        try {
            $user = auth()->user();
            $device = $user->device;

            if (!$device) {
                return response()->json([
                    'success' => false,
                    'message' => 'Device tidak ditemukan'
                ], 404);
            }

            $viewType = $request->get('view', 'raw');
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');

            $deleted = 0;

            if ($viewType == 'raw') {
                $query = SensorData::where('device_id', $device->id);

                if ($startDate) {
                    $query->whereDate('created_at', '>=', $startDate);
                }
                if ($endDate) {
                    $query->whereDate('created_at', '<=', $endDate);
                }

                $deleted = $query->delete();
            } else {
                $query = SensorDataAggregate::where('device_id', $device->id)
                    ->where('aggregate_type', $viewType);

                if ($startDate) {
                    $query->whereDate('period_start', '>=', $startDate);
                }
                if ($endDate) {
                    $query->whereDate('period_start', '<=', $endDate);
                }

                $deleted = $query->delete();
            }

            return response()->json([
                'success' => true,
                'message' => "Berhasil menghapus {$deleted} record",
                'deleted' => $deleted
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkDelete(Request $request)
    {
        try {
            $user = auth()->user();
            $device = $user->device;

            if (!$device) {
                return response()->json([
                    'success' => false,
                    'message' => 'Device tidak ditemukan'
                ], 404);
            }

            $ids = $request->get('ids', []);
            $viewType = $request->get('view', 'raw');

            if (empty($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada ID yang dipilih untuk dihapus'
                ], 400);
            }

            $deleted = 0;

            if ($viewType == 'raw') {
                $deleted = SensorData::where('device_id', $device->id)
                    ->whereIn('id', $ids)
                    ->delete();
            } else {
                $deleted = SensorDataAggregate::where('device_id', $device->id)
                    ->where('aggregate_type', $viewType)
                    ->whereIn('id', $ids)
                    ->delete();
            }

            return response()->json([
                'success' => true,
                'message' => "Berhasil menghapus {$deleted} record",
                'deleted' => $deleted
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
