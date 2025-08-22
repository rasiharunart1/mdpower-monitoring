@extends('layouts.template')

@section('content')
    @php
        // Define sensor ranges directly in code (no external config)
        $ranges = [
            'battery_a' => ['min' => 10.5, 'max' => 14.4],
            'battery_b' => ['min' => 10.5, 'max' => 14.4],
            'battery_c' => ['min' => 10.5, 'max' => 14.4],
            'battery_d' => ['min' => 10.5, 'max' => 14.4],

            'pln_volt' => ['min' => 180, 'max' => 240],
            'pln_current' => ['min' => 0, 'max' => 32],
            'pln_watt' => ['min' => 0, 'max' => 3500],

            'temperature_1' => ['min' => 0, 'max' => 60],
            'temperature_2' => ['min' => 0, 'max' => 60],
        ];

        // Helper to compute percent with clamping
        function sensor_pct($key, $value, $ranges)
        {
            $range = $ranges[$key] ?? ['min' => 0, 'max' => 100];
            $min = (float) ($range['min'] ?? 0);
            $max = (float) ($range['max'] ?? 100);
            $den = max($max - $min, 0.000001);
            $pct = (($value - $min) / $den) * 100;
            return max(0, min(100, $pct));
        }
    @endphp

    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">Dashboard Monitoring PLTS</h1>
        </div>

        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Device Status
                                </div>
                                <div class="row no-gutters align-items-center">
                                    <div class="col-auto">
                                        <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                            <span
                                                class="badge badge-{{ $device->status == 'online' ? 'success' : 'danger' }}"
                                                id="device_status">
                                                {{ $device->status }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500 mb-1">
                                    Last seen: <span
                                        id="last_seen">{{ $device->last_seen?->diffForHumans() ?? 'Never' }}</span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-wifi fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Battery Cards -->
            @foreach (['a', 'b', 'c', 'd'] as $batt)
                @php
                    $key = 'battery_' . $batt;
                    $val = (float) ($latestData?->$key ?? 0);
                    $pct = sensor_pct($key, $val, $ranges);
                @endphp
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Battery {{ strtoupper($batt) }}
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="battery_{{ $batt }}">
                                        {{ number_format($val, 2) }} V
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <div class="progress progress-sm mr-2" style="width: 90px;">
                                        <div class="progress-bar bg-primary" id="battery_{{ $batt }}_bar"
                                            style="width: {{ $pct }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- PLN Volt -->
            @php
                $plnVoltVal = (float) ($latestData->pln_volt ?? 0);
                $plnVoltPct = sensor_pct('pln_volt', $plnVoltVal, $ranges);
            @endphp
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    PLN Volt
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="pln_volt">
                                    {{ number_format($plnVoltVal, 2) }} V
                                </div>
                                <div class="progress progress-sm mt-2">
                                    <div class="progress-bar bg-success" id="pln_volt_bar"
                                        style="width: {{ $plnVoltPct }}%"></div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-bolt fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PLN Current -->
            @php
                $plnCurrentVal = (float) ($latestData->pln_current ?? 0);
                $plnCurrentPct = sensor_pct('pln_current', $plnCurrentVal, $ranges);
            @endphp
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    PLN Current
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="pln_current">
                                    {{ number_format($plnCurrentVal, 2) }} A
                                </div>
                                <div class="progress progress-sm mt-2">
                                    <div class="progress-bar bg-warning" id="pln_current_bar"
                                        style="width: {{ $plnCurrentPct }}%"></div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-flash fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PLN Watt -->
            @php
                $plnWattVal = (float) ($latestData->pln_watt ?? 0);
                $plnWattPct = sensor_pct('pln_watt', $plnWattVal, $ranges);
            @endphp
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    PLN Watt
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="pln_watt">
                                    {{ number_format($plnWattVal, 2) }} W
                                </div>
                                <div class="progress progress-sm mt-2">
                                    <div class="progress-bar bg-info" id="pln_watt_bar"
                                        style="width: {{ $plnWattPct }}%"></div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-plug fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Temperature 1 -->
            @php
                $t1Val = (float) ($latestData->temperature_1 ?? 0);
                $t1Pct = sensor_pct('temperature_1', $t1Val, $ranges);
            @endphp
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    Temperature 1
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="temperature_1">
                                    {{ number_format($t1Val, 2) }} °C
                                </div>
                                <div class="progress progress-sm mt-2">
                                    <div class="progress-bar bg-danger" id="temperature_1_bar"
                                        style="width: {{ $t1Pct }}%"></div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-thermometer-half fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Temperature 2 -->
            @php
                $t2Val = (float) ($latestData->temperature_2 ?? 0);
                $t2Pct = sensor_pct('temperature_2', $t2Val, $ranges);
            @endphp
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    Temperature 2
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="temperature_2">
                                    {{ number_format($t2Val, 2) }} °C
                                </div>
                                <div class="progress progress-sm mt-2">
                                    <div class="progress-bar bg-danger" id="temperature_2_bar"
                                        style="width: {{ $t2Pct }}%"></div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-thermometer-half fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        // Ranges defined in code (keep in sync with PHP above if you change)
        const ranges = {
            battery_a: {
                min: 10.5,
                max: 14.4
            },
            battery_b: {
                min: 10.5,
                max: 14.4
            },
            battery_c: {
                min: 10.5,
                max: 14.4
            },
            battery_d: {
                min: 10.5,
                max: 14.4
            },

            pln_volt: {
                min: 180,
                max: 240
            },
            pln_current: {
                min: 0,
                max: 32
            },
            pln_watt: {
                min: 0,
                max: 3500
            },

            temperature_1: {
                min: 0,
                max: 60
            },
            temperature_2: {
                min: 0,
                max: 60
            },
        };

        function clamp(v, lo, hi) {
            return Math.max(lo, Math.min(hi, v));
        }

        function pct(key, value) {
            const r = ranges[key] || {
                min: 0,
                max: 100
            };
            const den = Math.max(r.max - r.min, 1e-6);
            return clamp(((value - r.min) / den) * 100, 0, 100);
        }

        let updateInterval;
        let isUpdating = false;
        let consecutiveErrors = 0;
        const maxErrors = 3;

        function updateConnectionIndicator(status) {
            const indicator = $('#connection-indicator');
            const dot = $('#connection-dot');
            const text = $('#connection-text');

            indicator.removeClass('badge-success badge-warning badge-danger');
            dot.removeClass('text-success text-warning text-danger');

            switch (status) {
                case 'connected':
                    indicator.addClass('badge-success');
                    dot.addClass('text-success');
                    text.text('Connected');
                    break;
                case 'updating':
                    indicator.addClass('badge-warning');
                    dot.addClass('text-warning');
                    text.text('Updating...');
                    break;
                case 'error':
                    indicator.addClass('badge-danger');
                    dot.addClass('text-danger');
                    text.text('Connection Error');
                    break;
            }
        }

        function updateDashboard() {
            if (isUpdating) return;

            isUpdating = true;
            updateConnectionIndicator('updating');

            $.ajax({
                url: '{{ route('dashboard.realtime') }}',
                method: 'GET',
                timeout: 8000,
                success: function(response) {
                    consecutiveErrors = 0;
                    updateConnectionIndicator('connected');

                    if (response.data) {
                        // Batteries
                        ['a', 'b', 'c', 'd'].forEach(function(batt) {
                            const key = 'battery_' + batt;
                            const value = parseFloat(response.data[key]) || 0;
                            $('#' + key).text(value.toFixed(2) + ' V');
                            $('#' + key + '_bar').css('width', pct(key, value) + '%');
                        });

                        // PLN
                        const plnVolt = parseFloat(response.data.pln_volt) || 0;
                        const plnCurrent = parseFloat(response.data.pln_current) || 0;
                        const plnWatt = parseFloat(response.data.pln_watt) || 0;

                        $('#pln_volt').text(plnVolt.toFixed(2) + ' V');
                        $('#pln_volt_bar').css('width', pct('pln_volt', plnVolt) + '%');

                        $('#pln_current').text(plnCurrent.toFixed(2) + ' A');
                        $('#pln_current_bar').css('width', pct('pln_current', plnCurrent) + '%');

                        $('#pln_watt').text(plnWatt.toFixed(2) + ' W');
                        $('#pln_watt_bar').css('width', pct('pln_watt', plnWatt) + '%');

                        // Temperatures
                        const temp1 = parseFloat(response.data.temperature_1) || 0;
                        const temp2 = parseFloat(response.data.temperature_2) || 0;

                        $('#temperature_1').text(temp1.toFixed(2) + ' °C');
                        $('#temperature_1_bar').css('width', pct('temperature_1', temp1) + '%');

                        $('#temperature_2').text(temp2.toFixed(2) + ' °C');
                        $('#temperature_2_bar').css('width', pct('temperature_2', temp2) + '%');

                        // Device status
                        if (response.device_status) {
                            const statusBadge = $('#device_status');
                            statusBadge.text(response.device_status);
                            statusBadge.removeClass('badge-success badge-danger');
                            statusBadge.addClass(response.device_status === 'online' ? 'badge-success' :
                                'badge-danger');
                        }

                        // Last seen
                        if (response.last_seen) {
                            $('#last_seen').text(response.last_seen);
                        }

                        // Timestamp (optional)
                        $('#last_update').text(new Date().toLocaleTimeString());
                    }
                },
                error: function(xhr, status, error) {
                    consecutiveErrors++;
                    updateConnectionIndicator('error');
                    console.error('Dashboard update error:', error);

                    if (consecutiveErrors >= maxErrors) {
                        clearInterval(updateInterval);
                        setTimeout(() => {
                            consecutiveErrors = 0;
                            updateInterval = setInterval(updateDashboard, 5000);
                        }, 10000);
                    }
                },
                complete: function() {
                    isUpdating = false;
                }
            });
        }

        $(document).ready(function() {
            updateDashboard();
            updateInterval = setInterval(updateDashboard, 1000);

            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    if (updateInterval) clearInterval(updateInterval);
                    updateInterval = null;
                } else {
                    if (!updateInterval) {
                        updateInterval = setInterval(updateDashboard, 1000);
                        updateDashboard();
                    }
                }
            });
        });

        $(window).on('beforeunload', function() {
            if (updateInterval) clearInterval(updateInterval);
        });
    </script>
@endpush
