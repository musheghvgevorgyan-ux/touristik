<?php use App\Helpers\View; ?>

<style>
    .admin-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem; }
    .admin-header h1 { font-size: 1.8rem; color: var(--text-heading, #1a1a2e); margin: 0; }
    .btn-primary { padding: 0.55rem 1.3rem; background: #FF6B35; color: #fff; border: none; border-radius: 6px; font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: background 0.2s; text-decoration: none; display: inline-flex; align-items: center; gap: 0.4rem; }
    .btn-primary:hover { background: #e55a2b; }
    .admin-table-wrap { background: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); overflow-x: auto; margin-bottom: 1.5rem; }
    .admin-table { width: 100%; border-collapse: collapse; min-width: 900px; }
    .admin-table th { text-align: left; padding: 0.8rem 1.2rem; border-bottom: 2px solid #eee; color: #6c757d; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap; }
    .admin-table td { padding: 0.8rem 1.2rem; border-bottom: 1px solid #f0f0f0; font-size: 0.9rem; vertical-align: middle; }
    .admin-table tr:hover td { background: #f8f9fa; }
    .badge { display: inline-block; padding: 0.2rem 0.65rem; border-radius: 4px; font-size: 0.78rem; font-weight: 600; white-space: nowrap; }
    .badge-active { background: #d4edda; color: #155724; }
    .badge-inactive { background: #f8d7da; color: #721c24; }
    .badge-draft { background: #e2e3e5; color: #383d41; }
    .dest-thumb { width: 50px; height: 35px; object-fit: cover; border-radius: 4px; background: #f0f0f0; }
    .dest-featured-yes { color: #155724; font-weight: 600; }
    .dest-featured-no { color: #999; }
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
    .form-group input[type="color"] { height: 38px; padding: 2px 4px; cursor: pointer; }
    .form-check { display: flex; align-items: center; gap: 0.5rem; padding-top: 1.5rem; }
    .form-check input[type="checkbox"] { width: 18px; height: 18px; accent-color: #FF6B35; cursor: pointer; }
    .form-check label { font-size: 0.9rem; color: #333; cursor: pointer; text-transform: none; letter-spacing: 0; font-weight: 500; }
    .form-actions { display: flex; gap: 0.8rem; margin-top: 1.2rem; }
    .btn-save { padding: 0.55rem 1.5rem; background: #FF6B35; color: #fff; border: none; border-radius: 6px; font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
    .btn-save:hover { background: #e55a2b; }
    .btn-cancel-form { padding: 0.55rem 1.5rem; background: #6c757d; color: #fff; border: none; border-radius: 6px; font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
    .btn-cancel-form:hover { background: #5a6268; }
    @media (max-width: 768px) {
        .form-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="admin-header">
    <h1><?= View::e($title) ?></h1>
    <button type="button" class="btn-primary" id="btnAddDestination">+ Add Destination</button>
</div>

<!-- Add / Edit Form Panel -->
<div class="form-panel" id="destinationFormPanel">
    <h3 id="formTitle">Add New Destination</h3>
    <form method="POST" action="/admin/destinations" id="destinationForm">
        <?= View::csrf() ?>
        <input type="hidden" name="id" id="dest-id" value="">
        <div class="form-grid">
            <div class="form-group">
                <label for="dest-name">Name</label>
                <input type="text" name="name" id="dest-name" required placeholder="e.g. Paris" value="<?= View::old('name') ?>">
            </div>
            <div class="form-group">
                <label for="dest-slug">Slug</label>
                <input type="text" name="slug" id="dest-slug" required placeholder="e.g. paris" value="<?= View::old('slug') ?>">
            </div>
            <div class="form-group full-width">
                <label for="dest-description">Description</label>
                <textarea name="description" id="dest-description" placeholder="Brief description of the destination..."><?= View::old('description') ?></textarea>
            </div>
            <div class="form-group">
                <label for="dest-country">Country</label>
                <input type="text" name="country" id="dest-country" placeholder="e.g. France" value="<?= View::old('country') ?>">
            </div>
            <div class="form-group">
                <label for="dest-price">Price From</label>
                <input type="number" name="price_from" id="dest-price" step="0.01" min="0" placeholder="e.g. 299.00" value="<?= View::old('price_from') ?>">
            </div>
            <div class="form-group full-width">
                <label for="dest-image">Image URL</label>
                <input type="url" name="image_url" id="dest-image" placeholder="https://..." value="<?= View::old('image_url') ?>">
            </div>
            <div class="form-group">
                <label for="dest-color">Card Color</label>
                <input type="color" name="color" id="dest-color" value="<?= View::old('color', '#FF6B35') ?>">
            </div>
            <div class="form-group">
                <label for="dest-emoji">Emoji</label>
                <input type="text" name="emoji" id="dest-emoji" placeholder="e.g. &#127468;&#127463;" maxlength="4" value="<?= View::old('emoji') ?>">
            </div>
            <div class="form-group">
                <label for="dest-status">Status</label>
                <select name="status" id="dest-status">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="draft">Draft</option>
                </select>
            </div>
            <div class="form-check">
                <input type="checkbox" name="featured" id="dest-featured" value="1">
                <label for="dest-featured">Featured Destination</label>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn-save">Save Destination</button>
            <button type="button" class="btn-cancel-form" id="btnCancelForm">Cancel</button>
        </div>
    </form>
</div>

<div class="admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Name</th>
                <th>Slug</th>
                <th>Country</th>
                <th>Price From</th>
                <th>Featured</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($destinations)): ?>
                <tr>
                    <td colspan="9">
                        <div class="empty-state">
                            <span class="empty-icon">&#127758;</span>
                            <p>No destinations yet. Add your first destination above.</p>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($destinations as $dest): ?>
                <tr>
                    <td><?= (int)$dest['id'] ?></td>
                    <td>
                        <?php if (!empty($dest['image_url'])): ?>
                            <img src="<?= View::e($dest['image_url']) ?>" alt="<?= View::e($dest['name']) ?>" class="dest-thumb" loading="lazy">
                        <?php else: ?>
                            <span class="dest-thumb" style="display:inline-block;background:#eee;text-align:center;line-height:35px;font-size:1.2rem;"><?= View::e($dest['emoji'] ?? '?') ?></span>
                        <?php endif; ?>
                    </td>
                    <td><strong><?= View::e($dest['name']) ?></strong></td>
                    <td><code><?= View::e($dest['slug']) ?></code></td>
                    <td><?= View::e($dest['country'] ?? '-') ?></td>
                    <td><?= isset($dest['price_from']) ? '$' . number_format($dest['price_from'], 2) : '-' ?></td>
                    <td>
                        <?php if (!empty($dest['featured'])): ?>
                            <span class="dest-featured-yes">Yes</span>
                        <?php else: ?>
                            <span class="dest-featured-no">No</span>
                        <?php endif; ?>
                    </td>
                    <td><span class="badge badge-<?= View::e($dest['status'] ?? 'active') ?>"><?= View::e(ucfirst($dest['status'] ?? 'Active')) ?></span></td>
                    <td>
                        <div class="action-btns">
                            <button type="button" class="btn-edit" onclick="editDestination(<?= View::e(json_encode($dest)) ?>)">Edit</button>
                            <form method="POST" action="/admin/destinations/<?= (int)$dest['id'] ?>/delete" style="display:inline;" onsubmit="return confirm('Delete this destination permanently?')">
                                <?= View::csrf() ?>
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
    var panel = document.getElementById('destinationFormPanel');
    var form = document.getElementById('destinationForm');
    var formTitle = document.getElementById('formTitle');
    var btnAdd = document.getElementById('btnAddDestination');
    var btnCancel = document.getElementById('btnCancelForm');
    var nameInput = document.getElementById('dest-name');
    var slugInput = document.getElementById('dest-slug');

    btnAdd.addEventListener('click', function() {
        form.reset();
        document.getElementById('dest-id').value = '';
        document.getElementById('dest-color').value = '#FF6B35';
        formTitle.textContent = 'Add New Destination';
        form.action = '/admin/destinations';
        panel.classList.add('visible');
        nameInput.focus();
    });

    btnCancel.addEventListener('click', function() {
        panel.classList.remove('visible');
    });

    // Auto-generate slug from name
    nameInput.addEventListener('input', function() {
        if (!document.getElementById('dest-id').value) {
            slugInput.value = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
        }
    });

    // Expose edit function globally
    window.editDestination = function(dest) {
        document.getElementById('dest-id').value = dest.id || '';
        document.getElementById('dest-name').value = dest.name || '';
        document.getElementById('dest-slug').value = dest.slug || '';
        document.getElementById('dest-description').value = dest.description || '';
        document.getElementById('dest-country').value = dest.country || '';
        document.getElementById('dest-price').value = dest.price_from || '';
        document.getElementById('dest-image').value = dest.image_url || '';
        document.getElementById('dest-color').value = dest.color || '#FF6B35';
        document.getElementById('dest-emoji').value = dest.emoji || '';
        document.getElementById('dest-status').value = dest.status || 'active';
        document.getElementById('dest-featured').checked = !!dest.featured;
        formTitle.textContent = 'Edit Destination';
        form.action = '/admin/destinations/' + dest.id;
        panel.classList.add('visible');
        nameInput.focus();
    };
})();
</script>
