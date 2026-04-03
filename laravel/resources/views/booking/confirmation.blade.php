@extends('layouts.main')

@section('title', 'Booking Confirmation - Touristik')

@section('content')
<nav class="breadcrumbs" aria-label="Breadcrumb">
    <a href="/" data-t="breadcrumb_home">Home</a>
    <span class="breadcrumb-sep">&#8250;</span>
    <a href="/hotels/search">Search</a>
    <span class="breadcrumb-sep">&#8250;</span>
    <span class="breadcrumb-current">Booking Confirmation</span>
</nav>

<section class="booking-section">

@if(!$booking)
    <!-- Booking Not Found -->
    <div class="booking-error-page">
        <div class="booking-icon">&#9888;</div>
        <h2>Booking Not Found</h2>
        <p>The requested booking could not be found. Please check the reference number and try again.</p>
        <a href="/" class="btn">&#8592; Back to Home</a>
    </div>

@else
    <div class="booking-flow">
        <div class="booking-steps">
            <div class="booking-step done">&#10003; Rate Verified</div>
            <div class="booking-step done">&#10003; Booked</div>
            <div class="booking-step active">3. Confirmation</div>
        </div>

        <div class="voucher">
            <div class="voucher-header">
                <div class="voucher-icon">&#10003;</div>
                <h2>Booking Confirmed!</h2>
                <p class="voucher-subtitle">Your reservation has been successfully completed.</p>
            </div>

            <div class="voucher-ref">
                <span class="voucher-ref-label">Booking Reference</span>
                <span class="voucher-ref-code">{{ $booking['reference'] ?? '' }}</span>
                @if(!empty($booking['client_reference']))
                    <span class="voucher-ref-label" style="margin-top:0.5rem;">Agency Reference</span>
                    <span class="voucher-ref-agency">{{ $booking['client_reference'] }}</span>
                @endif
            </div>

            <div class="voucher-details">
                <!-- Hotel Information -->
                <div class="voucher-section">
                    <h4>&#127960; Hotel Information</h4>
                    <p class="voucher-hotel-name">{{ $booking['hotel'] ?? '' }}</p>
                    @if(!empty($booking['hotel_category']))
                        <p>&#11088; {{ $booking['hotel_category'] }}</p>
                    @endif
                    @if(!empty($booking['hotel_address']))
                        <p>&#128205; {{ $booking['hotel_address'] }}</p>
                    @endif
                    @if(!empty($booking['hotel_destination']))
                        <p>&#127758; {{ $booking['hotel_destination'] }}</p>
                    @endif
                    @if(!empty($booking['hotel_phone']))
                        <p>&#128222; {{ $booking['hotel_phone'] }}</p>
                    @endif
                </div>

                <!-- Booking Grid -->
                <div class="voucher-grid">
                    <div class="voucher-item">
                        <span class="voucher-label">Check-in</span>
                        <span class="voucher-value">{!! !empty($booking['check_in']) ? \Carbon\Carbon::parse($booking['check_in'])->format('D, M d Y') : 'N/A' !!}</span>
                    </div>
                    <div class="voucher-item">
                        <span class="voucher-label">Check-out</span>
                        <span class="voucher-value">{!! !empty($booking['check_out']) ? \Carbon\Carbon::parse($booking['check_out'])->format('D, M d Y') : 'N/A' !!}</span>
                    </div>
                    <div class="voucher-item">
                        <span class="voucher-label">Status</span>
                        <span class="voucher-value voucher-status-{!! strtolower($booking['status'] ?? 'confirmed') !!}">{{ $booking['status'] ?? 'CONFIRMED' }}</span>
                    </div>
                    <div class="voucher-item">
                        <span class="voucher-label">Booking Date</span>
                        <span class="voucher-value">{{ $booking['created_at'] ?? date('Y-m-d') }}</span>
                    </div>
                </div>

                <!-- Guest Information -->
                <div class="voucher-section">
                    <h4>&#128100; Guest Information</h4>
                    <p><strong>Lead Guest:</strong> {{ $booking['holder'] ?? '' }}</p>
                </div>

                <!-- Room Details -->
                @if(!empty($booking['rooms']))
                    <div class="voucher-section">
                        <h4>&#128719; Room Details</h4>
                        @foreach($booking['rooms'] as $ri => $room)
                            <div class="voucher-room-item">
                                <p><strong>Room {!! $ri + 1 !!}:</strong> {{ $room['name'] ?? 'Standard Room' }} ({{ $room['code'] ?? '' }})</p>
                                @if(!empty($room['rates']))
                                    @foreach($room['rates'] as $rate)
                                        @if(!empty($rate['boardName']))
                                            <p>&#127860; Board: <strong>{{ $rate['boardName'] }}</strong> ({{ $rate['boardCode'] ?? '' }})</p>
                                        @endif
                                        @if(!empty($rate['rateComments']))
                                            <div class="voucher-rate-comments">
                                                <p><strong>&#128196; Important Information:</strong></p>
                                                <p>{!! nl2br(e($rate['rateComments'])) !!}</p>
                                            </div>
                                        @endif
                                        @if(!empty($rate['cancellationPolicies']))
                                            <div class="voucher-cancel-policy">
                                                <p><strong>Cancellation Policy:</strong></p>
                                                @foreach($rate['cancellationPolicies'] as $cp)
                                                    <p>From {{ \Carbon\Carbon::parse($cp['from'] ?? '')->format('M d, Y H:i') }}: {{ $cp['amount'] ?? '' }} {{ $booking['currency'] ?? '' }}</p>
                                                @endforeach
                                            </div>
                                        @endif
                                        @if(!empty($rate['paxes']))
                                            <p><strong>Guests:</strong>
                                            @foreach($rate['paxes'] as $pi => $pax)
                                                {{ ($pax['name'] ?? '') . ' ' . ($pax['surname'] ?? '') }} ({!! ($pax['type'] ?? 'AD') === 'AD' ? 'Adult' : 'Child' . (!empty($pax['age']) ? ', age ' . (int) $pax['age'] : '') !!}){!! $pi < count($rate['paxes']) - 1 ? ', ' : '' !!}
                                            @endforeach
                                            </p>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Payment Information -->
                <div class="voucher-section voucher-payment">
                    <h4>&#128179; Payment Information</h4>
                    <p>Payable through <strong>{{ $booking['supplier_name'] ?? 'Hotelbeds' }}</strong>, acting as agent for the service operating company, details of which can be provided upon request.@if(!empty($booking['supplier_vat'])) VAT: {{ $booking['supplier_vat'] }}@endif Reference: {{ $booking['reference'] ?? '' }}</p>
                </div>
            </div>

            <div class="voucher-actions">
                <button onclick="window.print()" class="btn btn-outline-dark">&#128424; Print Voucher</button>
                <a href="/" class="btn">&#8592; Back to Home</a>
            </div>
        </div>
    </div>

@endif

</section>
@endsection
