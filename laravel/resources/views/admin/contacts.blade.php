@extends('layouts.main')

@section('title', 'Contact Messages - Admin')

@push('styles')
<style>
    .admin-header { margin-bottom: 2rem; }
    .admin-header h1 { font-size: 1.8rem; color: var(--text-heading); margin-bottom: 0.3rem; }
    .admin-header p { color: #6c757d; }
    .admin-table-wrap { background: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); overflow-x: auto; }
    .admin-table { width: 100%; border-collapse: collapse; }
    .admin-table th { text-align: left; padding: 0.8rem 1.2rem; border-bottom: 2px solid #eee; color: #6c757d; font-size: 0.85rem; text-transform: uppercase; }
    .admin-table td { padding: 0.8rem 1.2rem; border-bottom: 1px solid #f0f0f0; font-size: 0.95rem; }
    .admin-table tr:hover td { background: #f8f9fa; }
    .badge-new { background: #fff3cd; color: #856404; padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.8rem; font-weight: 600; }
    .badge-read { background: #d4edda; color: #155724; padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.8rem; font-weight: 600; }
    .msg-preview { max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: var(--text-secondary); }
    .msg-full { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center; }
    .msg-full.active { display: flex; }
    .msg-modal { background: #fff; border-radius: 12px; padding: 2rem; max-width: 600px; width: 90%; max-height: 80vh; overflow-y: auto; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
    .msg-modal h3 { margin: 0 0 0.5rem; }
    .msg-modal .msg-meta { color: #6c757d; font-size: 0.85rem; margin-bottom: 1rem; padding-bottom: 0.8rem; border-bottom: 1px solid #eee; }
    .msg-modal .msg-body { line-height: 1.7; color: #333; }
    .msg-modal .btn-close { margin-top: 1rem; padding: 0.5rem 1.5rem; background: #eee; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; }
    .btn-toggle { padding: 0.3rem 0.8rem; border: 1px solid #ddd; border-radius: 4px; background: #fff; cursor: pointer; font-size: 0.8rem; }
    .btn-toggle:hover { background: #f0f0f0; }
    .back-link { margin-top: 1.5rem; }
    .back-link a { color: #FF6B35; text-decoration: none; font-weight: 600; }
</style>
@endpush

@section('content')
<div style="max-width:1200px;margin:0 auto;padding:2rem;">
    <div class="admin-header">
        <h1>&#9993; Contact Messages</h1>
        <p>Messages from the contact form on your website</p>
    </div>

    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contacts as $c)
                <tr>
                    <td><span class="badge-{{ $c->status }}">{{ ucfirst($c->status) }}</span></td>
                    <td><strong>{{ $c->name }}</strong></td>
                    <td><a href="mailto:{{ $c->email }}">{{ $c->email }}</a></td>
                    <td>{{ $c->subject ?? '—' }}</td>
                    <td>
                        <div class="msg-preview" onclick="showMsg({{ $c->id }})" style="cursor:pointer;">{{ $c->message }}</div>
                        <div class="msg-full" id="msg-{{ $c->id }}" onclick="this.classList.remove('active')">
                            <div class="msg-modal" onclick="event.stopPropagation()">
                                <h3>{{ $c->subject ?? 'Message' }}</h3>
                                <div class="msg-meta">From: {{ $c->name }} ({{ $c->email }}) — {{ $c->created_at ? $c->created_at->format('M d, Y H:i') : '' }}</div>
                                <div class="msg-body">{{ $c->message }}</div>
                                <button class="btn-close" onclick="this.closest('.msg-full').classList.remove('active')">Close</button>
                            </div>
                        </div>
                    </td>
                    <td>{{ $c->created_at ? $c->created_at->format('M d, Y') : '' }}</td>
                    <td>
                        <form method="POST" action="/admin/contacts/{{ $c->id }}" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn-toggle">{{ $c->status === 'new' ? 'Mark Read' : 'Mark New' }}</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;color:#999;padding:2rem;">No messages yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($contacts->hasPages())
    <div style="margin-top:1rem;">{{ $contacts->links() }}</div>
    @endif

    <div class="back-link">
        <a href="/admin">&larr; Back to Dashboard</a>
    </div>
</div>

<script>
function showMsg(id) { document.getElementById('msg-' + id).classList.add('active'); }
</script>
@endsection
