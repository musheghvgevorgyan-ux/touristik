<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Email service for the Touristik platform.
 *
 * Uses Laravel's Mail facade with the branded HTML template approach.
 * All templates use inline CSS for maximum email-client compatibility
 * (Gmail, Outlook, Apple Mail, Yahoo, etc.).
 *
 * Can be converted to proper Mailable classes in the future.
 */
class EmailService
{
    // ───────────────────────────────────────────────────────────
    //  Core: template wrapper + send
    // ───────────────────────────────────────────────────────────

    /**
     * Generate the branded HTML email wrapper.
     *
     * Layout:
     *  - Gradient header (#0f2027 -> #203a43 -> #2c5364) with logo text
     *  - Orange title bar (#f18f01)
     *  - White content area (600 px max-width)
     *  - Optional CTA button
     *  - Divider
     *  - Footer with 3 branch addresses, phone numbers, social links
     *
     * @param string $title   Title shown in the orange bar and <title> tag
     * @param string $body    Inner HTML content
     * @param string $ctaHtml Optional call-to-action HTML (e.g. a styled <a> button)
     */
    public static function template(string $title, string $body, string $ctaHtml = ''): string
    {
        $titleEsc = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

        $ctaBlock = '';
        if ($ctaHtml !== '') {
            $ctaBlock = '<tr>'
                . '<td align="center" style="padding:0 40px 25px 40px;">'
                . $ctaHtml
                . '</td>'
                . '</tr>';
        }

        return '<!DOCTYPE html>'
            . '<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">'
            . '<head>'
            .   '<meta charset="UTF-8">'
            .   '<meta name="viewport" content="width=device-width, initial-scale=1.0">'
            .   '<meta http-equiv="X-UA-Compatible" content="IE=edge">'
            .   '<meta name="x-apple-disable-message-reformatting">'
            .   '<meta name="color-scheme" content="light">'
            .   '<meta name="supported-color-schemes" content="light">'
            .   '<title>' . $titleEsc . '</title>'
            .   '<!--[if mso]>'
            .   '<noscript><xml><o:OfficeDocumentSettings>'
            .   '<o:AllowPNG/><o:PixelsPerInch>96</o:PixelsPerInch>'
            .   '</o:OfficeDocumentSettings></xml></noscript>'
            .   '<![endif]-->'
            .   '<style type="text/css">'
            .     'body,table,td,a{-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%}'
            .     'table,td{mso-table-lspace:0pt;mso-table-rspace:0pt}'
            .     'img{-ms-interpolation-mode:bicubic;border:0;outline:none;text-decoration:none}'
            .     'body{margin:0;padding:0;width:100%!important;-webkit-font-smoothing:antialiased}'
            .     '#outlook a{padding:0}'
            .     '.ExternalClass{width:100%}'
            .     '.ExternalClass,.ExternalClass p,.ExternalClass span,.ExternalClass font,.ExternalClass td,.ExternalClass div{line-height:100%}'
            .   '</style>'
            . '</head>'
            . '<body style="margin:0;padding:0;background-color:#f4f4f4;font-family:\'Segoe UI\',Tahoma,Geneva,Verdana,sans-serif;-webkit-font-smoothing:antialiased;">'

            // Outer wrapper table -- centers the email
            . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f4f4f4;">'
            . '<tr><td align="center" style="padding:20px 10px;">'

            // Inner container -- 600px max
            . '<table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" '
            .   'style="max-width:600px;width:100%;background-color:#ffffff;border-radius:8px;overflow:hidden;'
            .   'box-shadow:0 2px 8px rgba(0,0,0,0.1);">'

            // -- Header: gradient --
            . '<tr>'
            . '<td style="padding:30px 40px;text-align:center;'
            .   'background:#203a43;'
            .   'background:-webkit-linear-gradient(135deg,#0f2027 0%,#203a43 50%,#2c5364 100%);'
            .   'background:linear-gradient(135deg,#0f2027 0%,#203a43 50%,#2c5364 100%);">'
            .   '<h1 style="margin:0;color:#ffffff;font-size:24px;font-weight:700;letter-spacing:1px;'
            .     'font-family:\'Segoe UI\',Tahoma,Geneva,Verdana,sans-serif;">'
            .     '&#9992; Touristik Travel Club'
            .   '</h1>'
            . '</td>'
            . '</tr>'

            // -- Title bar: orange --
            . '<tr>'
            . '<td style="background-color:#f18f01;padding:12px 40px;text-align:center;">'
            .   '<h2 style="margin:0;color:#ffffff;font-size:18px;font-weight:600;'
            .     'font-family:\'Segoe UI\',Tahoma,Geneva,Verdana,sans-serif;">'
            .     $titleEsc
            .   '</h2>'
            . '</td>'
            . '</tr>'

            // -- Body content --
            . '<tr>'
            . '<td style="padding:30px 40px;color:#333333;font-size:15px;line-height:1.7;'
            .   'font-family:\'Segoe UI\',Tahoma,Geneva,Verdana,sans-serif;">'
            .   $body
            . '</td>'
            . '</tr>'

            // -- CTA button (optional) --
            . $ctaBlock

            // -- Divider --
            . '<tr>'
            . '<td style="padding:0 40px;">'
            .   '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">'
            .   '<tr><td style="border-top:1px solid #e0e0e0;font-size:1px;line-height:1px;">&nbsp;</td></tr>'
            .   '</table>'
            . '</td>'
            . '</tr>'

            // -- Footer --
            . '<tr>'
            . '<td style="padding:20px 40px 30px;color:#888888;font-size:13px;line-height:1.8;'
            .   'font-family:\'Segoe UI\',Tahoma,Geneva,Verdana,sans-serif;">'
            .   '<p style="margin:0 0 8px;font-weight:600;color:#203a43;">Touristik Travel Club</p>'
            .   '<p style="margin:0;">Phone: '
            .     '<a href="tel:+37433060609" style="color:#888888;text-decoration:none;">+374 33 060 609</a>'
            .     ' | '
            .     '<a href="tel:+37455060609" style="color:#888888;text-decoration:none;">+374 55 060 609</a>'
            .   '</p>'
            .   '<p style="margin:0;">Email: '
            .     '<a href="mailto:info@touristik.am" style="color:#f18f01;text-decoration:none;">info@touristik.am</a>'
            .   '</p>'
            .   '<p style="margin:0;">Website: '
            .     '<a href="https://touristik.am" style="color:#f18f01;text-decoration:none;">touristik.am</a>'
            .   '</p>'
            .   '<p style="margin:10px 0 0;font-size:12px;color:#aaaaaa;">'
            .     'Branches: Komitas 38 &bull; Mashtots 7/6 &bull; Arshakunyats 34 (Yerevan Mall, 2nd floor)'
            .   '</p>'
            . '</td>'
            . '</tr>'

            // Close inner container
            . '</table>'

            // Close outer wrapper
            . '</td></tr></table>'
            . '</body></html>';
    }

