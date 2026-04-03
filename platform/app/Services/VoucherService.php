<?php

namespace App\Services;

/**
 * Voucher service for the Touristik platform.
 *
 * Generates a standalone, printable HTML voucher for a booking.
 * Not a PDF — pure HTML with @media print CSS for clean printing.
 *
 * Voucher contents (per Hotelbeds certification requirements 4.5):
 *  - Booking reference
 *  - Hotel info: name, category, address, destination, phone
 *  - Stay details: check-in, check-out, room type, board
 *  - Guest info: holder name, pax list
 *  - Cancellation policy
 *  - Rate comments / important information
 *  - Payment information with supplier details
 *  - Booking reference as text reference (QR placeholder)
 */
class VoucherService
{
    // ───────────────────────────────────────────────────────────
    //  Public API
    // ───────────────────────────────────────────────────────────

    /**
     * Generate a standalone HTML voucher document for a booking array.
     *
     * @param array $booking A booking record from BookingService::getByReference()
     *                       or the legacy raw booking array from the Hotelbeds flow.
     * @return string Complete HTML document
     */
    public static function generateHtml(array $booking): string
    {
        // Normalize keys — support both the new platform schema and the legacy one
        $reference    = self::e($booking['reference'] ?? '');
        $supplierRef  = self::e($booking['supplier_ref'] ?? $booking['client_reference'] ?? '');
        $status       = strtoupper($booking['status'] ?? 'CONFIRMED');
        $createdAt    = self::e($booking['created_at'] ?? date('Y-m-d'));

        // Hotel info
        $hotelName    = self::e($booking['hotel'] ?? $booking['hotel_name'] ?? '');
        $hotelCategory = self::e($booking['hotel_category'] ?? '');
        $hotelAddress = self::e($booking['hotel_address'] ?? '');
        $hotelDest    = self::e($booking['hotel_destination'] ?? '');
        $hotelPhone   = self::e($booking['hotel_phone'] ?? '');

        // Stay details
        $checkIn  = $booking['check_in'] ?? '';
        $checkOut = $booking['check_out'] ?? '';
        $checkInFmt  = $checkIn  ? date('D, M d Y', strtotime($checkIn))  : 'N/A';
        $checkOutFmt = $checkOut ? date('D, M d Y', strtotime($checkOut)) : 'N/A';
        $nights = ($checkIn && $checkOut) ? max(1, (int)((strtotime($checkOut) - strtotime($checkIn)) / 86400)) : '';

        // Guest info
        $holder = self::e(
            $booking['holder']
            ?? trim(($booking['guest_first_name'] ?? '') . ' ' . ($booking['guest_last_name'] ?? ''))
            ?: ($booking['guest_name'] ?? '')
        );

        // Currency and price
        $currency = self::e($booking['currency'] ?? 'EUR');
        $price    = number_format((float)($booking['sell_price'] ?? $booking['total_price'] ?? $booking['total_net'] ?? 0), 2);

        // Supplier info
        $supplierName = self::e($booking['supplier_name'] ?? 'Hotelbeds');
        $supplierVat  = self::e($booking['supplier_vat'] ?? '');

        // Product data (may contain rooms, rates, etc.)
        $productData = $booking['product_data'] ?? [];
        if (is_string($productData)) {
            $productData = json_decode($productData, true) ?: [];
        }

        // Rooms from product data or directly from booking
        $rooms = $booking['rooms'] ?? $productData['rooms'] ?? [];
        if (is_numeric($rooms)) {
            $rooms = []; // Legacy schema stores room count as an int
        }

        // ── Start building HTML ──
        $html = '<!DOCTYPE html>'
            . '<html lang="en">'
            . '<head>'
            . '<meta charset="UTF-8">'
            . '<meta name="viewport" content="width=device-width, initial-scale=1.0">'
            . '<title>Voucher - ' . $reference . ' | Touristik Travel</title>'
            . '<style>'
            . self::css()
            . '</style>'
            . '</head>'
            . '<body>'
            . '<div class="voucher">';

        // ── Header ──
        $html .= '<div class="voucher-header">'
            . '<div class="voucher-logo">&#9992; Touristik Travel Club</div>'
            . '<div class="voucher-doc-title">Hotel Voucher</div>'
            . '</div>';

        // ── Reference block ──
        $statusClass = 'status-' . strtolower($status);
        $html .= '<div class="ref-block">'
            . '<div class="ref-main">'
            .   '<span class="ref-label">Booking Reference</span>'
            .   '<span class="ref-code">' . $reference . '</span>'
            . '</div>';
        if ($supplierRef) {
            $html .= '<div class="ref-secondary">'
                . '<span class="ref-label">Supplier Reference</span>'
                . '<span class="ref-secondary-code">' . $supplierRef . '</span>'
                . '</div>';
        }
        $html .= '<div class="ref-status">'
            . '<span class="status-badge ' . $statusClass . '">' . self::e($status) . '</span>'
            . '</div>'
            . '</div>';

        // ── Hotel Information ──
        $html .= '<div class="section">'
            . '<h3 class="section-title">&#127960; Hotel Information</h3>'
            . '<div class="hotel-name">' . $hotelName . '</div>';
        if ($hotelCategory) {
            $html .= '<p class="hotel-detail">&#11088; ' . $hotelCategory . '</p>';
        }
        if ($hotelAddress) {
            $html .= '<p class="hotel-detail">&#128205; ' . $hotelAddress . '</p>';
        }
        if ($hotelDest) {
            $html .= '<p class="hotel-detail">&#127758; ' . $hotelDest . '</p>';
        }
        if ($hotelPhone) {
            $html .= '<p class="hotel-detail">&#128222; ' . $hotelPhone . '</p>';
        }
        $html .= '</div>';

        // ── Stay Details ──
        $html .= '<div class="section">'
            . '<h3 class="section-title">&#128197; Stay Details</h3>'
            . '<table class="detail-table">'
            . '<tr>'
            .   '<td class="dt-label">Check-in</td>'
            .   '<td class="dt-value">' . self::e($checkInFmt) . '</td>'
            .   '<td class="dt-label">Check-out</td>'
            .   '<td class="dt-value">' . self::e($checkOutFmt) . '</td>'
            . '</tr>'
            . '<tr>'
            .   '<td class="dt-label">Nights</td>'
            .   '<td class="dt-value">' . ($nights ?: 'N/A') . '</td>'
            .   '<td class="dt-label">Booking Date</td>'
            .   '<td class="dt-value">' . $createdAt . '</td>'
            . '</tr>'
            . '</table>'
            . '</div>';

        // ── Guest Information ──
        $html .= '<div class="section">'
            . '<h3 class="section-title">&#128100; Guest Information</h3>'
            . '<p><strong>Lead Guest:</strong> ' . $holder . '</p>';

        // Pax list from rooms
        if (!empty($rooms) && is_array($rooms)) {
            foreach ($rooms as $ri => $room) {
                if (!empty($room['rates'])) {
                    foreach ($room['rates'] as $rate) {
                        if (!empty($rate['paxes'])) {
                            $html .= '<p><strong>Room ' . ($ri + 1) . ' Guests:</strong> ';
                            $paxNames = [];
                            foreach ($rate['paxes'] as $pax) {
                                $paxLabel = self::e(($pax['name'] ?? '') . ' ' . ($pax['surname'] ?? ''));
                                $paxType  = ($pax['type'] ?? 'AD') === 'AD' ? 'Adult' : 'Child';
                                if (!empty($pax['age'])) {
                                    $paxType .= ', age ' . (int)$pax['age'];
                                }
                                $paxNames[] = $paxLabel . ' (' . $paxType . ')';
                            }
                            $html .= implode('; ', $paxNames) . '</p>';
                        }
                    }
                }
            }
        }
        $html .= '</div>';

        // ── Room Details ──
        if (!empty($rooms) && is_array($rooms)) {
            $html .= '<div class="section">'
                . '<h3 class="section-title">&#128719; Room Details</h3>';

            foreach ($rooms as $ri => $room) {
                $roomName = self::e($room['name'] ?? 'Standard Room');
                $roomCode = self::e($room['code'] ?? '');

                $html .= '<div class="room-block">'
                    . '<p class="room-name">Room ' . ($ri + 1) . ': ' . $roomName;
                if ($roomCode) {
                    $html .= ' <span class="room-code">(' . $roomCode . ')</span>';
                }
                $html .= '</p>';

                if (!empty($room['rates'])) {
                    foreach ($room['rates'] as $rate) {
                        if (!empty($rate['boardName'])) {
                            $html .= '<p>&#127860; Board: <strong>' . self::e($rate['boardName']) . '</strong>';
                            if (!empty($rate['boardCode'])) {
                                $html .= ' (' . self::e($rate['boardCode']) . ')';
                            }
                            $html .= '</p>';
                        }

                        // Rate comments / important info
                        if (!empty($rate['rateComments'])) {
                            $html .= '<div class="info-box">'
                                . '<p class="info-box-title">&#128196; Important Information</p>'
                                . '<p>' . nl2br(self::e($rate['rateComments'])) . '</p>'
                                . '</div>';
                        }

                        // Cancellation policy
                        if (!empty($rate['cancellationPolicies'])) {
                            $html .= '<div class="cancel-policy">'
                                . '<p class="info-box-title">Cancellation Policy</p>';
                            foreach ($rate['cancellationPolicies'] as $cp) {
                                $fromDate = !empty($cp['from']) ? date('M d, Y H:i', strtotime($cp['from'])) : 'N/A';
                                $amount   = self::e($cp['amount'] ?? '');
                                $html .= '<p>From ' . self::e($fromDate) . ': ' . $amount . ' ' . $currency . '</p>';
                            }
                            $html .= '</div>';
                        }
                    }
                }

                $html .= '</div>'; // .room-block
            }

            $html .= '</div>'; // .section
        }

        // ── Cancellation Policy (top-level, if not in rooms) ──
        $topPolicies = $booking['cancellation_policies'] ?? $productData['cancellation_policies'] ?? [];
        if (!empty($topPolicies) && empty($rooms)) {
            $html .= '<div class="section">'
                . '<h3 class="section-title">Cancellation Policy</h3>';
            foreach ($topPolicies as $cp) {
                $fromDate = !empty($cp['from']) ? date('M d, Y H:i', strtotime($cp['from'])) : 'N/A';
                $amount   = self::e($cp['amount'] ?? '');
                $html .= '<p>From ' . self::e($fromDate) . ': ' . $amount . ' ' . $currency . '</p>';
            }
            $html .= '</div>';
        }

        // ── Rate Comments (top-level) ──
        $topComments = $booking['rate_comments'] ?? $productData['rateComments'] ?? '';
        if ($topComments && empty($rooms)) {
            $html .= '<div class="section">'
                . '<h3 class="section-title">&#128196; Important Information</h3>'
                . '<div class="info-box"><p>' . nl2br(self::e($topComments)) . '</p></div>'
                . '</div>';
        }

        // ── Payment Information (required by Hotelbeds certification 4.5) ──
        $html .= '<div class="section payment-section">'
            . '<h3 class="section-title">&#128179; Payment Information</h3>'
            . '<table class="detail-table">'
            . '<tr>'
            .   '<td class="dt-label">Total</td>'
            .   '<td class="dt-value"><strong>' . $currency . ' ' . $price . '</strong></td>'
            .   '<td class="dt-label">Reference</td>'
            .   '<td class="dt-value">' . $reference . '</td>'
            . '</tr>'
            . '</table>'
            . '<p class="supplier-note">'
            .   'Payable through <strong>' . $supplierName . '</strong>, acting as agent for the service '
            .   'operating company, details of which can be provided upon request.';
        if ($supplierVat) {
            $html .= ' VAT: ' . $supplierVat . '.';
        }
        $html .= ' Reference: ' . $reference
            . '</p>'
            . '</div>';

        // ── Booking Reference (QR placeholder — plain text for now) ──
        $html .= '<div class="qr-section">'
            . '<div class="qr-placeholder">'
            .   '<div class="qr-ref-label">Booking Reference</div>'
            .   '<div class="qr-ref-code">' . $reference . '</div>'
            . '</div>'
            . '</div>';

        // ── Footer ──
        $html .= '<div class="voucher-footer">'
            . '<div class="footer-brand">Touristik Travel Club</div>'
            . '<div class="footer-branches">'
            .   '<span>Komitas 38</span>'
            .   '<span class="sep">&bull;</span>'
            .   '<span>Mashtots 7/6</span>'
            .   '<span class="sep">&bull;</span>'
            .   '<span>Arshakunyats 34 (Yerevan Mall, 2nd floor)</span>'
            . '</div>'
            . '<div class="footer-contact">'
            .   'Phone: +374 33 060 609 | +374 55 060 609 &nbsp;&middot;&nbsp; '
            .   'Email: info@touristik.am &nbsp;&middot;&nbsp; '
            .   '<a href="https://touristik.am">touristik.am</a>'
            . '</div>'
            . '</div>';

        // ── Print button (hidden in print) ──
        $html .= '<div class="actions no-print">'
            . '<button onclick="window.print()" class="btn-print">&#128424; Print Voucher</button>'
            . '</div>';

        // Close
        $html .= '</div>' // .voucher
            . '</body></html>';

        return $html;
    }

