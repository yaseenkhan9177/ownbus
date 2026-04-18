@extends('layouts.driver')

@section('title', 'Driver Login')

@push('extra-styles')
<style>
    body {
        background: #0f172a;
    }

    .pin-pad {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
        max-width: 270px;
        margin: 0 auto;
    }

    .pin-btn {
        aspect-ratio: 1;
        background: #1e293b;
        border: 1.5px solid rgba(255, 255, 255, 0.06);
        color: #f1f5f9;
        font-size: 1.5rem;
        font-weight: 700;
        border-radius: 1rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s;
    }

    .pin-btn:active {
        background: rgba(20, 184, 166, 0.2);
        border-color: #14b8a6;
        transform: scale(0.95);
    }

    .pin-btn.clear {
        font-size: 0.7rem;
        color: #64748b;
    }

    .pin-btn.backspace {
        font-size: 1rem;
        color: #94a3b8;
    }

    .pin-dots {
        display: flex;
        justify-content: center;
        gap: 0.875rem;
        margin: 2rem 0 2.5rem;
    }

    .pin-dot {
        width: 0.875rem;
        height: 0.875rem;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        border: 2px solid rgba(255, 255, 255, 0.15);
        transition: all 0.2s;
    }

    .pin-dot.filled {
        background: #14b8a6;
        border-color: #14b8a6;
        box-shadow: 0 0 12px rgba(20, 184, 166, 0.5);
    }
</style>
@endpush

@section('content')
<div style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 70vh; text-align: center; padding: 2rem 0;">

    {{-- Logo / Icon --}}
    <div style="width: 4rem; height: 4rem; border-radius: 1.25rem; background: linear-gradient(135deg, #14b8a6, #0d9488); display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem; box-shadow: 0 0 30px rgba(20,184,166,0.3);">
        <svg width="28" height="28" fill="none" stroke="#fff" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M3 10h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
    </div>

    <h2 style="font-size: 1.5rem; font-weight: 800; color: #f1f5f9; margin: 0 0 0.25rem;">Welcome, Driver</h2>
    <p style="font-size: 0.8rem; color: #64748b; margin: 0 0 2rem;">Enter your phone number and 4-digit PIN</p>

    @if($errors->any())
    <div class="alert-error" style="text-align: left; width: 100%; max-width: 320px;">
        @foreach($errors->all() as $error){{ $error }}@endforeach
    </div>
    @endif

    <form method="POST" action="{{ route('driver.login.submit') }}" style="width: 100%; max-width: 320px;">
        @csrf
        {{-- Phone Input --}}
        <div style="margin-bottom: 1.5rem; text-align: left;">
            <label class="form-label">📱 Phone Number</label>
            <input type="tel" name="phone" value="{{ old('phone') }}"
                class="form-input" placeholder="+971 50 123 4567"
                autocomplete="tel" required>
        </div>

        {{-- PIN Display --}}
        <div class="pin-dots">
            <div class="pin-dot" id="dot-0"></div>
            <div class="pin-dot" id="dot-1"></div>
            <div class="pin-dot" id="dot-2"></div>
            <div class="pin-dot" id="dot-3"></div>
        </div>

        {{-- Hidden PIN input --}}
        <input type="hidden" name="pin" id="pin-input">

        {{-- PIN Pad --}}
        <div class="pin-pad">
            @foreach([1,2,3,4,5,6,7,8,9] as $n)
            <button type="button" class="pin-btn" onclick="addDigit('{{ $n }}')">{{ $n }}</button>
            @endforeach
            <div></div>
            <button type="button" class="pin-btn" onclick="addDigit('0')">0</button>
            <button type="button" class="pin-btn backspace" onclick="removeDigit()">
                <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M3 12l6.414 6.414a2 2 0 001.414.586H19a2 2 0 002-2V7a2 2 0 00-2-2h-8.172a2 2 0 00-1.414.586L3 12z" />
                </svg>
            </button>
        </div>

        <button type="submit" id="submit-btn" class="btn btn-teal" style="margin-top: 2rem; display: none;">
            🔓 Log In
        </button>
    </form>
</div>

@push('scripts')
<script>
    let pin = '';

    function addDigit(d) {
        if (pin.length >= 4) return;
        pin += d;
        updateDots();
        if (pin.length === 4) {
            document.getElementById('submit-btn').style.display = 'block';
            document.getElementById('submit-btn').style.animation = 'fadeIn 0.3s';
            // Auto submit after brief delay
            setTimeout(() => document.querySelector('form').submit(), 300);
        }
    }

    function removeDigit() {
        pin = pin.slice(0, -1);
        document.getElementById('submit-btn').style.display = 'none';
        updateDots();
    }

    function updateDots() {
        document.getElementById('pin-input').value = pin;
        for (let i = 0; i < 4; i++) {
            const dot = document.getElementById('dot-' + i);
            dot.className = 'pin-dot' + (i < pin.length ? ' filled' : '');
        }
    }
</script>
@endpush
@endsection