@extends('layouts.template')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">Dashboard Monitoring PLTS</h1>
            {{-- <div>
                <span class="badge badge-success" id="connection-indicator">
                    <i class="fas fa-circle text-success" id="connection-dot"></i>
                    <span id="connection-text">Connected</span>
                </span>
                <small class="text-muted ml-2">Last update: <span id="last_update">{{ now()->format('H:i:s') }}</span></small>
            </div> --}}
        </div>
        <div class="row">
            <div class="col-md-6 mb-4">
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
                                <div class="text-xs text-gray-500">
                                    Auto-refresh: <span class="text-success">Every 5 seconds</span>
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
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Battery {{ strtoupper($batt) }}
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="battery_{{ $batt }}">
                                        {{ number_format($latestData?->{'battery_' . $batt} ?? 0, 2) }} V
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-primary" id="battery_{{ $batt }}_bar"
                                            style="width: {{ isset($latestData) ? min((($latestData->{'battery_' . $batt} ?? 0) / 14) * 100, 100) : 0 }}%">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- PLN Volt -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    PLN Volt
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="pln_volt">
                                    {{ number_format($latestData?->pln_volt ?? 0, 2) }} V
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
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    PLN Current
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="pln_current">
                                    {{ number_format($latestData?->pln_current ?? 0, 2) }} A
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
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    PLN Watt
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="pln_watt">
                                    {{ number_format($latestData?->pln_watt ?? 0, 2) }} W
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
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    Temperature 1
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="temperature_1">
                                    {{ number_format($latestData?->temperature_1 ?? 0, 2) }} °C
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
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    Temperature 2
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="temperature_2">
                                    {{ number_format($latestData?->temperature_2 ?? 0, 2) }} °C
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

        <div class="row">
            <!-- Device Status -->


            <!-- Device Information -->

        </div>

        <!-- Summary Stats Row -->

    </div>
@endsection

@push('scripts')
    <script>
        let updateInterval;
        let isUpdating = false;
        let consecutiveErrors = 0;
        const maxErrors = 3;

        function updateConnectionIndicator(status, message = '') {
            const indicator = $('#connection-indicator');
            const dot = $('#connection-dot');
            const text = $('#connection-text');

            // Reset classes
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
            if (isUpdating) {
                return;
            }

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
                        // Update battery data
                        ['a', 'b', 'c', 'd'].forEach(function(batt) {
                            const value = parseFloat(response.data['battery_' + batt]) || 0;
                            $('#battery_' + batt).text(value.toFixed(2) + ' V');
                            $('#battery_' + batt + '_bar').css('width', Math.min((value / 14 * 100),
                                100) + '%');
                        });

                        // Update PLN data
                        const plnVolt = parseFloat(response.data.pln_volt) || 0;
                        const plnCurrent = parseFloat(response.data.pln_current) || 0;
                        const plnWatt = parseFloat(response.data.pln_watt) || 0;

                        $('#pln_volt').text(plnVolt.toFixed(2) + ' V');
                        $('#pln_current').text(plnCurrent.toFixed(2) + ' A');
                        $('#pln_watt').text(plnWatt.toFixed(2) + ' W');

                        // Update temperature data
                        const temp1 = parseFloat(response.data.temperature_1) || 0;
                        const temp2 = parseFloat(response.data.temperature_2) || 0;

                        $('#temperature_1').text(temp1.toFixed(2) + ' °C');
                        $('#temperature_2').text(temp2.toFixed(2) + ' °C');

                        // Update device status
                        if (response.device_status) {
                            const statusBadge = $('#device_status');
                            statusBadge.text(response.device_status);
                            statusBadge.removeClass('badge-success badge-danger');
                            statusBadge.addClass(response.device_status === 'online' ? 'badge-success' :
                                'badge-danger');
                        }

                        // Update last seen
                        if (response.last_seen) {
                            $('#last_seen').text(response.last_seen);
                        }

                        // Update summary statistics


                        // Update timestamp
                        $('#last_update').text(new Date().toLocaleTimeString());
                    }
                },
                error: function(xhr, status, error) {
                    consecutiveErrors++;
                    updateConnectionIndicator('error');

                    console.error('Dashboard update error:', error);

                    // Stop updates after too many consecutive errors
                    if (consecutiveErrors >= maxErrors) {
                        clearInterval(updateInterval);
                        // Restart after 30 seconds
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

        // Initialize dashboard
        $(document).ready(function() {
            // Run first update immediately
            updateDashboard();

            // Set up interval for auto-update every 5 seconds
            updateInterval = setInterval(updateDashboard, 5000);

            // Handle page visibility change to pause/resume updates
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    if (updateInterval) {
                        clearInterval(updateInterval);
                    }
                } else {
                    if (!updateInterval) {
                        updateInterval = setInterval(updateDashboard, 1000);
                        updateDashboard(); // Update immediately when page becomes visible
                    }
                }
            });
        });

        // Clean up on page unload
        $(window).on('beforeunload', function() {
            if (updateInterval) {
                clearInterval(updateInterval);
            }
        });
    </script>
@endpush