    /**
     * Get voucher HTML for a booking by its internal reference.
     *
     * @param string $reference Internal booking reference (TK-YYMMDD-XXX)
     * @return string|null HTML string, or null if booking not found
     */
    public static function forBooking(string $reference): ?string
    {
        $booking = BookingService::getByReference($reference);
        if (!$booking) {
            return null;
        }
        return self::generateHtml($booking);
    }

    // ───────────────────────────────────────────────────────────
    //  Private helpers
    // ───────────────────────────────────────────────────────────

    /**
     * HTML-escape a string.
     */
    private static function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * All CSS for the voucher — screen and print.
     */
    private static function css(): string
    {
        return <<<'CSS'
/* ── Reset & base ── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f4f4f4;
    color: #333;
    line-height: 1.6;
    -webkit-font-smoothing: antialiased;
}

/* ── Voucher container ── */
.voucher {
    max-width: 800px;
    margin: 20px auto;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.1);
    overflow: hidden;
}

/* ── Header ── */
.voucher-header {
    background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
    padding: 28px 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.voucher-logo {
    color: #fff;
    font-size: 22px;
    font-weight: 700;
    letter-spacing: 1px;
}
.voucher-doc-title {
    color: rgba(255,255,255,0.8);
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 2px;
    font-weight: 600;
}

/* ── Reference block ── */
.ref-block {
    background: #f18f01;
    padding: 20px 40px;
    display: flex;
    align-items: center;
    gap: 30px;
    flex-wrap: wrap;
}
.ref-main, .ref-secondary { display: flex; flex-direction: column; }
.ref-label {
    color: rgba(255,255,255,0.85);
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: 600;
}
.ref-code {
    color: #fff;
    font-size: 26px;
    font-weight: 700;
    letter-spacing: 2px;
}
.ref-secondary-code {
    color: #fff;
    font-size: 16px;
    font-weight: 600;
}
.ref-status { margin-left: auto; }
.status-badge {
    display: inline-block;
    padding: 5px 16px;
    border-radius: 4px;
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.status-confirmed { background: #28a745; color: #fff; }
.status-cancelled { background: #dc3545; color: #fff; }
.status-pending   { background: #ffc107; color: #333; }
.status-failed    { background: #dc3545; color: #fff; }

/* ── Sections ── */
.section {
    padding: 20px 40px;
    border-bottom: 1px solid #eee;
}
.section:last-of-type { border-bottom: none; }
.section-title {
    font-size: 16px;
    font-weight: 600;
    color: #203a43;
    margin-bottom: 12px;
    padding-bottom: 6px;
    border-bottom: 2px solid #f18f01;
    display: inline-block;
}

/* ── Hotel ── */
.hotel-name {
    font-size: 20px;
    font-weight: 700;
    color: #0f2027;
    margin-bottom: 6px;
}
.hotel-detail {
    font-size: 14px;
    color: #555;
    margin-bottom: 3px;
}

/* ── Detail table (2x2 grid) ── */
.detail-table {
    width: 100%;
    border-collapse: collapse;
}
.detail-table td {
    padding: 8px 12px;
    font-size: 14px;
    border-bottom: 1px solid #eee;
}
.dt-label {
    font-weight: 600;
    color: #203a43;
    width: 120px;
}
.dt-value { color: #333; }

/* ── Room details ── */
.room-block {
    background: #fafafa;
    border: 1px solid #eee;
    border-radius: 6px;
    padding: 14px 18px;
    margin-bottom: 10px;
}
.room-name { font-weight: 600; color: #203a43; margin-bottom: 6px; font-size: 15px; }
.room-code { color: #888; font-weight: 400; font-size: 13px; }

/* ── Info / rate comments box ── */
.info-box {
    background: #f8f9fa;
    border-left: 4px solid #f18f01;
    border-radius: 4px;
    padding: 12px 16px;
    margin: 8px 0;
    font-size: 13px;
    color: #555;
}
.info-box-title { font-weight: 600; color: #203a43; margin-bottom: 4px; }

/* ── Cancellation policy ── */
.cancel-policy {
    background: #fff5f5;
    border-left: 4px solid #dc3545;
    border-radius: 4px;
    padding: 12px 16px;
    margin: 8px 0;
    font-size: 13px;
}

/* ── Payment section ── */
.payment-section { background: #fafafa; }
.supplier-note {
    font-size: 12px;
    color: #888;
    margin-top: 10px;
    line-height: 1.6;
}

/* ── QR / Reference section ── */
.qr-section {
    padding: 20px 40px;
    text-align: center;
    border-bottom: 1px solid #eee;
}
.qr-placeholder {
    display: inline-block;
    border: 2px dashed #ccc;
    border-radius: 8px;
    padding: 16px 30px;
    text-align: center;
}
.qr-ref-label {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #888;
    margin-bottom: 4px;
}
.qr-ref-code {
    font-size: 22px;
    font-weight: 700;
    color: #203a43;
    letter-spacing: 2px;
    font-family: 'Courier New', Courier, monospace;
}

/* ── Footer ── */
.voucher-footer {
    background: #f8f9fa;
    padding: 20px 40px;
    text-align: center;
    font-size: 13px;
    color: #888;
}
.footer-brand {
    font-weight: 600;
    color: #203a43;
    font-size: 14px;
    margin-bottom: 4px;
}
.footer-branches { margin-bottom: 4px; }
.footer-branches .sep { margin: 0 6px; }
.footer-contact a { color: #f18f01; text-decoration: none; }

/* ── Actions (print button) ── */
.actions {
    padding: 20px 40px;
    text-align: center;
}
.btn-print {
    display: inline-block;
    background: #f18f01;
    color: #fff;
    border: none;
    padding: 12px 30px;
    border-radius: 6px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    font-family: inherit;
}
.btn-print:hover { background: #d97e01; }

/* ── Print styles ── */
@media print {
    body { background: #fff; }
    .voucher {
        box-shadow: none;
        margin: 0;
        max-width: 100%;
        border-radius: 0;
    }
    .no-print { display: none !important; }
    .voucher-header {
        background: #203a43 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    .ref-block {
        background: #f18f01 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    .status-badge, .status-confirmed, .status-cancelled, .status-pending {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    .section { page-break-inside: avoid; }
    .voucher-footer { page-break-inside: avoid; }
}

/* ── Responsive ── */
@media screen and (max-width: 640px) {
    .voucher-header { flex-direction: column; text-align: center; gap: 8px; padding: 20px; }
    .ref-block { flex-direction: column; text-align: center; padding: 16px 20px; gap: 12px; }
    .ref-status { margin-left: 0; }
    .section { padding: 16px 20px; }
    .detail-table td { display: block; width: 100%; }
    .dt-label { padding-bottom: 0; }
    .dt-value { padding-top: 0; padding-bottom: 12px; }
    .qr-section { padding: 16px 20px; }
    .actions { padding: 16px 20px; }
    .voucher-footer { padding: 16px 20px; }
}
CSS;
    }
}
