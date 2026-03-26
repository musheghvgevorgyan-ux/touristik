    <footer>
        <div class="footer-grid">
            <div class="footer-col">
                <h4 data-t="footer_branches_title">Our Branches</h4>
                <ul class="footer-list">
                    <li><i class="fa-solid fa-location-dot"></i> <span data-t="branch_1">Komitas 38</span></li>
                    <li><i class="fa-solid fa-location-dot"></i> <span data-t="branch_2">Mashtots 7/6</span></li>
                    <li><i class="fa-solid fa-location-dot"></i> <span data-t="branch_3">Arshakunyats 34 (Yerevan Mall, 2nd floor)</span></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4 data-t="footer_hours_title">Working Hours</h4>
                <ul class="footer-list">
                    <li><i class="fa-regular fa-clock"></i> <span data-t="hours_weekday">Mon – Fri: 10:00 – 20:00</span></li>
                    <li><i class="fa-regular fa-clock"></i> <span data-t="hours_weekend">Sat – Sun: 11:00 – 18:00</span></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4 data-t="footer_contact_title">Contact Us</h4>
                <ul class="footer-list">
                    <li><i class="fa-solid fa-phone"></i> <a href="tel:+37433060609">+374 33 060 609</a></li>
                    <li><i class="fa-solid fa-phone"></i> <a href="tel:+37455060609">+374 55 060 609</a></li>
                    <li><i class="fa-solid fa-phone"></i> <a href="tel:+37444060608">+374 44 060 608</a></li>
                    <li><i class="fa-solid fa-phone"></i> <a href="tel:+37495060608">+374 95 060 608</a></li>
                    <li><i class="fa-solid fa-envelope"></i> <a href="mailto:touristik.visadepartment@gmail.com">touristik.visadepartment@gmail.com</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p data-t="footer_text"><?= htmlspecialchars(getSetting($pdo, 'footer_text', '© 2026 Touristik. All rights reserved.')) ?></p>
        </div>
    </footer>

    <a href="https://wa.me/37433060609" target="_blank" rel="noopener" class="whatsapp-float" aria-label="WhatsApp">
        <svg viewBox="0 0 32 32" width="28" height="28" fill="#fff"><path d="M16.004 0C7.174 0 .002 7.172.002 16c0 2.82.737 5.572 2.137 7.998L.012 32l8.204-2.094A15.9 15.9 0 0016.004 32C24.834 32 32 24.828 32 16S24.834 0 16.004 0zm0 29.32a13.28 13.28 0 01-7.09-2.04l-.508-.303-4.87 1.244 1.302-4.706-.332-.528A13.27 13.27 0 012.68 16c0-7.348 5.976-13.32 13.324-13.32S29.32 8.652 29.32 16s-5.968 13.32-13.316 13.32zm7.296-9.976c-.4-.2-2.367-1.168-2.734-1.301-.367-.133-.634-.2-.9.2-.268.4-1.034 1.301-1.268 1.568-.234.267-.467.3-.867.1-.4-.2-1.69-.623-3.22-1.987-1.19-1.062-1.993-2.374-2.227-2.774-.233-.4-.025-.616.175-.815.18-.18.4-.467.6-.7.2-.234.267-.4.4-.667.133-.267.067-.5-.033-.7-.1-.2-.9-2.168-1.234-2.968-.325-.78-.655-.674-.9-.686l-.767-.013c-.267 0-.7.1-1.067.5s-1.4 1.368-1.4 3.335c0 1.968 1.434 3.87 1.634 4.137.2.267 2.82 4.306 6.834 6.037.955.412 1.7.658 2.28.842.959.305 1.832.262 2.522.159.77-.115 2.367-.968 2.7-1.902.334-.934.334-1.734.234-1.902-.1-.167-.367-.267-.767-.467z"/></svg>
    </a>
    <button class="back-to-top" id="backToTop" aria-label="Back to top">&#8679;</button>

    <script src="js/script.js?v=<?= time() ?>"></script>
</body>
</html>
