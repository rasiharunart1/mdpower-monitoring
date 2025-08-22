@extends('layouts.template')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Logs Sensor Data</h1>

        <!-- Success/Error Messages -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if (isset($error))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ $error }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <!-- Filter Form -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row align-items-end">
                    <div class="col-md-2">
                        <label for="view" class="form-label">View Type:</label>
                        <select name="view" id="view" class="form-control">
                            <option value="raw" {{ $viewType == 'raw' ? 'selected' : '' }}>Data Real</option>
                            <option value="daily" {{ $viewType == 'daily' ? 'selected' : '' }}>Harian</option>
                            <option value="weekly" {{ $viewType == 'weekly' ? 'selected' : '' }}>Mingguan</option>
                            <option value="monthly" {{ $viewType == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="start_date" class="form-label">Tanggal Mulai:</label>
                        <input type="date" name="start_date" id="start_date" value="{{ $startDate }}"
                            class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label for="end_date" class="form-label">Tanggal Selesai:</label>
                        <input type="date" name="end_date" id="end_date" value="{{ $endDate }}"
                            class="form-control">
                    </div>
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-primary mr-2">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        <a href="{{ route('logs.export', request()->all()) }}" class="btn btn-success mr-2">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </a>
                        <button type="button" class="btn btn-danger" id="delete-logs-btn">
                            <i class="fas fa-trash"></i> Hapus Data
                        </button>
                        <a href="{{ route('logs.index') }}" class="btn btn-secondary">
                            <i class="fas fa-refresh"></i> Reset Filter
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Data Table -->
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    Data {{ ucfirst($viewType) }}
                    @if ($startDate || $endDate)
                        ({{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d/m/Y') : 'Awal' }} -
                        {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d/m/Y') : 'Akhir' }})
                    @endif
                    @if ($pagination)
                        <span class="badge badge-info ml-2">{{ $pagination->total() }} total records</span>
                    @endif
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="thead-dark">
                            <tr>
                                @if ($viewType == 'raw')
                                    <th>Waktu</th>
                                    <th>Battery A (V)</th>
                                    <th>Battery B (V)</th>
                                    <th>Battery C (V)</th>
                                    <th>Battery D (V)</th>
                                    <th>Temp 1 (°C)</th>
                                    <th>Temp 2 (°C)</th>
                                    <th>PLN Volt (V)</th>
                                    <th>PLN Current (A)</th>
                                    <th>PLN Watt (W)</th>

                                    <th>Relay 1</th>
                                    <th>Relay 2</th>
                                @else
                                    <th>Periode</th>
                                    <th>Avg Battery A</th>
                                    <th>Avg Battery B</th>
                                    <th>Avg Battery C</th>
                                    <th>Avg Battery D</th>
                                    <th>Avg Temp1</th>
                                    <th>Avg Temp2</th>
                                    <th>Max Temp1</th>
                                    <th>Max Temp2</th>
                                    <th>Total Energy (kWh)</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($logs as $log)
                                <tr>
                                    @if ($viewType == 'raw')
                                        <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                                        <td>{{ number_format($log->battery_a, 2) }}</td>
                                        <td>{{ number_format($log->battery_b, 2) }}</td>
                                        <td>{{ number_format($log->battery_c, 2) }}</td>
                                        <td>{{ number_format($log->battery_d, 2) }}</td>
                                        <td>{{ number_format($log->temperature_1, 2) }}</td>
                                        <td>{{ number_format($log->temperature_2, 2) }}</td>
                                        <td>{{ number_format($log->pln_volt, 2) }}</td>
                                        <td>{{ number_format($log->pln_current, 2) }}</td>
                                        <td>{{ number_format($log->pln_watt, 2) }}</td>
                                        <td>{{ number_format($log->relay_1) }}</td>
                                        <td>{{ number_format($log->relay_2) }}</td>
                                    @else
                                        <td>
                                            {{ $log->period_start->format('d/m/Y') }}
                                            @if ($viewType == 'weekly')
                                                - {{ $log->period_end->format('d/m/Y') }}
                                            @elseif($viewType == 'monthly')
                                                ({{ $log->period_start->format('F Y') }})
                                            @endif
                                        </td>
                                        <td>{{ number_format($log->getAvgBatteryA() ?? 0, 2) }}</td>
                                        <td>{{ number_format($log->getAvgBatteryB() ?? 0, 2) }}</td>
                                        <td>{{ number_format($log->getAvgBatteryC() ?? 0, 2) }}</td>
                                        <td>{{ number_format($log->getAvgBatteryD() ?? 0, 2) }}</td>
                                        <td>{{ number_format($log->getAvgTemperature1() ?? 0, 2) }}</td>
                                        <td>{{ number_format($log->getAvgTemperature2() ?? 0, 2) }}</td>
                                        <td>{{ number_format($log->getMaxTemperature1() ?? 0, 2) }}</td>
                                        <td>{{ number_format($log->getMaxTemperature2() ?? 0, 2) }}</td>
                                        <td>{{ number_format($log->getTotalEnergyConsumed() ?? 0, 2) }}</td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $viewType == 'raw' ? '10' : '10' }}" class="text-center">
                                        Tidak ada data untuk periode yang dipilih
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($pagination && $pagination->hasPages())
                    <div class="d-flex justify-content-center">
                        <nav aria-label="Page navigation">
                            <ul class="pagination">
                                {{-- Previous Page Link --}}
                                @if ($pagination->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link">Previous</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $pagination->previousPageUrl() }}"
                                            rel="prev">Previous</a>
                                    </li>
                                @endif

                                {{-- Pagination Elements --}}
                                {{-- @foreach ($pagination->getUrlRange(1, $pagination->lastPage()) as $page => $url)
                                    @if ($page == $pagination->currentPage())
                                        <li class="page-item active">
                                            <span class="page-link">{{ $page }}</span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                        </li>
                                    @endif
                                @endforeach --}}

                                {{-- Next Page Link --}}
                                @if ($pagination->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $pagination->nextPageUrl() }}"
                                            rel="next">Next</a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link">Next</span>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#delete-logs-btn').click(function() {
                const startDate = $('#start_date').val();
                const endDate = $('#end_date').val();
                const viewType = $('#view').val();

                let confirmMessage = 'Apakah Anda yakin ingin menghapus data log';
                if (startDate || endDate) {
                    confirmMessage += ' untuk periode yang dipilih';
                } else {
                    confirmMessage += ' SEMUA';
                }
                confirmMessage += '? Tindakan ini tidak dapat dibatalkan!';

                if (!confirm(confirmMessage)) return;

                $.ajax({
                    url: '{{ route('logs.destroy') }}',
                    type: 'DELETE',
                    data: {
                        view: viewType,
                        start_date: startDate,
                        end_date: endDate,
                        _token: '{{ csrf_token() }}'
                    },
                    beforeSend: function() {
                        $('#delete-logs-btn').prop('disabled', true).html(
                            '<i class="fas fa-spinner fa-spin"></i> Menghapus...');
                    },
                    success: function(response) {
                        if (response.success) {
                            // Show success message with SweetAlert or simple alert
                            alert(response.message);

                            // Reload page with current filters
                            const currentParams = new URLSearchParams(window.location.search);
                            window.location.href = '{{ route('logs.index') }}?' + currentParams
                                .toString();
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        let message = 'Gagal menghapus data';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        alert('Error: ' + message);
                        console.error('Delete error:', xhr);
                    },
                    complete: function() {
                        $('#delete-logs-btn').prop('disabled', false).html(
                            '<i class="fas fa-trash"></i> Hapus Data');
                    }
                });
            });

            // Auto dismiss alerts after 5 seconds
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);
        });
    </script>
@endpush
