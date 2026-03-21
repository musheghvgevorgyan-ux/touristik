    <footer>
        <p data-t="footer_text"><?= htmlspecialchars(getSetting($pdo, 'footer_text', '© 2026 Touristik. All rights reserved.')) ?></p>
    </footer>

    <!-- Booking Modal -->
    <div id="bookingModal" class="booking-modal-overlay">
        <div class="booking-modal">
            <button class="booking-modal-close">&times;</button>
            <div class="booking-modal-icon">&#9993;</div>
            <h3 data-t="modal_title">Thank You!</h3>
            <p data-t="modal_text">We have received your request. Our team will contact you shortly to finalize your booking.</p>
            <p class="booking-modal-sub" data-t="modal_sub">Please make sure your contact details are up to date.</p>
            <button class="btn booking-modal-ok" data-t="modal_ok">OK</button>
        </div>
    </div>

    <script src="js/script.js"></script>
</body>
</html>
