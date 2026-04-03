@extends('layouts.main')

@section('title', 'Promo Codes - Touristik')

@push('styles')
<style>
    .admin-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem; }
    .admin-header h1 { font-size: 1.8rem; color: var(--text-heading, #1a1a2e); margin: 0; }
    .btn-primary { padding: 0.55rem 1.3rem; background: #FF6B35; color: #fff; border: none; border-radius: 6px; font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: background 0.2s; text-decoration: none; display: inline-flex; align-items: center; gap: 0.4rem; }
    .btn-primary:hover { background: #e55a2b; }
    .admin-table-wrap { background: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); overflow-x: auto; margin-bottom: 1.5rem; }
    .admin-table { width: 100%; border-collapse: collapse; min-width: 850px; }
    .admin-table th { text-align: left; padding: 0.8rem 1.2rem; border-bottom: 2px solid #eee; color: #6c757d; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap; }
    .admin-table td { padding: 0.8rem 1.2rem; border-bottom: 1px solid #f0f0f0; font-size: 0.9rem; vertical-align: middle; }
    .admin-table tr:hover td { background: #f8f9fa; }
    .badge { display: inline-block; padding: 0.2rem 0.65rem; border-radius: 4px; font-size: 0.78rem; font-weight: 600; white-space: nowrap; }
    .badge-active { background: #d4edda; color: #155724; }
    .badge-inactive, .badge-expired { background: #f8d7da; color: #721c24; }
    .badge-scheduled { background: #d1ecf1; color: #0c5460; }
    .promo-code { font-family: 'Courier New', monospace; font-weight: 700; font-size: 0.9rem; background: #f8f9fa; padding: 0.15rem 0.5rem; border-radius: 3px; letter-spacing: 0.5px; }
    .usage-info { font-size: 0.85rem; color: #6c757d; }
    .usage-info strong { color: #333; }
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
    .form-group select { padding: 0.55rem 0.8rem; border: 1px solid #dee2e6; border-radius: 6px; font-size: 0.9rem; background: #fff; color: #333; font-family: inherit; }
    .form-group input:focus,
    .form-group select:focus { outline: none; border-color: #FF6B35; box-shadow: 0 0 0 3px rgba(255,107,53,0.15); }
    .code-input-wrap { display: flex; gap: 0.5rem; }
    .code-input-wrap input { flex: 1; font-family: 'Courier New', monospace; text-transform: uppercase; letter-spacing: 1px; }
    .btn-generate { padding: 0.55rem 0.8rem; background: #e2e3e5; color: #333; border: 1px solid #dee2e6; border-radius: 6px; font-size: 0.8rem; font-weight: 600; cursor: pointer; white-space: nowrap; transition: background 0.2s; }
    .btn-generate:hover { background: #d6d8db; }
    .checkbox-group { display: flex; flex-wrap: wrap; gap: 0.8rem; padding-top: 0.3rem; }
    .checkbox-group label { display: flex; align-items: center; gap: 0.3rem; font-size: 0.88rem; color: #333; cursor: pointer; text-transform: none; letter-spacing: 0; font-weight: 500; }
    .checkbox-group input[type="checkbox"] { width: 16px; height: 16px; accent-color: #FF6B35; cursor: pointer; }
    .form-actions { display: flex; gap: 0.8rem; margin-top: 1.2rem; }
    .btn-save { padding: 0.55rem 1.5rem; background: #FF6B35; color: #fff; border: none; border-radius: 6px; font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
    .btn-save:hover { background: #e55a2b; }
    .btn-cancel-form { padding: 0.55rem 1.5rem; background: #6c757d; color: #fff; border: none; border-radius: 6px; font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
    .btn-cancel-form:hover { background: #5a6268; }
    @media (max-width: 768px) {
        .form-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div class="admin-header">
    <h1>{{ $title }}</h1>
    <button type="button" class="btn-primary" id="btnCreatePromo">+ Create Promo</button>
</div>

<!-- Create / Edit Form Panel -->
<div class="form-panel" id="promoFormPanel">
    <h3 id="promoFormTitle">Create Promo Code</h3>
    <form method="POST" action="/admin/promos" id="promoForm">
        @csrf
        <input type="hidden" name="id" id="promo-id" value="">
        <div class="form-grid">
            <div class="form-group">
                <label for="promo-code">Promo Code</label>
                <div class="code-input-wrap">
                    <input type="text" name="code" id="promo-code" required placeholder="SUMMER2026" value="{{ old('code') }}">
                    <button type="button" class="btn-generate" id="btnGenerateCode">Generate</button>
                </div>
            </div>
            <div class="form-group">
                <label for="promo-type">Discount Type</label>
                <select name="type" id="promo-type">
                    <option value="percentage">Percentage (%)</option>
                    <option value="fixed">Fixed Amount ($)</option>
                </select>
            </div>
            <div class="form-group">
                <label for="promo-value">Discount Value</label>
                <input type="number" name="value" id="promo-value" step="0.01" min="0" required placeholder="e.g. 10" value="{{ old('value') }}">
            </div>
            <div class="form-group">
                <label for="promo-min-order">Minimum Order ($)</label>
                <input type="number" name="min_order" id="promo-min-order" step="0.01" min="0" placeholder="e.g. 100" value="{{ old('min_order') }}">
            </div>
            <div class="form-group">
                <label for="promo-max-discount">Max Discount ($)</label>
                <input type="number" name="max_discount" id="promo-max-discount" step="0.01" min="0" placeholder="e.g. 50 (percentage type only)" value="{{ old('max_discount') }}">
            </div>
            <div class="form-group">
                <label for="promo-usage-limit">Total Usage Limit</label>
                <input type="number" name="usage_limit" id="promo-usage-limit" min="0" placeholder="0 = unlimited" value="{{ old('usage_limit') }}">
            </div>
            <div class="form-group">
                <label for="promo-per-user">Per User Limit</label>
                <input type="number" name="per_user_limit" id="promo-per-user" min="0" placeholder="0 = unlimited" value="{{ old('per_user_limit') }}">
            </div>
            <div class="form-group">
                <label for="promo-status">Status</label>
                <select name="status" id="promo-status">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div class="form-group">
                <label for="promo-starts">Starts At</label>
                <input type="datetime-local" name="starts_at" id="promo-starts" value="{{ old('starts_at') }}">
            </div>
            <div class="form-group">
                <label for="promo-expires">Expires At</label>
                <input type="datetime-local" name="expires_at" id="promo-expires" value="{{ old('expires_at') }}">
            </div>
            <div class="form-group full-width">
                <label>Applicable Product Types</label>
                <div class="checkbox-group">
                    <label><input type="checkbox" name="product_types[]" value="hotel"> Hotels</label>
                    <label><input type="checkbox" name="product_types[]" value="tour"> Tours</label>
                    <label><input type="checkbox" name="product_types[]" value="transfer"> Transfers</label>
                    <label><input type="checkbox" name="product_types[]" value="flight"> Flights</label>
                </div>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn-save">Save Promo</button>
            <button type="button" class="btn-cancel-form" id="btnCancelPromo">Cancel</button>
        </div>
    </form>
</div>

<div class="admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Code</th>
                <th>Type</th>
                <th>Value</th>
                <th>Usage</th>
                <th>Valid Period</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @if(empty($promos))
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <span class="empty-icon">&#127873;</span>
                            <p>No promo codes yet. Create your first one above.</p>
                        </div>
                    </td>
                </tr>
            @else
                @foreach($promos as $promo)
                    @php
                    $now = time();
                    $isExpired = !empty($promo['expires_at']) && strtotime($promo['expires_at']) < $now;
                    $isScheduled = !empty($promo['starts_at']) && strtotime($promo['starts_at']) > $now;
                    $statusClass = $isExpired ? 'expired' : ($isScheduled ? 'scheduled' : ($promo['status'] ?? 'active'));
                    $statusLabel = $isExpired ? 'Expired' : ($isScheduled ? 'Scheduled' : ucfirst($promo['status'] ?? 'Active'));
                    @endphp
                <tr>
                    <td><span class="promo-code">{{ $promo['code'] }}</span></td>
                    <td>{{ ucfirst($promo['type'] ?? 'percentage') }}</td>
                    <td>
                        @if(($promo['type'] ?? 'percentage') === 'percentage')
                            <strong>{!! number_format($promo['value'] ?? 0, 0) !!}%</strong>
                        @else
                            <strong>${!! number_format($promo['value'] ?? 0, 2) !!}</strong>
                        @endif
                    </td>
                    <td>
                        <span class="usage-info">
                            <strong>{!! (int)($promo['usage_count'] ?? 0) !!}</strong>
                            / {!! (int)($promo['usage_limit'] ?? 0) > 0 ? (int)$promo['usage_limit'] : '&infin;' !!}
                        </span>
                    </td>
                    <td>
                        @if(!empty($promo['starts_at']) || !empty($promo['expires_at']))
                            {!! !empty($promo['starts_at']) ? \Carbon\Carbon::parse($promo['starts_at'])->format('M d, Y') : 'N/A' !!}
                            &ndash;
                            {!! !empty($promo['expires_at']) ? \Carbon\Carbon::parse($promo['expires_at'])->format('M d, Y') : 'N/A' !!}
                        @else
                            No limit
                        @endif
                    </td>
                    <td><span class="badge badge-{{ $statusClass }}">{{ $statusLabel }}</span></td>
                    <td>
                        <div class="action-btns">
                            <button type="button" class="btn-edit" onclick="editPromo({{ json_encode($promo) }})">Edit</button>
                            <form method="POST" action="/admin/promos/{!! (int)$promo['id'] !!}/delete" style="display:inline;" onsubmit="return confirm('Delete this promo code?')">
                                @csrf
                                <button type="submit" class="btn-delete">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>

<script>
(function() {
    var panel = document.getElementById('promoFormPanel');
    var form = document.getElementById('promoForm');
    var formTitle = document.getElementById('promoFormTitle');
    var btnCreate = document.getElementById('btnCreatePromo');
    var btnCancel = document.getElementById('btnCancelPromo');
    var btnGenerate = document.getElementById('btnGenerateCode');
    var codeInput = document.getElementById('promo-code');

    btnCreate.addEventListener('click', function() {
        form.reset();
        document.getElementById('promo-id').value = '';
        formTitle.textContent = 'Create Promo Code';
        form.action = '/admin/promos';
        panel.classList.add('visible');
        codeInput.focus();
    });

    btnCancel.addEventListener('click', function() {
        panel.classList.remove('visible');
    });

    btnGenerate.addEventListener('click', function() {
        var chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        var code = '';
        for (var i = 0; i < 8; i++) {
            code += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        codeInput.value = code;
    });

    // Format datetime-local value from DB datetime
    function toLocalDatetime(dt) {
        if (!dt) return '';
        // Supports "YYYY-MM-DD HH:MM:SS" and ISO formats
        var d = new Date(dt.replace(' ', 'T'));
        if (isNaN(d.getTime())) return '';
        return d.getFullYear() + '-' +
            String(d.getMonth()+1).padStart(2,'0') + '-' +
            String(d.getDate()).padStart(2,'0') + 'T' +
            String(d.getHours()).padStart(2,'0') + ':' +
            String(d.getMinutes()).padStart(2,'0');
    }

    window.editPromo = function(promo) {
        document.getElementById('promo-id').value = promo.id || '';
        document.getElementById('promo-code').value = promo.code || '';
        document.getElementById('promo-type').value = promo.type || 'percentage';
        document.getElementById('promo-value').value = promo.value || '';
        document.getElementById('promo-min-order').value = promo.min_order || '';
        document.getElementById('promo-max-discount').value = promo.max_discount || '';
        document.getElementById('promo-usage-limit').value = promo.usage_limit || '';
        document.getElementById('promo-per-user').value = promo.per_user_limit || '';
        document.getElementById('promo-status').value = promo.status || 'active';
        document.getElementById('promo-starts').value = toLocalDatetime(promo.starts_at);
        document.getElementById('promo-expires').value = toLocalDatetime(promo.expires_at);

        // Handle product type checkboxes
        var boxes = form.querySelectorAll('input[name="product_types[]"]');
        var types = promo.product_types || [];
        if (typeof types === 'string') {
            try { types = JSON.parse(types); } catch(e) { types = types.split(','); }
        }
        boxes.forEach(function(box) {
            box.checked = types.indexOf(box.value) !== -1;
        });

        formTitle.textContent = 'Edit Promo Code';
        form.action = '/admin/promos/' + promo.id;
        panel.classList.add('visible');
        codeInput.focus();
    };
})();
</script>
@endsection
