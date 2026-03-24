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

    <script src="js/script.js?v=<?= time() ?>"></script>
</body>
</html>
