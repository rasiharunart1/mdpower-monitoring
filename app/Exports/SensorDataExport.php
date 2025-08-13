<?php

namespace App\Exports;

use App\Models\SensorData;
use App\Models\SensorDataAggregate;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SensorDataExport implements FromCollection, WithHeadings
{
    protected $deviceId, $startDate, $endDate, $viewType;

    public function __construct($deviceId, $startDate, $endDate, $viewType)
    {
        $this->deviceId = $deviceId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->viewType = $viewType;
    }

    public function collection()
    {
        switch ($this->viewType) {
            case 'raw':
                $query = SensorData::where('device_id', $this->deviceId);
                if ($this->startDate) $query->whereDate('created_at', '>=', $this->startDate);
                if ($this->endDate) $query->whereDate('created_at', '<=', $this->endDate);
                return $query->orderBy('created_at')->get([
                    'created_at',
                    'battery_a',
                    'battery_b',
                    'battery_c',
                    'battery_d',
                    'temperature_1',
                    'temperature_2',
                    'pln_volt',
                    'pln_current',
                    'pln_watt'
                ]);
            case 'daily':
            case 'weekly':
            case 'monthly':
                $query = SensorDataAggregate::where('device_id', $this->deviceId)
                    ->where('aggregate_type', $this->viewType);
                if ($this->startDate) $query->whereDate('period_start', '>=', $this->startDate);
                if ($this->endDate) $query->whereDate('period_end', '<=', $this->endDate);
                return $query->orderBy('period_start')->get([
                    'period_start',
                    'battery_a_avg',
                    'battery_b_avg',
                    'battery_c_avg',
                    'battery_d_avg',
                    'temperature_1_avg',
                    'temperature_2_avg',
                    'pln_volt_avg',
                    'pln_current_avg',
                    'pln_watt_sum'
                ]);
            default:
                return collect();
        }
    }

    public function headings(): array
    {
        switch ($this->viewType) {
            case 'raw':
                return [
                    'Tanggal',
                    'Battery A (V)',
                    'Battery B (V)',
                    'Battery C (V)',
                    'Battery D (V)',
                    'Temperature 1 (°C)',
                    'Temperature 2 (°C)',
                    'PLN Volt (V)',
                    'PLN Current (A)',
                    'PLN Watt (W)',
                ];
            case 'daily':
            case 'weekly':
            case 'monthly':
                return [
                    'Periode',
                    'Battery A Rata-rata (V)',
                    'Battery B Rata-rata (V)',
                    'Battery C Rata-rata (V)',
                    'Battery D Rata-rata (V)',
                    'Temperature 1 Rata-rata (°C)',
                    'Temperature 2 Rata-rata (°C)',
                    'PLN Volt Rata-rata (V)',
                    'PLN Current Rata-rata (A)',
                    'Total PLN Watt (W)',
                ];
            default:
                return [];
        }
    }
}