    /**
     * Send an HTML email via Laravel's Mail facade.
     *
     * @param string $to       Recipient email address
     * @param string $subject  Email subject line
     * @param string $htmlBody Full HTML document to send
     * @param string $replyTo  Optional reply-to override
     * @return bool
     */
    public static function send(string $to, string $subject, string $htmlBody, string $replyTo = ''): bool
    {
        $fromName  = config('mail.from.name', 'Touristik Travel');
        $fromEmail = config('mail.from.address', 'noreply@touristik.am');
        $replyAddr = $replyTo ?: config('mail.reply_to.address', 'info@touristik.am');

        try {
            Mail::html($htmlBody, function ($message) use ($to, $subject, $fromName, $fromEmail, $replyAddr) {
                $message->to($to)
                    ->subject($subject)
                    ->from($fromEmail, $fromName)
                    ->replyTo($replyAddr);
            });

            return true;
        } catch (\Throwable $e) {
            Log::error('EmailService::send failed', [
                'to'      => $to,
                'subject' => $subject,
                'error'   => $e->getMessage(),
            ]);
            return false;
        }
    }

    // ───────────────────────────────────────────────────────────
    //  HTML helpers
    // ───────────────────────────────────────────────────────────

    /**
     * Build a single info-table row (label + value) with optional alternate background.
     */
    public static function infoRow(string $label, string $value, bool $alt = false): string
    {
        $bg = $alt ? 'background-color:#f8f9fa;' : '';
        return '<tr style="' . $bg . '">'
            . '<td style="border-bottom:1px solid #e0e0e0;font-weight:600;width:140px;color:#203a43;padding:8px;">'
            .   htmlspecialchars($label, ENT_QUOTES, 'UTF-8')
            . '</td>'
            . '<td style="border-bottom:1px solid #e0e0e0;padding:8px;">'
            .   $value  // Caller is responsible for escaping; value may contain HTML (badges, links)
            . '</td>'
            . '</tr>';
    }

    /**
     * Wrap rows in a styled info table.
     */
    public static function infoTable(string $rows): string
    {
        return '<table width="100%" cellpadding="0" cellspacing="0" border="0" '
            . 'style="border:1px solid #e0e0e0;border-radius:6px;border-collapse:collapse;margin-bottom:20px;">'
            . $rows
            . '</table>';
    }

