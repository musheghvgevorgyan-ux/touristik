<?php use App\Helpers\View; ?>

<style>
    .reviews-page { max-width: 900px; margin: 0 auto; padding: 2rem 2rem 4rem; }
    .reviews-page h1 { font-size: 1.8rem; color: var(--text-heading); margin-bottom: 0.3rem; }
    .reviews-page > p { color: var(--text-secondary); margin-bottom: 2rem; }

    /* Review cards */
    .reviews-list { display: flex; flex-direction: column; gap: 1.2rem; margin-bottom: 3rem; }
    .review-card { background: var(--bg-card); border-radius: var(--radius); box-shadow: var(--shadow); padding: 1.5rem 2rem; transition: transform 0.2s; }
    .review-card:hover { transform: translateY(-2px); }
    .review-header { display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 0.6rem; }
    .review-header h3 { font-size: 1.1rem; color: var(--text-heading); margin: 0; }
    .review-stars { display: flex; gap: 2px; font-size: 1.1rem; }
    .review-stars .star-filled { color: #FFB800; }
    .review-stars .star-empty { color: var(--border-color); }
    .review-meta { display: flex; flex-wrap: wrap; gap: 1rem; font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 0.8rem; }
    .review-meta span { display: flex; align-items: center; gap: 0.3rem; }
    .review-status { display: inline-block; padding: 0.15rem 0.6rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: capitalize; }
    .review-status.pending { background: rgba(255,152,0,0.12); color: #ff9800; }
    .review-status.approved { background: rgba(52,168,83,0.12); color: #34a853; }
    .review-status.rejected { background: rgba(234,67,53,0.12); color: #ea4335; }
    .review-comment { font-size: 0.95rem; color: var(--text-primary); line-height: 1.6; margin-bottom: 0; }
    .review-admin-reply { margin-top: 1rem; padding: 1rem 1.2rem; background: var(--bg-body); border-left: 3px solid var(--primary); border-radius: 0 var(--radius) var(--radius) 0; }
    .review-admin-reply .reply-label { font-size: 0.8rem; font-weight: 600; color: var(--primary); margin-bottom: 0.4rem; display: flex; align-items: center; gap: 0.3rem; }
    .review-admin-reply p { font-size: 0.9rem; color: var(--text-primary); margin: 0; line-height: 1.5; }

    /* Leave a Review form */
    .review-form-section { background: var(--bg-card); border-radius: var(--radius); box-shadow: var(--shadow); padding: 2rem 2rem 2.5rem; margin-bottom: 2rem; }
    .review-form-section h2 { font-size: 1.3rem; color: var(--text-heading); margin-bottom: 0.3rem; }
    .review-form-section > p { color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 1.5rem; }
    .form-group { margin-bottom: 1.2rem; }
    .form-group label { display: block; font-weight: 600; margin-bottom: 0.3rem; font-size: 0.9rem; color: var(--text-secondary); }
    .form-group select,
    .form-group input,
    .form-group textarea { width: 100%; padding: 0.7rem 1rem; border: 1px solid var(--border-color); border-radius: var(--radius); font-size: 1rem; transition: border-color 0.2s; background: var(--bg-body); color: var(--text-primary); font-family: inherit; }
    .form-group select:focus,
    .form-group input:focus,
    .form-group textarea:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(255,107,53,0.1); }
    .form-group textarea { resize: vertical; min-height: 100px; }
    .form-group .hint { font-size: 0.8rem; color: var(--text-secondary); margin-top: 0.2rem; }

    /* Star rating selector */
    .star-rating-selector { display: flex; gap: 4px; flex-direction: row-reverse; justify-content: flex-end; margin-top: 0.3rem; }
    .star-rating-selector input { display: none; }
    .star-rating-selector label { font-size: 1.8rem; color: var(--border-color); cursor: pointer; transition: color 0.15s; line-height: 1; }
    .star-rating-selector label:hover,
    .star-rating-selector label:hover ~ label,
    .star-rating-selector input:checked ~ label { color: #FFB800; }
    .star-rating-selected { font-size: 0.85rem; color: var(--text-secondary); margin-top: 0.2rem; min-height: 1.2em; }

    .btn-submit { display: inline-block; padding: 0.8rem 2.5rem; background: var(--primary); color: #fff; border: none; border-radius: var(--radius); font-size: 1rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
    .btn-submit:hover { background: var(--primary-dark); }
    .btn-submit:disabled { opacity: 0.6; cursor: not-allowed; }

    /* Empty states */
    .reviews-empty { text-align: center; padding: 3rem 1rem; color: var(--text-secondary); }
    .reviews-empty .empty-icon { font-size: 3rem; margin-bottom: 0.8rem; opacity: 0.4; }
    .reviews-empty p { margin: 0; }
    .no-bookings-msg { text-align: center; padding: 2rem 1rem; color: var(--text-secondary); font-size: 0.95rem; }
    .no-bookings-msg a { color: var(--primary); text-decoration: none; font-weight: 600; }
    .no-bookings-msg a:hover { text-decoration: underline; }

    .reviews-back { margin-top: 2rem; }
    .reviews-back a { color: var(--primary); text-decoration: none; font-weight: 600; }
    .reviews-back a:hover { text-decoration: underline; }

    @media (max-width: 600px) {
        .review-card { padding: 1.2rem; }
        .review-header { flex-direction: column; }
        .review-form-section { padding: 1.5rem; }
    }
</style>

<div class="reviews-page">
    <h1 data-t="reviews_title">My Reviews</h1>
    <p data-t="reviews_subtitle">View and manage your travel reviews</p>

    <!-- Existing Reviews -->
    <?php if (!empty($reviews)): ?>
        <div class="reviews-list">
            <?php foreach ($reviews as $review): ?>
                <?php
                    $rating = (int) ($review['rating'] ?? 0);
                    $status = $review['status'] ?? 'pending';
                    $statusClass = match ($status) {
                        'approved' => 'approved',
                        'rejected' => 'rejected',
                        default    => 'pending',
                    };
                ?>
                <div class="review-card reveal">
                    <div class="review-header">
                        <h3><?= View::e($review['title'] ?? 'Untitled Review') ?></h3>
                        <div class="review-stars">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <span class="<?= $i <= $rating ? 'star-filled' : 'star-empty' ?>">&#9733;</span>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <div class="review-meta">
                        <?php if (!empty($review['booking_reference'])): ?>
                            <span>Booking: <?= View::e($review['booking_reference']) ?></span>
                        <?php endif; ?>
                        <?php if (!empty($review['product_type'])): ?>
                            <span><?= View::e(ucfirst($review['product_type'])) ?></span>
                        <?php endif; ?>
                        <span><?= View::date($review['created_at'] ?? '', 'M j, Y') ?></span>
                        <span class="review-status <?= $statusClass ?>"><?= View::e(ucfirst($status)) ?></span>
                    </div>
                    <div class="review-comment">
                        <?= nl2br(View::e($review['comment'] ?? '')) ?>
                    </div>
                    <?php if (!empty($review['admin_reply'])): ?>
                        <div class="review-admin-reply">
                            <div class="reply-label">&#128172; Response from Touristik</div>
                            <p><?= nl2br(View::e($review['admin_reply'])) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="reviews-empty reveal">
            <div class="empty-icon">&#9733;</div>
            <p data-t="no_reviews_yet">You haven't written any reviews yet.</p>
        </div>
    <?php endif; ?>

    <!-- Leave a Review Form -->
    <?php if (!empty($pendingBookings)): ?>
        <div class="review-form-section reveal" id="leaveReview">
            <h2 data-t="leave_review_title">Leave a Review</h2>
            <p data-t="leave_review_subtitle">Share your experience to help other travelers</p>

            <form method="POST" action="/account/reviews" id="reviewForm">
                <?= View::csrf() ?>

                <div class="form-group">
                    <label for="booking_id" data-t="select_booking">Select Booking</label>
                    <select id="booking_id" name="booking_id" required>
                        <option value="">-- Choose a booking --</option>
                        <?php foreach ($pendingBookings as $b): ?>
                            <option value="<?= (int) $b['id'] ?>" <?= View::old('booking_id') == $b['id'] ? 'selected' : '' ?>>
                                <?= View::e($b['reference']) ?> &mdash; <?= View::e(ucfirst($b['product_type'] ?? 'Booking')) ?> (<?= View::date($b['created_at'] ?? '', 'M j, Y') ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label data-t="your_rating">Your Rating</label>
                    <div class="star-rating-selector" id="starSelector">
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>" <?= View::old('rating') == $i ? 'checked' : '' ?> required>
                            <label for="star<?= $i ?>" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">&#9733;</label>
                        <?php endfor; ?>
                    </div>
                    <div class="star-rating-selected" id="ratingText">
                        <?php
                            $oldRating = (int) View::old('rating', '0');
                            if ($oldRating > 0) {
                                $labels = [1 => 'Poor', 2 => 'Fair', 3 => 'Good', 4 => 'Very Good', 5 => 'Excellent'];
                                echo View::e($labels[$oldRating] ?? '');
                            }
                        ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="review_title" data-t="review_title_label">Title</label>
                    <input type="text" id="review_title" name="title" value="<?= View::e(View::old('title', '')) ?>"
                           placeholder="Sum up your experience in a few words" required minlength="3" maxlength="200">
                </div>

                <div class="form-group">
                    <label for="review_comment" data-t="review_comment_label">Your Review</label>
                    <textarea id="review_comment" name="comment" rows="5"
                              placeholder="Tell us about your experience..." required minlength="10" maxlength="2000"><?= View::e(View::old('comment', '')) ?></textarea>
                    <div class="hint" data-t="review_hint">Minimum 10 characters</div>
                </div>

                <button type="submit" class="btn-submit" data-t="submit_review">Submit Review</button>
            </form>
        </div>
    <?php else: ?>
        <div class="no-bookings-msg reveal">
            <?php if (empty($reviews)): ?>
                <p data-t="no_bookings_to_review">You need a confirmed booking before you can leave a review. <a href="/hotels/search">Search hotels</a> to get started.</p>
            <?php else: ?>
                <p data-t="all_bookings_reviewed">You've reviewed all your eligible bookings. New bookings will appear here once confirmed.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="reviews-back reveal">
        <a href="/account" data-t="back_to_dashboard">&larr; Back to Dashboard</a>
    </div>
</div>

<script>
(function() {
    var labels = {1: 'Poor', 2: 'Fair', 3: 'Good', 4: 'Very Good', 5: 'Excellent'};
    var ratingText = document.getElementById('ratingText');
    var radios = document.querySelectorAll('#starSelector input[type="radio"]');

    if (radios.length && ratingText) {
        radios.forEach(function(radio) {
            radio.addEventListener('change', function() {
                ratingText.textContent = labels[parseInt(this.value)] || '';
            });
        });
    }
})();
</script>
