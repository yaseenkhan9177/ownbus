@extends('layouts.driver')

@section('title', 'Breakdown Report')
@section('page-title', '🚨 Report Issue')

@section('content')

<div style="background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.2); border-radius: 0.875rem; padding: 1rem; margin-bottom: 1.25rem;">
    <p style="font-size: 0.75rem; color: #f87171; font-weight: 600; margin: 0;">
        🚨 Use this form to report a breakdown, accident, or any vehicle issue immediately. Your manager will be notified.
    </p>
</div>

<div class="card">
    <form method="POST" action="{{ route('driver.breakdown.store') }}" enctype="multipart/form-data">
        @csrf

        {{-- Location --}}
        <div style="margin-bottom: 1rem;">
            <label class="form-label">📍 Your Current Location *</label>
            <input type="text" name="location" class="form-input" id="location-input"
                value="{{ old('location') }}" placeholder="Street / Area / nearby landmark" required>
            <button type="button" onclick="getLocation()" style="background: none; border: none; color: #14b8a6; font-size: 0.72rem; font-weight: 700; cursor: pointer; padding: 0.3rem 0; text-transform: uppercase; letter-spacing: 0.05em;">
                📌 Use GPS Location
            </button>
        </div>

        {{-- Description --}}
        <div style="margin-bottom: 1rem;">
            <label class="form-label">Description of Issue *</label>
            <textarea name="description" class="form-input" rows="4"
                placeholder="Describe the problem in detail: flat tyre, engine issue, accident, etc." required>{{ old('description') }}</textarea>
        </div>

        {{-- Photo Upload --}}
        <div style="margin-bottom: 1.5rem;">
            <label class="form-label">📷 Photo of Issue</label>
            <label for="breakdown-photo" style="display: block; cursor: pointer;">
                <div id="bphoto-preview-box" style="border: 2px dashed rgba(239,68,68,0.3); border-radius: 0.875rem; padding: 2rem; text-align: center; transition: all 0.2s;">
                    <div id="bupload-placeholder">
                        <p style="font-size: 1.75rem; margin: 0 0 0.5rem;">📸</p>
                        <p style="font-size: 0.8rem; color: #f87171; font-weight: 600; margin: 0;">Tap to take photo</p>
                        <p style="font-size: 0.7rem; color: #64748b; margin: 0.25rem 0 0;">JPG, PNG up to 5MB</p>
                    </div>
                    <img id="bphoto-preview" style="display: none; max-width: 100%; border-radius: 0.75rem;">
                </div>
            </label>
            <input type="file" id="breakdown-photo" name="photo" accept="image/*" capture="environment" style="display: none;" onchange="previewBreakdownPhoto(this)">
        </div>

        <button type="submit" class="btn btn-danger">🚨 Send Emergency Report</button>
    </form>
</div>

@push('scripts')
<script>
    function previewBreakdownPhoto(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = (e) => {
                document.getElementById('bupload-placeholder').style.display = 'none';
                const preview = document.getElementById('bphoto-preview');
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function getLocation() {
        if (!navigator.geolocation) return;
        navigator.geolocation.getCurrentPosition((pos) => {
            const lat = pos.coords.latitude.toFixed(5);
            const lng = pos.coords.longitude.toFixed(5);
            document.getElementById('location-input').value = `GPS: ${lat}, ${lng}`;
        }, () => {
            alert('Could not get GPS location. Please type your location manually.');
        });
    }
</script>
@endpush
@endsection