    /**
     * A styled status badge (green for confirmed, red for cancelled, grey for other).
     */
    public static function statusBadge(string $status): string
    {
        $upper = strtoupper(trim($status));
        $colors = [
            'CONFIRMED' => ['bg' => '#28a745', 'color' => '#ffffff'],
            'CANCELLED' => ['bg' => '#dc3545', 'color' => '#ffffff'],
            'PENDING'   => ['bg' => '#ffc107', 'color' => '#333333'],
            'FAILED'    => ['bg' => '#dc3545', 'color' => '#ffffff'],
        ];
        $c = $colors[$upper] ?? ['bg' => '#6c757d', 'color' => '#ffffff'];

        return '<span style="display:inline-block;background-color:' . $c['bg'] . ';color:' . $c['color']
            . ';padding:3px 10px;border-radius:4px;font-size:13px;font-weight:600;">'
            . htmlspecialchars($upper, ENT_QUOTES, 'UTF-8')
            . '</span>';
    }

    /**
     * A centered CTA button (orange, rounded).
     */
    public static function ctaButton(string $url, string $label): string
    {
        return '<a href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '" '
            . 'style="display:inline-block;background-color:#f18f01;color:#ffffff;'
            . 'padding:12px 30px;border-radius:6px;text-decoration:none;'
            . 'font-weight:600;font-size:15px;font-family:\'Segoe UI\',Tahoma,Geneva,Verdana,sans-serif;">'
            . htmlspecialchars($label, ENT_QUOTES, 'UTF-8')
            . '</a>';
    }

    // ───────────────────────────────────────────────────────────
    //  Convenience: Booking emails
    // ───────────────────────────────────────────────────────────

    /**
     * Send a booking confirmation email to the guest.
     *
     * @param string $guestEmail  Recipient
     * @param string $guestName   Full name of lead guest
     * @param array  $bookingData Keys: reference, hotel|hotel_name, check_in, check_out, status, currency, sell_price|total_price
     */
    public static function sendBookingConfirmation(string $guestEmail, string $guestName, array $bookingData): bool
    {
        $ref      = htmlspecialchars($bookingData['reference'] ?? '', ENT_QUOTES, 'UTF-8');
        $hotel    = htmlspecialchars($bookingData['hotel'] ?? $bookingData['hotel_name'] ?? '', ENT_QUOTES, 'UTF-8');
        $checkIn  = htmlspecialchars($bookingData['check_in'] ?? '', ENT_QUOTES, 'UTF-8');
        $checkOut = htmlspecialchars($bookingData['check_out'] ?? '', ENT_QUOTES, 'UTF-8');
        $status   = $bookingData['status'] ?? 'CONFIRMED';
        $currency = htmlspecialchars($bookingData['currency'] ?? 'EUR', ENT_QUOTES, 'UTF-8');
        $price    = number_format((float) ($bookingData['sell_price'] ?? $bookingData['total_price'] ?? 0), 2);

        $nameEsc = htmlspecialchars($guestName, ENT_QUOTES, 'UTF-8');

        $rows = self::infoRow('Reference', '<strong>' . $ref . '</strong>', true)
            . self::infoRow('Hotel', $hotel)
            . self::infoRow('Check-in', $checkIn, true)
            . self::infoRow('Check-out', $checkOut)
            . self::infoRow('Total', $currency . ' ' . $price, true)
            . self::infoRow('Status', self::statusBadge($status));

        $body = '<p style="margin:0 0 15px;">Dear <strong>' . $nameEsc . '</strong>,</p>'
            . '<p style="margin:0 0 20px;">Your booking has been confirmed. Here are your reservation details:</p>'
            . self::infoTable($rows)
            . '<p style="margin:0;">Thank you for booking with Touristik Travel Club!</p>';

        $voucherUrl = config('app.url', 'https://touristik.am') . '/booking/' . urlencode($bookingData['reference'] ?? '');
        $cta = self::ctaButton($voucherUrl, 'View Your Voucher');

        $html = self::template('Booking Confirmation', $body, $cta);
        return self::send($guestEmail, "Booking Confirmation - {$ref} | Touristik", $html);
    }

