<?php use App\Helpers\View; ?>

<nav class="breadcrumbs" aria-label="Breadcrumb">
    <a href="/" data-t="breadcrumb_home">Home</a>
    <span class="breadcrumb-sep">&#8250;</span>
    <a href="/account/bookings">My Bookings</a>
    <span class="breadcrumb-sep">&#8250;</span>
    <span class="breadcrumb-current">Payment</span>
</nav>

<section class="booking-section">
    <div class="booking-flow">
        <div class="booking-steps">
            <div class="booking-step done">&#10003; Booked</div>
            <div class="booking-step active">2. Payment</div>
            <div class="booking-step">3. Confirmation</div>
        </div>

        <div class="checkout-layout">
            <!-- ─── Left: Booking Summary ─────────────────────── -->
            <div class="checkout-summary">
                <div class="checkout-card">
                    <h3>&#128203; Booking Summary</h3>

                    <div class="checkout-ref">
                        <span class="checkout-ref-label">Reference</span>
                        <span class="checkout-ref-code"><?= View::e($booking['reference'] ?? '') ?></span>
                    </div>

                    <?php
                        $product = $booking['product_data'] ?? [];
                        $hotelName = $product['hotel'] ?? $product['hotel_name'] ?? '';
                        $checkIn   = $product['check_in'] ?? $booking['check_in'] ?? '';
                        $checkOut  = $product['check_out'] ?? $booking['check_out'] ?? '';
                        $currency  = $booking['currency'] ?? 'USD';
                        $totalPrice = (float)($booking['sell_price'] ?? $booking['net_price'] ?? 0);
                    ?>

                    <?php if ($hotelName): ?>
                        <div class="checkout-detail">
                            <h4><?= View::e($hotelName) ?></h4>
                            <?php if (!empty($product['destination'])): ?>
                                <p class="checkout-dest">&#128205; <?= View::e($product['destination']) ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="checkout-info-grid">
                        <?php if ($checkIn): ?>
                        <div class="checkout-info-item">
                            <span class="checkout-label">Check-in</span>
                            <span class="checkout-value"><?= View::date($checkIn, 'D, M d Y') ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ($checkOut): ?>
                        <div class="checkout-info-item">
                            <span class="checkout-label">Check-out</span>
                            <span class="checkout-value"><?= View::date($checkOut, 'D, M d Y') ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="checkout-info-item">
                            <span class="checkout-label">Guest</span>
                            <span class="checkout-value"><?= View::e(($booking['guest_first_name'] ?? '') . ' ' . ($booking['guest_last_name'] ?? '')) ?></span>
                        </div>
                        <div class="checkout-info-item">
                            <span class="checkout-label">Status</span>
                            <span class="checkout-value"><?= View::e(ucfirst($booking['status'] ?? 'confirmed')) ?></span>
                        </div>
                        <?php if (!empty($product['rooms'])): ?>
                        <div class="checkout-info-item">
                            <span class="checkout-label">Rooms</span>
                            <span class="checkout-value"><?= count($product['rooms']) ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($product['board']) || !empty($product['board_name'])): ?>
                        <div class="checkout-info-item">
                            <span class="checkout-label">Board</span>
                            <span class="checkout-value"><?= View::e($product['board_name'] ?? $product['board'] ?? '') ?></span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="checkout-total">
                        <span>Total to Pay</span>
                        <span class="checkout-total-price"><?= View::price($totalPrice, $currency) ?></span>
                    </div>

                    <?php if (!empty($booking['payments']) && count($booking['payments']) > 0): ?>
                        <div class="checkout-payments-history">
                            <h5>Previous Payment Attempts</h5>
                            <?php foreach ($booking['payments'] as $p): ?>
                                <div class="checkout-payment-row checkout-payment-<?= View::e($p['status'] ?? 'pending') ?>">
                                    <span><?= View::e(ucfirst($p['gateway'] ?? '')) ?> - <?= View::e(ucfirst($p['status'] ?? '')) ?></span>
                                    <span><?= View::price((float)($p['amount'] ?? 0), $p['currency'] ?? $currency) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ─── Right: Payment Options ────────────────────── -->
            <div class="checkout-options">

                <!-- Option 1: Pay at Office -->
                <div class="checkout-card checkout-option-card">
                    <div class="checkout-option-header">
                        <div class="checkout-option-icon">&#127970;</div>
                        <div>
                            <h4>Pay at Office</h4>
                            <p class="checkout-option-subtitle">Reserve Now, Pay Later</p>
                        </div>
                    </div>
                    <div class="checkout-option-body">
                        <p>Visit any of our branches in Yerevan to complete your payment in person.</p>
                        <div class="checkout-branches">
                            <div class="checkout-branch">
                                <span class="checkout-branch-icon">&#128205;</span>
                                <span>Komitas 38</span>
                            </div>
                            <div class="checkout-branch">
                                <span class="checkout-branch-icon">&#128205;</span>
                                <span>Mashtots 7/6</span>
                            </div>
                            <div class="checkout-branch">
                                <span class="checkout-branch-icon">&#128205;</span>
                                <span>Arshakunyats 34 (Yerevan Mall, 2nd floor)</span>
                            </div>
                        </div>
                        <p class="checkout-hours">Mon-Fri: 10:00-20:00 | Sat-Sun: 11:00-18:00</p>
                        <form method="POST" action="/payment/process" class="checkout-form">
                            <?= View::csrf() ?>
                            <input type="hidden" name="booking_reference" value="<?= View::e($booking['reference'] ?? '') ?>">
                            <input type="hidden" name="payment_method" value="office">
                            <button type="submit" class="btn checkout-btn checkout-btn-office">
                                &#128203; Reserve &mdash; Pay at Office
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Option 2: Pay Online (Sandbox) -->
                <div class="checkout-card checkout-option-card">
                    <div class="checkout-option-header">
                        <div class="checkout-option-icon">&#128179;</div>
                        <div>
                            <h4>Pay Online</h4>
                            <p class="checkout-option-subtitle">Instant Confirmation</p>
                        </div>
                    </div>
                    <div class="checkout-option-body">
                        <div class="checkout-sandbox-notice">
                            <span class="checkout-sandbox-badge">&#9888; Test Mode</span>
                            <p>This is a sandbox environment for testing. No real charges will be made. Use card <strong>4242 4242 4242 4242</strong> for a successful test payment.</p>
                        </div>
                        <form method="POST" action="/payment/process" class="checkout-form" id="sandboxPaymentForm">
                            <?= View::csrf() ?>
                            <input type="hidden" name="booking_reference" value="<?= View::e($booking['reference'] ?? '') ?>">
                            <input type="hidden" name="payment_method" value="sandbox">

                            <div class="checkout-card-form">
                                <div class="form-group">
                                    <label for="card_number">Card Number</label>
                                    <input type="text" id="card_number" name="card_number"
                                           placeholder="4242 4242 4242 4242"
                                           maxlength="19" autocomplete="cc-number" required
                                           pattern="[\d\s]{13,19}">
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="card_expiry">Expiry Date</label>
                                        <input type="text" id="card_expiry" name="card_expiry"
                                               placeholder="MM/YY" maxlength="5"
                                               autocomplete="cc-exp" required
                                               pattern="\d{2}/\d{2}">
                                    </div>
                                    <div class="form-group">
                                        <label for="card_cvv">CVV</label>
                                        <input type="text" id="card_cvv" name="card_cvv"
                                               placeholder="123" maxlength="4"
                                               autocomplete="cc-csc" required
                                               pattern="\d{3,4}">
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn checkout-btn checkout-btn-pay">
                                &#128274; Pay <?= View::price($totalPrice, $currency) ?>
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<style>
/* ─── Checkout Layout ──────────────────────────────────────── */
.checkout-layout {
    display: grid;
    grid-template-columns: 1fr 1.2fr;
    gap: 2rem;
    max-width: 1100px;
    margin: 0 auto;
    padding: 1.5rem 0;
}

