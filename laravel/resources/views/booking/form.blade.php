@extends('layouts.main')

@section('title', 'Booking - Touristik')

@section('content')
<nav class="breadcrumbs" aria-label="Breadcrumb">
    <a href="/" data-t="breadcrumb_home">Home</a>
    <span class="breadcrumb-sep">&#8250;</span>
    <a href="/hotels/search">Search</a>
    <span class="breadcrumb-sep">&#8250;</span>
    <span class="breadcrumb-current">Booking</span>
</nav>

<section class="booking-section">

@if(!$checkData && $error)
    <!-- Error / No Selection State -->
    <div class="booking-error-page">
        <div class="booking-icon">&#128269;</div>
        <h2>No hotel selected</h2>
        <p class="alert error">{{ $error }}</p>
        <a href="/" class="btn">&#8592; Search Hotels</a>
    </div>

@elseif(!$checkData && !$error)
    <!-- Rate No Longer Available -->
    <div class="booking-error-page">
        <div class="booking-icon">&#128683;</div>
        <h2>Rate No Longer Available</h2>
        <p>The selected rate has expired or is no longer available. Please search again.</p>
        <a href="/" class="btn">&#8592; Search Again</a>
    </div>

@elseif($checkData)
    <!-- Booking Confirmation Form -->
    <div class="booking-flow">
        <div class="booking-steps">
            <div class="booking-step done">&#10003; Rate Verified</div>
            <div class="booking-step active">2. Guest Details</div>
            <div class="booking-step">3. Confirmation</div>
        </div>

        <div class="booking-layout">
            <div class="booking-summary">
                <h3>&#127960; Booking Summary</h3>
                @if(!empty($checkData['hotel']['image']))
                    <div class="booking-hotel-image">
                        <img src="{{ $checkData['hotel']['image'] }}" alt="{{ $checkData['hotel']['name'] ?? '' }}">
                    </div>
                @endif
                <div class="booking-detail">
                    <h4>{{ $checkData['hotel']['name'] ?? $hotelName }}</h4>
                    @if(!empty($checkData['hotel']['category']))
                        <p class="booking-category">{{ $checkData['hotel']['category'] }}</p>
                    @endif
                    @if(!empty($checkData['hotel']['destination']))
                        <p class="booking-dest">&#128205; {{ $checkData['hotel']['destination'] }}</p>
                    @endif
                </div>
                <div class="booking-info-grid">
                    <div class="booking-info-item">
                        <span class="booking-label">Check-in</span>
                        <span class="booking-value">{!! !empty($checkData['rate']['check_in']) ? \Carbon\Carbon::parse($checkData['rate']['check_in'])->format('D, M d Y') : 'N/A' !!}</span>
                    </div>
                    <div class="booking-info-item">
                        <span class="booking-label">Check-out</span>
                        <span class="booking-value">{!! !empty($checkData['rate']['check_out']) ? \Carbon\Carbon::parse($checkData['rate']['check_out'])->format('D, M d Y') : 'N/A' !!}</span>
                    </div>
                    <div class="booking-info-item">
                        <span class="booking-label">Room</span>
                        <span class="booking-value">{{ $checkData['room']['name'] ?? '' }}</span>
                    </div>
                    <div class="booking-info-item">
                        <span class="booking-label">Board</span>
                        <span class="booking-value">{{ $checkData['rate']['board'] ?? '' }}</span>
                    </div>
                </div>

                @if(!empty($checkData['rate']['cancellation_policies']))
                    <div class="booking-cancellation">
                        <h5>&#128196; Cancellation Policy</h5>
                        @foreach($checkData['rate']['cancellation_policies'] as $policy)
                            <p>From {{ \Carbon\Carbon::parse($policy['from'] ?? '')->format('M d, Y') }}: {{ $policy['amount'] ?? '' }} {{ $checkData['rate']['currency'] ?? '' }}</p>
                        @endforeach
                    </div>
                @endif

                @if(!empty($checkData['rate']['rateComments']))
                    <div class="booking-rate-comments">
                        <h5>&#128196; Important Information</h5>
                        <p>{!! nl2br(e($checkData['rate']['rateComments'])) !!}</p>
                    </div>
                @endif

                <div class="booking-total">
                    <span>Total Price</span>
                    <span class="booking-total-price">{{ $checkData['rate']['currency'] ?? 'EUR' }} {!! number_format($checkData['rate']['net'] ?? 0, 2) !!}</span>
                </div>
            </div>

            <div class="booking-form-panel">
                <h3>&#128100; Guest Details</h3>
                @if($error)
                    <div class="alert error">{{ $error }}</div>
                @endif
                <form method="POST" action="/booking/store" class="booking-form">
                    @csrf
                    <div class="form-row">
                        <div class="form-group">
                            <label for="holder_name">First Name *</label>
                            <input type="text" id="holder_name" name="holder_name" value="{{ old('holder_name') }}" required placeholder="Guest first name">
                        </div>
                        <div class="form-group">
                            <label for="holder_surname">Last Name *</label>
                            <input type="text" id="holder_surname" name="holder_surname" value="{{ old('holder_surname') }}" required placeholder="Guest last name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="holder_email">Email</label>
                        <input type="email" id="holder_email" name="holder_email" value="{{ old('holder_email') }}" placeholder="your@email.com">
                    </div>
                    <div class="form-group">
                        <label for="holder_phone">Phone</label>
                        <input type="tel" id="holder_phone" name="holder_phone" value="{{ old('holder_phone') }}" placeholder="+374 XX XXX XXX">
                    </div>
                    <div class="form-group">
                        <label for="remark">Special Requests</label>
                        <textarea id="remark" name="remark" rows="3" placeholder="Any special requests...">{{ old('remark') }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-book-confirm">&#128274; Confirm Booking</button>
                    <p class="booking-disclaimer">By confirming, you agree to the cancellation policy shown above.</p>
                </form>
            </div>
        </div>
    </div>

@endif

</section>
@endsection