    /**
     * Send a booking cancellation email to the guest.
     *
     * @param string $guestEmail Recipient
     * @param string $guestName  Full name of lead guest
     * @param array  $bookingData Keys: reference, hotel|hotel_name, check_in, check_out
     */
    public static function sendBookingCancellation(string $guestEmail, string $guestName, array $bookingData): bool
    {
        $ref      = htmlspecialchars($bookingData['reference'] ?? '', ENT_QUOTES, 'UTF-8');
        $hotel    = htmlspecialchars($bookingData['hotel'] ?? $bookingData['hotel_name'] ?? '', ENT_QUOTES, 'UTF-8');
        $checkIn  = htmlspecialchars($bookingData['check_in'] ?? '', ENT_QUOTES, 'UTF-8');
        $checkOut = htmlspecialchars($bookingData['check_out'] ?? '', ENT_QUOTES, 'UTF-8');

        $nameEsc = htmlspecialchars($guestName, ENT_QUOTES, 'UTF-8');

        $rows = self::infoRow('Reference', $ref, true)
            . self::infoRow('Hotel', $hotel)
            . self::infoRow('Check-in', $checkIn, true)
            . self::infoRow('Check-out', $checkOut)
            . self::infoRow('Status', self::statusBadge('CANCELLED'), true);

        $body = '<p style="margin:0 0 15px;">Dear <strong>' . $nameEsc . '</strong>,</p>'
            . '<p style="margin:0 0 20px;">Your booking has been cancelled. Details below:</p>'
            . self::infoTable($rows)
            . '<p style="margin:0;">If you have any questions, please do not hesitate to contact us.</p>';

        $html = self::template('Booking Cancelled', $body);
        return self::send($guestEmail, "Booking Cancelled - {$ref} | Touristik", $html);
    }

    /**
     * Notify the admin about a new booking.
     *
     * @param array  $bookingData Keys: reference, hotel|hotel_name, check_in, check_out, status, currency, sell_price|total_price
     * @param string $guestEmail  Guest email
     * @param string $guestPhone  Guest phone
     */
    public static function sendAdminNewBooking(array $bookingData, string $guestEmail, string $guestPhone): bool
    {
        $adminEmail = self::adminEmail();
        if (!$adminEmail) {
            return false;
        }

        $ref      = htmlspecialchars($bookingData['reference'] ?? '', ENT_QUOTES, 'UTF-8');
        $hotel    = htmlspecialchars($bookingData['hotel'] ?? $bookingData['hotel_name'] ?? '', ENT_QUOTES, 'UTF-8');
        $guest    = htmlspecialchars(
            ($bookingData['guest_first_name'] ?? '') . ' ' . ($bookingData['guest_last_name'] ?? $bookingData['guest_name'] ?? ''),
            ENT_QUOTES, 'UTF-8'
        );
        $checkIn  = htmlspecialchars($bookingData['check_in'] ?? '', ENT_QUOTES, 'UTF-8');
        $checkOut = htmlspecialchars($bookingData['check_out'] ?? '', ENT_QUOTES, 'UTF-8');
        $status   = $bookingData['status'] ?? 'CONFIRMED';
        $currency = htmlspecialchars($bookingData['currency'] ?? 'EUR', ENT_QUOTES, 'UTF-8');
        $price    = number_format((float) ($bookingData['sell_price'] ?? $bookingData['total_price'] ?? 0), 2);
        $emailEsc = htmlspecialchars($guestEmail ?: 'Not provided', ENT_QUOTES, 'UTF-8');
        $phoneEsc = htmlspecialchars($guestPhone ?: 'Not provided', ENT_QUOTES, 'UTF-8');

        $rows = self::infoRow('Reference', '<strong>' . $ref . '</strong>', true)
            . self::infoRow('Hotel', $hotel)
            . self::infoRow('Guest', $guest, true)
            . self::infoRow('Email', '<a href="mailto:' . $emailEsc . '" style="color:#f18f01;text-decoration:none;">' . $emailEsc . '</a>')
            . self::infoRow('Phone', $phoneEsc, true)
            . self::infoRow('Check-in', $checkIn)
            . self::infoRow('Check-out', $checkOut, true)
            . self::infoRow('Total', $currency . ' ' . $price)
            . self::infoRow('Status', self::statusBadge($status), true);

        $body = '<p style="margin:0 0 15px;font-size:16px;font-weight:600;color:#203a43;">'
            . 'A new booking has been received.'
            . '</p>'
            . self::infoTable($rows);

        $html = self::template('New Booking Received', $body);
        return self::send($adminEmail, "New Booking: {$ref} - {$hotel}", $html);
    }

    // ───────────────────────────────────────────────────────────
    //  Convenience: Contact emails
    // ───────────────────────────────────────────────────────────

