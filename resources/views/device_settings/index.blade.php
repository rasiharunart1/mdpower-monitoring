@extends('layouts.template')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Pengaturan Device</h1>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="alert alert-info">
            <b>Device Code:</b> {{ $device->device_code }}
        </div>

        <!-- FORM SUBMIT BIASA - TANPA AJAX -->
        <form action="{{ route('device-settings.update') }}" method="POST" id="settings-form">
            @csrf

            <div class="form-group">
                <label>Threshold Suhu 1</label>
                <input type="number" step="0.1" name="temp1_threshold"
                    value="{{ old('temp1_threshold', $settings->temp1_threshold) }}" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Threshold Suhu 2</label>
                <input type="number" step="0.1" name="temp2_threshold"
                    value="{{ old('temp2_threshold', $settings->temp2_threshold) }}" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Email Notifikasi</label>
                <input type="hidden" name="email_notification" value="0">
                <input type="checkbox" name="email_notification" value="1"
                    {{ old('email_notification', $settings->email_notification) ? 'checked' : '' }}>
            </div>
            <div class="form-group">
                <label>Interval Pengambilan Data (detik)</label>
                <input type="number" step="0.1" name="data_collection_interval"
                    value="{{ old('data_collection_interval', $settings->data_collection_interval) }}" min="0.1"
                    max="3600" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>

        <!-- DEBUG INFO -->
        <div class="mt-4">
            <small class="text-muted">
                Debug: User = {{ Auth::user()->email ?? 'unknown' }} |
                Time = {{ now()->toDateTimeString() }} |
                Device = {{ $device->id ?? 'none' }} |
                Settings = {{ $settings->id ?? 'none' }}
            </small>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        console.log('=== SIMPLE FORM TEST ===');
        console.log('Time:', '{{ now()->toDateTimeString() }}');
        console.log('User:', '{{ Auth::user()->email ?? 'unknown' }}');
        console.log('Form action:', $('#settings-form').attr('action'));

        // Simple form validation
        $('#settings-form').on('submit', function(e) {
            console.log('Form submitting...');
            console.log('Form data:', $(this).serialize());

            var temp1 = $('input[name="temp1_threshold"]').val();
            var temp2 = $('input[name="temp2_threshold"]').val();

            if (!temp1 || !temp2) {
                alert('Mohon isi semua field yang diperlukan');
                e.preventDefault();
                return false;
            }

            console.log('Form validation passed, submitting...');
            return true;
        });
    </script>
@endsection
