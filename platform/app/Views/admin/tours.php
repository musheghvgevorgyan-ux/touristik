<?php use App\Helpers\View; ?>

<style>
    .admin-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem; }
    .admin-header h1 { font-size: 1.8rem; color: var(--text-heading, #1a1a2e); margin: 0; }
    .btn-primary { padding: 0.55rem 1.3rem; background: #FF6B35; color: #fff; border: none; border-radius: 6px; font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: background 0.2s; text-decoration: none; display: inline-flex; align-items: center; gap: 0.4rem; }
    .btn-primary:hover { background: #e55a2b; }

    /* Filter Tabs */
    .filter-tabs { display: flex; gap: 0.5rem; margin-bottom: 1.2rem; flex-wrap: wrap; }
    .filter-tab { padding: 0.45rem 1rem; border-radius: 6px; font-size: 0.85rem; font-weight: 600; text-decoration: none; color: #6c757d; background: #f0f0f0; transition: all 0.2s; border: none; cursor: pointer; }
    .filter-tab:hover { background: #e0e0e0; color: #333; }
    .filter-tab.active { background: #FF6B35; color: #fff; }

    /* Table */
    .admin-table-wrap { background: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); overflow-x: auto; margin-bottom: 1.5rem; }
    .admin-table { width: 100%; border-collapse: collapse; min-width: 1000px; }
    .admin-table th { text-align: left; padding: 0.8rem 1.2rem; border-bottom: 2px solid #eee; color: #6c757d; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap; }
    .admin-table td { padding: 0.8rem 1.2rem; border-bottom: 1px solid #f0f0f0; font-size: 0.9rem; vertical-align: middle; }
    .admin-table tr:hover td { background: #f8f9fa; }

    /* Badges */
    .badge { display: inline-block; padding: 0.2rem 0.65rem; border-radius: 4px; font-size: 0.78rem; font-weight: 600; white-space: nowrap; }
    .badge-active { background: #d4edda; color: #155724; }
    .badge-inactive { background: #f8d7da; color: #721c24; }
    .badge-ingoing { background: #cce5ff; color: #004085; }
    .badge-outgoing { background: #fff3cd; color: #856404; }
    .badge-transfer { background: #e2e3e5; color: #383d41; }

    .tour-thumb { width: 60px; height: 40px; object-fit: cover; border-radius: 4px; background: #f0f0f0; }
    .tour-featured-yes { color: #155724; font-weight: 600; }
    .tour-featured-no { color: #999; }
    .btn-edit { padding: 0.3rem 0.7rem; background: #fff3cd; color: #856404; border: 1px solid #ffeeba; border-radius: 4px; font-size: 0.8rem; font-weight: 600; cursor: pointer; transition: background 0.2s; text-decoration: none; }
    .btn-edit:hover { background: #ffe69c; }
    .btn-delete { padding: 0.3rem 0.7rem; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 4px; font-size: 0.8rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
    .btn-delete:hover { background: #f1b0b7; }
    .action-btns { display: flex; gap: 0.4rem; align-items: center; }
    .empty-state { text-align: center; padding: 3rem 1.5rem; color: #6c757d; }
    .empty-state .empty-icon { font-size: 3rem; margin-bottom: 0.8rem; display: block; }

    /* Form Panel */
    .form-panel { background: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); padding: 1.5rem; margin-bottom: 1.5rem; display: none; }
    .form-panel.visible { display: block; }
    .form-panel h3 { margin: 0 0 1.2rem; font-size: 1.1rem; color: var(--text-heading, #1a1a2e); padding-bottom: 0.6rem; border-bottom: 2px solid #f0f0f0; }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .form-group { display: flex; flex-direction: column; gap: 0.3rem; }
    .form-group.full-width { grid-column: 1 / -1; }
    .form-group label { font-size: 0.8rem; font-weight: 600; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; }
    .form-group input,
    .form-group select,
    .form-group textarea { padding: 0.55rem 0.8rem; border: 1px solid #dee2e6; border-radius: 6px; font-size: 0.9rem; background: #fff; color: #333; font-family: inherit; }
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus { outline: none; border-color: #FF6B35; box-shadow: 0 0 0 3px rgba(255,107,53,0.15); }
    .form-group textarea { resize: vertical; min-height: 80px; }
    .form-check { display: flex; align-items: center; gap: 0.5rem; padding-top: 1.5rem; }
    .form-check input[type="checkbox"] { width: 18px; height: 18px; accent-color: #FF6B35; cursor: pointer; }
    .form-check label { font-size: 0.9rem; color: #333; cursor: pointer; text-transform: none; letter-spacing: 0; font-weight: 500; }
    .form-actions { display: flex; gap: 0.8rem; margin-top: 1.2rem; }
    .btn-save { padding: 0.55rem 1.5rem; background: #FF6B35; color: #fff; border: none; border-radius: 6px; font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
    .btn-save:hover { background: #e55a2b; }
    .btn-cancel-form { padding: 0.55rem 1.5rem; background: #6c757d; color: #fff; border: none; border-radius: 6px; font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
    .btn-cancel-form:hover { background: #5a6268; }

    /* Itinerary Editor */
    .itinerary-editor { margin-top: 0.5rem; }
    .itinerary-day { display: flex; gap: 0.5rem; align-items: flex-start; margin-bottom: 0.5rem; }
    .itinerary-day .day-label { min-width: 55px; padding: 0.55rem 0; font-size: 0.85rem; font-weight: 600; color: #FF6B35; white-space: nowrap; }
    .itinerary-day input { flex: 1; padding: 0.55rem 0.8rem; border: 1px solid #dee2e6; border-radius: 6px; font-size: 0.9rem; font-family: inherit; }
    .itinerary-day input:focus { outline: none; border-color: #FF6B35; box-shadow: 0 0 0 3px rgba(255,107,53,0.15); }
    .btn-remove-day { padding: 0.4rem 0.6rem; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 4px; font-size: 0.8rem; cursor: pointer; transition: background 0.2s; line-height: 1; }
    .btn-remove-day:hover { background: #f1b0b7; }
    .btn-add-day { padding: 0.4rem 0.9rem; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 4px; font-size: 0.8rem; font-weight: 600; cursor: pointer; transition: background 0.2s; margin-top: 0.3rem; }
    .btn-add-day:hover { background: #b1dfbb; }

    @media (max-width: 768px) {
        .form-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="admin-header">
    <h1><?= View::e($title) ?></h1>
    <button type="button" class="btn-primary" id="btnAddTour">+ Add Tour</button>
</div>

<!-- Add / Edit Form Panel -->
<div class="form-panel" id="tourFormPanel">
    <h3 id="formTitle">Add New Tour</h3>
    <form method="POST" action="/admin/tours" id="tourForm">
        <?= View::csrf() ?>
        <input type="hidden" name="id" id="tour-id" value="">
        <div class="form-grid">
            <div class="form-group">
                <label for="tour-title">Title</label>
                <input type="text" name="title" id="tour-title" required placeholder="e.g. Classic Yerevan Tour" value="<?= View::old('title') ?>">
            </div>
            <div class="form-group">
                <label for="tour-slug">Slug</label>
                <input type="text" name="slug" id="tour-slug" placeholder="e.g. classic-yerevan-tour" value="<?= View::old('slug') ?>">
            </div>
            <div class="form-group">
                <label for="tour-type">Type</label>
                <select name="type" id="tour-type">
                    <option value="ingoing">Ingoing</option>
                    <option value="outgoing">Outgoing</option>
                    <option value="transfer">Transfer</option>
                </select>
            </div>
            <div class="form-group">
                <label for="tour-duration">Duration</label>
                <input type="text" name="duration" id="tour-duration" placeholder="e.g. 3 days" value="<?= View::old('duration') ?>">
            </div>
            <div class="form-group full-width">
                <label for="tour-description">Description</label>
                <textarea name="description" id="tour-description" placeholder="Describe the tour..."><?= View::old('description') ?></textarea>
            </div>
            <div class="form-group">
                <label for="tour-price">Price From</label>
                <input type="number" name="price_from" id="tour-price" step="0.01" min="0" placeholder="e.g. 199.00" value="<?= View::old('price_from') ?>">
            </div>
            <div class="form-group">
                <label for="tour-destination">Destination</label>
                <select name="destination_id" id="tour-destination">
                    <option value="">-- None --</option>
                    <?php foreach ($destinations as $dest): ?>
                        <option value="<?= (int)$dest['id'] ?>"><?= View::e($dest['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group full-width">
                <label for="tour-image">Image URL</label>
                <input type="url" name="image_url" id="tour-image" placeholder="https://..." value="<?= View::old('image_url') ?>">
            </div>
            <div class="form-group">
                <label for="tour-status">Status</label>
                <select name="status" id="tour-status">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div class="form-check">
                <input type="checkbox" name="featured" id="tour-featured" value="1">
                <label for="tour-featured">Featured Tour</label>
            </div>

            <!-- Itinerary Editor -->
            <div class="form-group full-width">
                <label>Itinerary</label>
                <div class="itinerary-editor" id="itineraryEditor">
                    <div class="itinerary-day" data-day="1">
                        <span class="day-label">Day 1</span>
                        <input type="text" name="itinerary_day[]" placeholder="Describe Day 1 activities...">
                        <button type="button" class="btn-remove-day" onclick="removeDay(this)" title="Remove day">&times;</button>
                    </div>
                </div>
                <button type="button" class="btn-add-day" id="btnAddDay">+ Add Day</button>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn-save">Save Tour</button>
            <button type="button" class="btn-cancel-form" id="btnCancelForm">Cancel</button>
        </div>
    </form>
</div>

<!-- Filter Tabs -->
<div class="filter-tabs">
    <a href="/admin/tours" class="filter-tab <?= empty($typeFilter) ? 'active' : '' ?>">All</a>
    <a href="/admin/tours?type=ingoing" class="filter-tab <?= ($typeFilter ?? '') === 'ingoing' ? 'active' : '' ?>">Ingoing</a>
    <a href="/admin/tours?type=outgoing" class="filter-tab <?= ($typeFilter ?? '') === 'outgoing' ? 'active' : '' ?>">Outgoing</a>
    <a href="/admin/tours?type=transfer" class="filter-tab <?= ($typeFilter ?? '') === 'transfer' ? 'active' : '' ?>">Transfer</a>
</div>

<!-- Tours Table -->
<div class="admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Title</th>
                <th>Type</th>
                <th>Duration</th>
                <th>Price From</th>
                <th>Destination</th>
                <th>Featured</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($tours)): ?>
                <tr>
                    <td colspan="10">
                        <div class="empty-state">
                            <span class="empty-icon">&#127760;</span>
                            <p>No tours yet. Add your first tour above.</p>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($tours as $tour): ?>
                <tr>
                    <td><?= (int)$tour['id'] ?></td>
                    <td>
                        <?php if (!empty($tour['image_url'])): ?>
                            <img src="<?= View::e($tour['image_url']) ?>" alt="<?= View::e($tour['title']) ?>" class="tour-thumb" loading="lazy">
                        <?php else: ?>
                            <span class="tour-thumb" style="display:inline-block;background:#eee;text-align:center;line-height:40px;font-size:1.1rem;">&#128247;</span>
                        <?php endif; ?>
                    </td>
                    <td><strong><?= View::e($tour['title']) ?></strong></td>
                    <td><span class="badge badge-<?= View::e($tour['type']) ?>"><?= View::e(ucfirst($tour['type'])) ?></span></td>
                    <td><?= View::e($tour['duration'] ?? '-') ?></td>
                    <td><?= isset($tour['price_from']) ? '$' . number_format((float)$tour['price_from'], 2) : '-' ?></td>
                    <td><?= View::e($tour['destination_name'] ?? '-') ?></td>
                    <td>
                        <?php if (!empty($tour['featured'])): ?>
                            <span class="tour-featured-yes">Yes</span>
                        <?php else: ?>
                            <span class="tour-featured-no">No</span>
                        <?php endif; ?>
                    </td>
                    <td><span class="badge badge-<?= View::e($tour['status'] ?? 'active') ?>"><?= View::e(ucfirst($tour['status'] ?? 'Active')) ?></span></td>
                    <td>
                        <div class="action-btns">
                            <button type="button" class="btn-edit" onclick='editTour(<?= View::e(json_encode($tour)) ?>)'>Edit</button>
                            <form method="POST" action="/admin/tours/delete" style="display:inline;" onsubmit="return confirm('Delete this tour permanently?')">
                                <?= View::csrf() ?>
                                <input type="hidden" name="id" value="<?= (int)$tour['id'] ?>">
                                <button type="submit" class="btn-delete">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
(function() {
    var panel = document.getElementById('tourFormPanel');
    var form = document.getElementById('tourForm');
    var formTitle = document.getElementById('formTitle');
    var btnAdd = document.getElementById('btnAddTour');
    var btnCancel = document.getElementById('btnCancelForm');
    var titleInput = document.getElementById('tour-title');
    var slugInput = document.getElementById('tour-slug');
    var editor = document.getElementById('itineraryEditor');

    btnAdd.addEventListener('click', function() {
        form.reset();
        document.getElementById('tour-id').value = '';
        formTitle.textContent = 'Add New Tour';
        // Reset itinerary to one empty day
        resetItinerary([]);
        panel.classList.add('visible');
        titleInput.focus();
    });

    btnCancel.addEventListener('click', function() {
        panel.classList.remove('visible');
    });

    // Auto-generate slug from title (only for new tours)
    titleInput.addEventListener('input', function() {
        if (!document.getElementById('tour-id').value) {
            slugInput.value = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
        }
    });

    // Itinerary: Add Day
    document.getElementById('btnAddDay').addEventListener('click', function() {
        addItineraryDay('');
    });

    function addItineraryDay(text) {
        var days = editor.querySelectorAll('.itinerary-day');
        var dayNum = days.length + 1;
        var div = document.createElement('div');
        div.className = 'itinerary-day';
        div.setAttribute('data-day', dayNum);
        div.innerHTML =
            '<span class="day-label">Day ' + dayNum + '</span>' +
            '<input type="text" name="itinerary_day[]" placeholder="Describe Day ' + dayNum + ' activities..." value="' + escapeAttr(text) + '">' +
            '<button type="button" class="btn-remove-day" onclick="removeDay(this)" title="Remove day">&times;</button>';
        editor.appendChild(div);
    }

    function resetItinerary(days) {
        editor.innerHTML = '';
        if (!days || days.length === 0) {
            addItineraryDay('');
        } else {
            for (var i = 0; i < days.length; i++) {
                addItineraryDay(days[i].description || '');
            }
        }
    }

    function renumberDays() {
        var rows = editor.querySelectorAll('.itinerary-day');
        for (var i = 0; i < rows.length; i++) {
            rows[i].setAttribute('data-day', i + 1);
            rows[i].querySelector('.day-label').textContent = 'Day ' + (i + 1);
            rows[i].querySelector('input').placeholder = 'Describe Day ' + (i + 1) + ' activities...';
        }
    }

    function escapeAttr(str) {
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(str));
        return div.innerHTML.replace(/"/g, '&quot;');
    }

    // Expose globally
    window.removeDay = function(btn) {
        var row = btn.closest('.itinerary-day');
        row.remove();
        renumberDays();
        // Ensure at least one day remains
        if (editor.querySelectorAll('.itinerary-day').length === 0) {
            addItineraryDay('');
        }
    };

    window.editTour = function(tour) {
        document.getElementById('tour-id').value = tour.id || '';
        document.getElementById('tour-title').value = tour.title || '';
        document.getElementById('tour-slug').value = tour.slug || '';
        document.getElementById('tour-type').value = tour.type || 'ingoing';
        document.getElementById('tour-description').value = tour.description || '';
        document.getElementById('tour-duration').value = tour.duration || '';
        document.getElementById('tour-price').value = tour.price_from || '';
        document.getElementById('tour-image').value = tour.image_url || '';
        document.getElementById('tour-destination').value = tour.destination_id || '';
        document.getElementById('tour-status').value = tour.status || 'active';
        document.getElementById('tour-featured').checked = !!(tour.featured && tour.featured != '0');

        // Parse itinerary JSON
        var itinerary = [];
        if (tour.itinerary) {
            try {
                itinerary = typeof tour.itinerary === 'string' ? JSON.parse(tour.itinerary) : tour.itinerary;
            } catch (e) {
                itinerary = [];
            }
        }
        resetItinerary(itinerary);

        formTitle.textContent = 'Edit Tour';
        panel.classList.add('visible');
        titleInput.focus();
    };
})();
</script>
