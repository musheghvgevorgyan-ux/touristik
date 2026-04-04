@extends('layouts.main')

@section('title', 'Manage Tours - Touristik')

@push('styles')
<style>
    .admin-page { max-width: 1300px; margin: 0 auto; padding: 6rem 2rem 4rem; }
    .admin-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem; }
    .admin-header h1 { font-size: 1.8rem; color: var(--text-heading); margin: 0; }
    .btn-primary { padding: 0.55rem 1.3rem; background: var(--primary); color: #fff; border: none; border-radius: 6px; font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: background 0.2s; text-decoration: none; }
    .btn-primary:hover { background: var(--primary-dark); }

    .filter-tabs { display: flex; gap: 0.5rem; margin-bottom: 1.2rem; flex-wrap: wrap; }
    .filter-tab { padding: 0.45rem 1rem; border-radius: 6px; font-size: 0.85rem; font-weight: 600; text-decoration: none; color: #6c757d; background: rgba(0,0,0,0.04); transition: all 0.2s; }
    .filter-tab:hover { background: rgba(0,0,0,0.08); color: #333; }
    .filter-tab.active { background: var(--primary); color: #fff; }

    .admin-table-wrap { background: var(--bg-card); border-radius: 10px; box-shadow: var(--shadow); overflow-x: auto; margin-bottom: 1.5rem; }
    .admin-table { width: 100%; border-collapse: collapse; min-width: 900px; }
    .admin-table th { text-align: left; padding: 0.7rem 1rem; border-bottom: 2px solid rgba(0,0,0,0.06); color: #6c757d; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px; }
    .admin-table td { padding: 0.7rem 1rem; border-bottom: 1px solid rgba(0,0,0,0.04); font-size: 0.88rem; vertical-align: middle; }
    .admin-table tr:hover td { background: rgba(0,0,0,0.02); }
    .badge { display: inline-block; padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600; }
    .badge-active { background: #d4edda; color: #155724; }
    .badge-inactive { background: #f8d7da; color: #721c24; }
    .badge-ingoing { background: #cce5ff; color: #004085; }
    .badge-outgoing { background: #fff3cd; color: #856404; }
    .badge-transfer { background: #e2e3e5; color: #383d41; }
    .tour-thumb { width: 60px; height: 40px; object-fit: cover; border-radius: 4px; }
    .action-btns { display: flex; gap: 0.4rem; }
    .btn-edit { padding: 0.3rem 0.7rem; background: #fff3cd; color: #856404; border: 1px solid #ffeeba; border-radius: 4px; font-size: 0.8rem; font-weight: 600; cursor: pointer; }
    .btn-edit:hover { background: #ffe69c; }
    .btn-delete { padding: 0.3rem 0.7rem; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 4px; font-size: 0.8rem; font-weight: 600; cursor: pointer; }
    .btn-delete:hover { background: #f1b0b7; }

    /* Form Panel */
    .form-panel { background: var(--bg-card); border-radius: 10px; box-shadow: var(--shadow); padding: 1.5rem; margin-bottom: 1.5rem; display: none; }
    .form-panel.visible { display: block; }
    .form-panel h3 { margin: 0 0 1rem; font-size: 1.1rem; color: var(--text-heading); padding-bottom: 0.6rem; border-bottom: 2px solid rgba(0,0,0,0.06); }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .form-group { display: flex; flex-direction: column; gap: 0.3rem; }
    .form-group.full-width { grid-column: 1 / -1; }
    .form-group label { font-size: 0.78rem; font-weight: 600; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; }
    .form-group input, .form-group select, .form-group textarea {
        padding: 0.5rem 0.7rem; border: 1px solid rgba(0,0,0,0.12); border-radius: 6px; font-size: 0.88rem; background: var(--bg-body); color: var(--text-primary); font-family: inherit;
    }
    .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
    .form-group textarea { resize: vertical; min-height: 80px; }
    .form-check { display: flex; align-items: center; gap: 0.5rem; padding-top: 1.5rem; }
    .form-check input[type="checkbox"] { width: 18px; height: 18px; accent-color: var(--primary); }
    .form-check label { font-size: 0.9rem; text-transform: none; letter-spacing: 0; font-weight: 500; }
    .form-actions { display: flex; gap: 0.8rem; margin-top: 1.2rem; }
    .btn-save { padding: 0.55rem 1.5rem; background: var(--primary); color: #fff; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; }
    .btn-cancel-form { padding: 0.55rem 1.5rem; background: #6c757d; color: #fff; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; }

    /* List editors */
    .list-editor { margin-top: 0.3rem; }
    .list-editor-row { display: flex; gap: 0.4rem; margin-bottom: 0.4rem; }
    .list-editor-row input { flex: 1; }
    .btn-remove-row { padding: 0.35rem 0.6rem; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 4px; font-size: 0.8rem; cursor: pointer; line-height: 1; }
    .btn-add-row { padding: 0.35rem 0.8rem; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 4px; font-size: 0.8rem; font-weight: 600; cursor: pointer; margin-top: 0.2rem; }

    /* Itinerary editor */
    .itin-row { display: grid; grid-template-columns: auto 1fr 1fr auto; gap: 0.4rem; margin-bottom: 0.4rem; align-items: start; }
    .itin-label { padding: 0.5rem 0; font-size: 0.85rem; font-weight: 600; color: var(--primary); white-space: nowrap; min-width: 40px; }

    @media (max-width: 768px) { .form-grid { grid-template-columns: 1fr; } .itin-row { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="admin-page">
    <div class="admin-header">
        <h1>Manage Tours</h1>
        <button type="button" class="btn-primary" id="btnAddTour">+ Add Tour</button>
    </div>

    <!-- Form Panel -->
    <div class="form-panel" id="tourFormPanel">
        <h3 id="formTitle">Add New Tour</h3>
        <form method="POST" action="/admin/tours" id="tourForm">
            @csrf
            <input type="hidden" name="_tour_id" id="tour-id" value="">
            <div class="form-grid">
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" id="f-title" required placeholder="e.g. Classic Yerevan Tour">
                </div>
                <div class="form-group">
                    <label>Slug</label>
                    <input type="text" name="slug" id="f-slug" placeholder="auto-generated">
                </div>
                <div class="form-group">
                    <label>Type</label>
                    <select name="type" id="f-type">
                        <option value="ingoing">Ingoing</option>
                        <option value="outgoing">Outgoing</option>
                        <option value="transfer">Transfer</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Region</label>
                    <select name="region" id="f-region">
                        <option value="">-- None --</option>
                        @foreach(['yerevan','aragatsotn','ararat','armavir','gegharkunik','kotayk','lori','shirak','syunik','tavush','vayots_dzor'] as $r)
                        <option value="{{ $r }}">{{ ucwords(str_replace('_',' ',$r)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Duration</label>
                    <input type="text" name="duration" id="f-duration" placeholder="e.g. 3 Days">
                </div>
                <div class="form-group">
                    <label>Price From ($)</label>
                    <input type="number" name="price_from" id="f-price" step="0.01" min="0" placeholder="e.g. 199">
                </div>
                <div class="form-group full-width">
                    <label>Description</label>
                    <textarea name="description" id="f-desc" rows="4" placeholder="Describe the tour..."></textarea>
                </div>
                <div class="form-group full-width">
                    <label>Main Image URL</label>
                    <input type="url" name="image_url" id="f-image" placeholder="https://...">
                </div>
                <div class="form-group">
                    <label>Destination</label>
                    <select name="destination_id" id="f-dest">
                        <option value="">-- None --</option>
                        @foreach($destinations as $dest)
                        <option value="{{ $dest->id }}">{{ $dest->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" id="f-status">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="featured" id="f-featured" value="1">
                    <label for="f-featured">Featured Tour</label>
                </div>

                <!-- Itinerary -->
                <div class="form-group full-width">
                    <label>Itinerary</label>
                    <div class="list-editor" id="itinEditor"></div>
                    <button type="button" class="btn-add-row" onclick="addItin()">+ Add Step</button>
                </div>

                <!-- Gallery -->
                <div class="form-group full-width">
                    <label>Gallery (Image URLs)</label>
                    <div class="list-editor" id="galleryEditor"></div>
                    <button type="button" class="btn-add-row" onclick="addListRow('galleryEditor','gallery_url','Image URL')">+ Add Image</button>
                </div>

                <!-- Includes -->
                <div class="form-group">
                    <label>What's Included</label>
                    <div class="list-editor" id="includesEditor"></div>
                    <button type="button" class="btn-add-row" onclick="addListRow('includesEditor','includes_item','e.g. Transport')">+ Add</button>
                </div>

                <!-- Excludes -->
                <div class="form-group">
                    <label>What's Excluded</label>
                    <div class="list-editor" id="excludesEditor"></div>
                    <button type="button" class="btn-add-row" onclick="addListRow('excludesEditor','excludes_item','e.g. Meals')">+ Add</button>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-save">Save Tour</button>
                <button type="button" class="btn-cancel-form" onclick="document.getElementById('tourFormPanel').classList.remove('visible')">Cancel</button>
            </div>
        </form>
    </div>

    <!-- Filter Tabs -->
    <div class="filter-tabs">
        <a href="/admin/tours" class="filter-tab {{ empty($typeFilter) ? 'active' : '' }}">All ({{ App\Models\Tour::count() }})</a>
        <a href="/admin/tours?type=ingoing" class="filter-tab {{ ($typeFilter ?? '') === 'ingoing' ? 'active' : '' }}">Ingoing</a>
        <a href="/admin/tours?type=outgoing" class="filter-tab {{ ($typeFilter ?? '') === 'outgoing' ? 'active' : '' }}">Outgoing</a>
        <a href="/admin/tours?type=transfer" class="filter-tab {{ ($typeFilter ?? '') === 'transfer' ? 'active' : '' }}">Transfer</a>
    </div>

    <!-- Tours Table -->
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Region</th>
                    <th>Duration</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tours as $tour)
                <tr>
                    <td>
                        @if($tour->image_url)
                        <img src="{{ $tour->image_url }}" alt="" class="tour-thumb" loading="lazy">
                        @else
                        <span class="tour-thumb" style="display:inline-block;background:#eee;text-align:center;line-height:40px;">&#128247;</span>
                        @endif
                    </td>
                    <td><strong><a href="/tours/{{ $tour->slug }}" target="_blank" style="color:inherit;">{{ $tour->title }}</a></strong></td>
                    <td><span class="badge badge-{{ $tour->type }}">{{ ucfirst($tour->type) }}</span></td>
                    <td>{{ $tour->region ? ucwords(str_replace('_',' ',$tour->region)) : '-' }}</td>
                    <td>{{ $tour->duration ?? '-' }}</td>
                    <td>{{ $tour->price_from ? '$'.number_format($tour->price_from, 0) : '-' }}</td>
                    <td><span class="badge badge-{{ $tour->status }}">{{ ucfirst($tour->status) }}</span></td>
                    <td>
                        <div class="action-btns">
                            <button type="button" class="btn-edit" onclick='editTour(@json($tour))'>Edit</button>
                            <form method="POST" action="/admin/tours/delete" style="display:inline;" onsubmit="return confirm('Delete this tour?')">
                                @csrf
                                <input type="hidden" name="id" value="{{ $tour->id }}">
                                <button type="submit" class="btn-delete">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" style="text-align:center;padding:3rem;color:#6c757d;">No tours yet. Click "+ Add Tour" above.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $tours->appends(['type' => $typeFilter])->links() }}
</div>
@endsection

@push('scripts')
<script>
(function() {
    var panel = document.getElementById('tourFormPanel');
    var form = document.getElementById('tourForm');

    document.getElementById('btnAddTour').addEventListener('click', function() {
        form.reset();
        form.action = '/admin/tours';
        document.getElementById('tour-id').value = '';
        document.getElementById('formTitle').textContent = 'Add New Tour';
        resetEditors([], [], [], []);
        panel.classList.add('visible');
        document.getElementById('f-title').focus();
    });

    document.getElementById('f-title').addEventListener('input', function() {
        if (!document.getElementById('tour-id').value) {
            document.getElementById('f-slug').value = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
        }
    });

    window.editTour = function(tour) {
        document.getElementById('tour-id').value = tour.id;
        form.action = '/admin/tours/' + tour.id;
        document.getElementById('formTitle').textContent = 'Edit Tour';
        document.getElementById('f-title').value = tour.title || '';
        document.getElementById('f-slug').value = tour.slug || '';
        document.getElementById('f-type').value = tour.type || 'ingoing';
        document.getElementById('f-region').value = tour.region || '';
        document.getElementById('f-duration').value = tour.duration || '';
        document.getElementById('f-price').value = tour.price_from || '';
        document.getElementById('f-desc').value = tour.description || '';
        document.getElementById('f-image').value = tour.image_url || '';
        document.getElementById('f-dest').value = tour.destination_id || '';
        document.getElementById('f-status').value = tour.status || 'active';
        document.getElementById('f-featured').checked = !!tour.featured;

        var itin = tour.itinerary || [];
        var gallery = tour.gallery || [];
        var incl = tour.includes || [];
        var excl = tour.excludes || [];
        if (typeof itin === 'string') itin = JSON.parse(itin);
        if (typeof gallery === 'string') gallery = JSON.parse(gallery);
        if (typeof incl === 'string') incl = JSON.parse(incl);
        if (typeof excl === 'string') excl = JSON.parse(excl);

        resetEditors(itin, gallery, incl, excl);
        panel.classList.add('visible');
        document.getElementById('f-title').focus();
    };

    function resetEditors(itin, gallery, incl, excl) {
        var ie = document.getElementById('itinEditor'); ie.innerHTML = '';
        if (itin.length === 0) addItin();
        else itin.forEach(function(item) { addItin(item.title, item.description); });

        var ge = document.getElementById('galleryEditor'); ge.innerHTML = '';
        if (gallery.length === 0) addListRow('galleryEditor','gallery_url','Image URL');
        else gallery.forEach(function(url) { addListRow('galleryEditor','gallery_url','Image URL', url); });

        var ie2 = document.getElementById('includesEditor'); ie2.innerHTML = '';
        if (incl.length === 0) addListRow('includesEditor','includes_item','e.g. Transport');
        else incl.forEach(function(v) { addListRow('includesEditor','includes_item','', v); });

        var ee = document.getElementById('excludesEditor'); ee.innerHTML = '';
        if (excl.length === 0) addListRow('excludesEditor','excludes_item','e.g. Meals');
        else excl.forEach(function(v) { addListRow('excludesEditor','excludes_item','', v); });
    }

    window.addItin = function(title, desc) {
        var el = document.getElementById('itinEditor');
        var n = el.children.length + 1;
        var div = document.createElement('div');
        div.className = 'itin-row';
        div.innerHTML = '<span class="itin-label">' + n + '.</span>' +
            '<input type="text" name="itinerary_title[]" placeholder="Title" value="' + esc(title||'') + '">' +
            '<input type="text" name="itinerary_desc[]" placeholder="Description" value="' + esc(desc||'') + '">' +
            '<button type="button" class="btn-remove-row" onclick="this.parentElement.remove()">&times;</button>';
        el.appendChild(div);
    };

    window.addListRow = function(editorId, fieldName, placeholder, value) {
        var el = document.getElementById(editorId);
        var div = document.createElement('div');
        div.className = 'list-editor-row';
        div.innerHTML = '<input type="text" name="' + fieldName + '[]" placeholder="' + (placeholder||'') + '" value="' + esc(value||'') + '">' +
            '<button type="button" class="btn-remove-row" onclick="this.parentElement.remove()">&times;</button>';
        el.appendChild(div);
    };

    function esc(s) { var d = document.createElement('div'); d.textContent = s; return d.innerHTML.replace(/"/g,'&quot;'); }
})();
</script>
@endpush
