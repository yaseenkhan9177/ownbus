@extends('layouts.driver')

@section('title', 'Fuel Upload')
@section('page-title', '⛽ Fuel Upload')

@section('content')

<div class="card">
    <p style="font-size: 0.75rem; color: #64748b; margin: 0 0 1.5rem; line-height: 1.6;">
        Submit your fuel receipt for reimbursement or company records. A photo of the receipt is recommended.
    </p>

    <form method="POST" action="{{ route('driver.fuel.store') }}" enctype="multipart/form-data">
        @csrf

        {{-- Fuel Liters --}}
        <div style="margin-bottom: 1rem;">
            <label class="form-label">Fuel Amount (Liters) *</label>
            <input type="number" name="fuel_liters" class="form-input" step="0.1" min="1"
                value="{{ old('fuel_liters') }}" placeholder="e.g. 45.5" required>
        </div>

        {{-- Fuel Cost --}}
        <div style="margin-bottom: 1rem;">
            <label class="form-label">Total Cost (AED) *</label>
            <input type="number" name="fuel_cost" class="form-input" step="0.01" min="0"
                value="{{ old('fuel_cost') }}" placeholder="e.g. 180.00" required>
        </div>

        {{-- Odometer --}}
        <div style="margin-bottom: 1rem;">
            <label class="form-label">Odometer Reading (km)</label>
            <input type="number" name="odometer" class="form-input" min="0"
                value="{{ old('odometer') }}" placeholder="e.g. 54320">
        </div>

        {{-- Photo Upload --}}
        <div style="margin-bottom: 1rem;">
            <label class="form-label">📷 Receipt Photo</label>
            <label for="photo-input" style="display: block; cursor: pointer;">
                <div id="photo-preview-box" style="border: 2px dashed rgba(20,184,166,0.3); border-radius: 0.875rem; padding: 2rem; text-align: center; transition: all 0.2s;">
                    <div id="upload-placeholder">
                        <p style="font-size: 1.75rem; margin: 0 0 0.5rem;">📷</p>
                        <p style="font-size: 0.8rem; color: #14b8a6; font-weight: 600; margin: 0;">Tap to upload photo</p>
                        <p style="font-size: 0.7rem; color: #64748b; margin: 0.25rem 0 0;">JPG, PNG up to 5MB</p>
                    </div>
                    <img id="photo-preview" style="display: none; max-width: 100%; border-radius: 0.75rem;">
                </div>
            </label>
            <input type="file" id="photo-input" name="photo" accept="image/*" capture="environment" style="display: none;" onchange="previewPhoto(this)">
        </div>

        {{-- Notes --}}
        <div style="margin-bottom: 1.5rem;">
            <label class="form-label">Notes (Optional)</label>
            <textarea name="notes" class="form-input" rows="2" placeholder="Station name, location, etc.">{{ old('notes') }}</textarea>
        </div>

        <button type="submit" class="btn btn-teal">⛽ Submit Fuel Report</button>
    </form>
</div>

@push('scripts')
<script>
    function previewPhoto(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = (e) => {
                document.getElementById('upload-placeholder').style.display = 'none';
                const preview = document.getElementById('photo-preview');
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
@endsection