.checkout-card {
    background: var(--card-bg, #fff);
    border-radius: 12px;
    padding: 1.8rem;
    box-shadow: 0 2px 16px rgba(0,0,0,0.07);
    border: 1px solid var(--border-color, #e8e8e8);
}

.checkout-card h3 {
    margin: 0 0 1.2rem 0;
    font-size: 1.2rem;
    color: var(--text-color, #222);
}

/* ─── Reference Badge ──────────────────────────────────────── */
.checkout-ref {
    display: flex;
    align-items: center;
    gap: 0.8rem;
    margin-bottom: 1.2rem;
    padding: 0.6rem 1rem;
    background: var(--bg-subtle, #f8f9fa);
    border-radius: 8px;
}

.checkout-ref-label {
    font-size: 0.85rem;
    color: var(--text-muted, #666);
    font-weight: 500;
}

.checkout-ref-code {
    font-weight: 700;
    font-size: 1.05rem;
    color: #FF6B35;
    letter-spacing: 0.03em;
    font-family: 'Courier New', monospace;
}

/* ─── Detail Section ───────────────────────────────────────── */
.checkout-detail h4 {
    margin: 0 0 0.3rem 0;
    font-size: 1.1rem;
    color: var(--text-color, #222);
}

.checkout-dest {
    color: var(--text-muted, #666);
    font-size: 0.9rem;
    margin: 0 0 1rem 0;
}

/* ─── Info Grid ────────────────────────────────────────────── */
.checkout-info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.8rem;
    margin: 1rem 0;
}

.checkout-info-item {
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
}

.checkout-label {
    font-size: 0.78rem;
    color: var(--text-muted, #999);
    text-transform: uppercase;
    letter-spacing: 0.04em;
    font-weight: 600;
}

.checkout-value {
    font-size: 0.95rem;
    color: var(--text-color, #333);
    font-weight: 500;
}

/* ─── Total ────────────────────────────────────────────────── */
.checkout-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 0;
    margin-top: 1rem;
    border-top: 2px solid var(--border-color, #eee);
    font-size: 1.1rem;
    font-weight: 700;
}

.checkout-total-price {
    color: #FF6B35;
    font-size: 1.35rem;
}

/* ─── Payment History ──────────────────────────────────────── */
.checkout-payments-history {
    margin-top: 1rem;
    padding-top: 0.8rem;
    border-top: 1px dashed var(--border-color, #ddd);
}

.checkout-payments-history h5 {
    margin: 0 0 0.5rem 0;
    font-size: 0.85rem;
    color: var(--text-muted, #666);
}

.checkout-payment-row {
    display: flex;
    justify-content: space-between;
    padding: 0.4rem 0.6rem;
    font-size: 0.85rem;
    border-radius: 6px;
    margin-bottom: 0.3rem;
}

.checkout-payment-completed {
    background: #d4edda;
    color: #155724;
}

.checkout-payment-failed {
    background: #f8d7da;
    color: #721c24;
}

.checkout-payment-pending {
    background: #fff3cd;
    color: #856404;
}

/* ─── Payment Options ──────────────────────────────────────── */
.checkout-options {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.checkout-option-card {
    transition: box-shadow 0.2s ease;
}

.checkout-option-card:hover {
    box-shadow: 0 4px 24px rgba(0,0,0,0.1);
}

.checkout-option-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}

.checkout-option-icon {
    font-size: 2rem;
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--bg-subtle, #f8f9fa);
    border-radius: 12px;
    flex-shrink: 0;
}

.checkout-option-header h4 {
    margin: 0;
    font-size: 1.1rem;
    color: var(--text-color, #222);
}

.checkout-option-subtitle {
    margin: 0.15rem 0 0 0;
    font-size: 0.85rem;
    color: var(--text-muted, #888);
}

.checkout-option-body p {
    color: var(--text-muted, #555);
    font-size: 0.9rem;
    line-height: 1.5;
    margin: 0 0 0.8rem 0;
}

/* ─── Branches List ────────────────────────────────────────── */
.checkout-branches {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin: 0.8rem 0;
    padding: 0.8rem;
    background: var(--bg-subtle, #f8f9fa);
    border-radius: 8px;
}

.checkout-branch {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.88rem;
    color: var(--text-color, #333);
}

.checkout-branch-icon {
    font-size: 1rem;
    flex-shrink: 0;
}

.checkout-hours {
    font-size: 0.82rem !important;
    color: var(--text-muted, #999) !important;
    text-align: center;
}

/* ─── Sandbox Notice ───────────────────────────────────────── */
.checkout-sandbox-notice {
    background: #fff3cd;
    border: 1px solid #ffeeba;
    border-radius: 8px;
    padding: 0.8rem 1rem;
    margin-bottom: 1rem;
}

.checkout-sandbox-badge {
    display: inline-block;
    background: #856404;
    color: #fff;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 0.2rem 0.6rem;
    border-radius: 4px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.4rem;
}

.checkout-sandbox-notice p {
    margin: 0.4rem 0 0 0 !important;
    font-size: 0.83rem !important;
    color: #856404 !important;
}

/* ─── Card Form ────────────────────────────────────────────── */
.checkout-card-form {
    margin-bottom: 1rem;
}

.checkout-card-form .form-group {
    margin-bottom: 0.8rem;
}

.checkout-card-form label {
    display: block;
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--text-color, #333);
    margin-bottom: 0.3rem;
}

.checkout-card-form input {
    width: 100%;
    padding: 0.7rem 0.9rem;
    border: 1.5px solid var(--border-color, #ddd);
    border-radius: 8px;
    font-size: 0.95rem;
    background: var(--input-bg, #fff);
    color: var(--text-color, #222);
    transition: border-color 0.2s ease;
    box-sizing: border-box;
}

.checkout-card-form input:focus {
    border-color: #FF6B35;
    outline: none;
    box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.12);
}

.checkout-card-form .form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.8rem;
}

/* ─── Buttons ──────────────────────────────────────────────── */
.checkout-btn {
    width: 100%;
    padding: 0.9rem 1.5rem;
    font-size: 1rem;
    font-weight: 700;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.2s ease;
    text-align: center;
    margin-top: 0.5rem;
}

.checkout-btn-office {
    background: linear-gradient(135deg, #2c3e50, #34495e);
    color: #fff;
}

.checkout-btn-office:hover {
    background: linear-gradient(135deg, #34495e, #2c3e50);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(44, 62, 80, 0.3);
}

.checkout-btn-pay {
    background: linear-gradient(135deg, #FF6B35, #e55a2b);
    color: #fff;
}

.checkout-btn-pay:hover {
    background: linear-gradient(135deg, #e55a2b, #FF6B35);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(255, 107, 53, 0.35);
}

.checkout-btn:active {
    transform: translateY(0);
}

/* ─── Responsive ───────────────────────────────────────────── */
@media (max-width: 768px) {
    .checkout-layout {
        grid-template-columns: 1fr;
        padding: 1rem 0;
    }

    .checkout-info-grid {
        grid-template-columns: 1fr;
    }

    .checkout-card-form .form-row {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Card number formatting: add spaces every 4 digits
    var cardInput = document.getElementById('card_number');
    if (cardInput) {
        cardInput.addEventListener('input', function(e) {
            var value = e.target.value.replace(/\s+/g, '').replace(/\D/g, '');
            var formatted = value.match(/.{1,4}/g);
            e.target.value = formatted ? formatted.join(' ') : '';
        });
    }

    // Expiry date formatting: auto-insert slash
    var expiryInput = document.getElementById('card_expiry');
    if (expiryInput) {
        expiryInput.addEventListener('input', function(e) {
            var value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            e.target.value = value;
        });
    }

    // CVV: digits only
    var cvvInput = document.getElementById('card_cvv');
    if (cvvInput) {
        cvvInput.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/\D/g, '').substring(0, 4);
        });
    }

    // Prevent double-submit on payment forms
    var forms = document.querySelectorAll('.checkout-form');
    forms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            var btn = form.querySelector('.checkout-btn');
            if (btn && btn.disabled) {
                e.preventDefault();
                return;
            }
            if (btn) {
                btn.disabled = true;
                btn.style.opacity = '0.7';
                btn.innerHTML = '&#9203; Processing...';
            }
        });
    });
});
</script>