    /**
     * Auto-reply to a contact form submission.
     *
     * @param string $email   The customer's email
     * @param string $name    The customer's name
     * @param string $message The original message (shown back for reference)
     */
    public static function sendContactAutoReply(string $email, string $name, string $message = ''): bool
    {
        $nameEsc = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');

        $body = '<p style="margin:0 0 15px;">Dear <strong>' . $nameEsc . '</strong>,</p>'
            . '<p style="margin:0 0 15px;">Thank you for contacting Touristik Travel Club!</p>'
            . '<p style="margin:0 0 20px;">We have received your message and will get back to you within 24 hours.</p>';

        if ($message !== '') {
            $msgEsc = nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8'));
            $body .= '<div style="background-color:#f8f9fa;padding:15px 20px;border-radius:6px;'
                . 'border-left:4px solid #2c5364;margin-bottom:20px;">'
                . '<p style="margin:0;font-weight:600;color:#203a43;margin-bottom:8px;">Your message:</p>'
                . '<p style="margin:0;font-style:italic;color:#555555;">&ldquo;' . $msgEsc . '&rdquo;</p>'
                . '</div>';
        }

        $body .= '<p style="margin:0;">Best regards,<br><strong>Touristik Travel Club</strong></p>';

        $html = self::template('Message Received', $body);
        return self::send($email, 'We received your message - Touristik Travel Club', $html);
    }

    /**
     * Alert admin about a new contact form submission.
     *
     * @param string $name    Sender name
     * @param string $email   Sender email
     * @param string $message Message text
     */
    public static function sendContactAlert(string $name, string $email, string $message): bool
    {
        $adminEmail = self::adminEmail();
        if (!$adminEmail) {
            return false;
        }

        $nameEsc  = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        $emailEsc = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
        $msgEsc   = nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8'));

        $rows = self::infoRow('Name', $nameEsc, true)
            . self::infoRow('Email', '<a href="mailto:' . $emailEsc . '" style="color:#f18f01;text-decoration:none;">' . $emailEsc . '</a>');

        $body = '<p style="margin:0 0 15px;font-weight:600;color:#203a43;">New contact form submission:</p>'
            . self::infoTable($rows)
            . '<div style="background-color:#f8f9fa;padding:15px 20px;border-radius:6px;'
            . 'border-left:4px solid #f18f01;margin-bottom:15px;">'
            . '<p style="margin:0;font-weight:600;color:#203a43;margin-bottom:8px;">Message:</p>'
            . '<p style="margin:0;white-space:pre-wrap;">' . $msgEsc . '</p>'
            . '</div>';

        $html = self::template('New Contact Message', $body);
        return self::send($adminEmail, "New Contact from Touristik: {$nameEsc}", $html, $email);
    }

    // ───────────────────────────────────────────────────────────
    //  Convenience: Password reset
    // ───────────────────────────────────────────────────────────

    /**
     * Send a password reset email.
     *
     * @param string $email    Recipient
     * @param string $name     User's name
     * @param string $resetUrl Full URL to the password reset page (includes token)
     */
    public static function sendPasswordReset(string $email, string $name, string $resetUrl): bool
    {
        $nameEsc = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');

        $body = '<p style="margin:0 0 15px;">Dear <strong>' . $nameEsc . '</strong>,</p>'
            . '<p style="margin:0 0 15px;">We received a request to reset your password for your Touristik account.</p>'
            . '<p style="margin:0 0 20px;">Click the button below to set a new password. This link will expire in 1 hour.</p>';

        $cta = self::ctaButton($resetUrl, 'Reset Password');

        $body .= '<p style="margin:20px 0 0;font-size:13px;color:#888888;">'
            . 'If you did not request a password reset, please ignore this email. '
            . 'Your account remains secure.'
            . '</p>'
            . '<p style="margin:10px 0 0;font-size:12px;color:#aaaaaa;word-break:break-all;">'
            . 'If the button above does not work, copy and paste this link into your browser:<br>'
            . '<a href="' . htmlspecialchars($resetUrl, ENT_QUOTES, 'UTF-8') . '" style="color:#f18f01;text-decoration:none;">'
            . htmlspecialchars($resetUrl, ENT_QUOTES, 'UTF-8')
            . '</a>'
            . '</p>';

        $html = self::template('Password Reset', $body, $cta);
        return self::send($email, 'Password Reset - Touristik Travel Club', $html);
    }

    // ───────────────────────────────────────────────────────────
    //  Private helpers
    // ───────────────────────────────────────────────────────────

    /**
     * Resolve the admin email: prefer the settings table, fall back to config.
     */
    private static function adminEmail(): string
    {
        // Try the settings table first
        try {
            $email = SettingsService::get('contact_email', '');
            if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $email;
            }
        } catch (\Throwable $e) {
            // Settings table may not be available yet; fall through
        }

        return config('mail.admin_email', 'info@touristik.am');
    }
}
