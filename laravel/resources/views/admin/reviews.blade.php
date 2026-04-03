@extends('layouts.main')

@section('title', 'Manage Reviews - Touristik')

@push('styles')
<style>
    .admin-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem; }
    .admin-header h1 { font-size: 1.8rem; color: var(--text-heading, #1a1a2e); margin: 0; }

    /* Filter Tabs */
    .filter-tabs { display: flex; gap: 0.3rem; background: #fff; padding: 0.5rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); margin-bottom: 1.5rem; flex-wrap: wrap; }
    .filter-tab { display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.55rem 1.1rem; border-radius: 6px; text-decoration: none; color: #6c757d; font-size: 0.9rem; font-weight: 600; transition: all 0.2s; border: none; background: none; cursor: pointer; }
    .filter-tab:hover { background: #f8f9fa; color: #333; }
    .filter-tab.active { background: #FF6B35; color: #fff; }
    .filter-tab .tab-count { display: inline-flex; align-items: center; justify-content: center; min-width: 22px; height: 22px; background: rgba(0,0,0,0.1); border-radius: 11px; font-size: 0.75rem; padding: 0 0.4rem; }
    .filter-tab.active .tab-count { background: rgba(255,255,255,0.25); }

    /* Review Cards */
    .reviews-list { display: flex; flex-direction: column; gap: 1rem; }
    .review-card { background: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); padding: 1.5rem; }
    .review-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.8rem; flex-wrap: wrap; gap: 0.5rem; }
    .review-user { font-weight: 600; color: var(--text-heading, #1a1a2e); font-size: 1rem; }
    .review-product { font-size: 0.85rem; color: #6c757d; margin-top: 0.15rem; }
    .review-meta { text-align: right; }
    .review-date { font-size: 0.82rem; color: #6c757d; }
    .badge { display: inline-block; padding: 0.2rem 0.65rem; border-radius: 4px; font-size: 0.78rem; font-weight: 600; white-space: nowrap; margin-left: 0.4rem; }
    .badge-approved, .badge-active { background: #d4edda; color: #155724; }
    .badge-pending { background: #fff3cd; color: #856404; }
    .badge-rejected { background: #f8d7da; color: #721c24; }
    .review-stars { color: #f5a623; font-size: 1.1rem; letter-spacing: 1px; margin-bottom: 0.5rem; }
    .review-title { font-weight: 600; color: #333; font-size: 0.95rem; margin-bottom: 0.3rem; }
    .review-comment { color: #555; font-size: 0.92rem; line-height: 1.55; margin-bottom: 1rem; }
    .review-actions { display: flex; gap: 0.6rem; align-items: flex-start; flex-wrap: wrap; padding-top: 1rem; border-top: 1px solid #f0f0f0; }
    .review-actions form { display: inline; }
    .btn-approve { padding: 0.4rem 1rem; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 6px; font-size: 0.85rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
    .btn-approve:hover { background: #b7dfbf; }
    .btn-reject { padding: 0.4rem 1rem; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 6px; font-size: 0.85rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
    .btn-reject:hover { background: #f1b0b7; }
    .reply-form { flex: 1; min-width: 250px; display: flex; gap: 0.5rem; }
    .reply-form textarea { flex: 1; padding: 0.45rem 0.7rem; border: 1px solid #dee2e6; border-radius: 6px; font-size: 0.85rem; font-family: inherit; resize: vertical; min-height: 36px; }
    .reply-form textarea:focus { outline: none; border-color: #FF6B35; box-shadow: 0 0 0 3px rgba(255,107,53,0.15); }
    .btn-reply { padding: 0.4rem 0.9rem; background: #FF6B35; color: #fff; border: none; border-radius: 6px; font-size: 0.85rem; font-weight: 600; cursor: pointer; transition: background 0.2s; align-self: flex-start; }
    .btn-reply:hover { background: #e55a2b; }
    .empty-state { text-align: center; padding: 3rem 1.5rem; color: #6c757d; background: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
    .empty-state .empty-icon { font-size: 3rem; margin-bottom: 0.8rem; display: block; }
    .empty-state p { font-size: 1.05rem; margin: 0; }
    @media (max-width: 600px) {
        .review-top { flex-direction: column; }
        .review-meta { text-align: left; }
        .reply-form { min-width: 100%; }
    }
</style>
@endpush

@section('content')
@php
$allCount = count($reviews ?? []);
$pendingCount = count(array_filter($reviews ?? [], fn($r) => ($r['status'] ?? '') === 'pending'));
$approvedCount = count(array_filter($reviews ?? [], fn($r) => ($r['status'] ?? '') === 'approved'));
$rejectedCount = count(array_filter($reviews ?? [], fn($r) => ($r['status'] ?? '') === 'rejected'));
$currentFilter = $filters['status'] ?? '';
@endphp

<div class="admin-header">
    <h1>{{ $title }}</h1>
</div>

<div class="filter-tabs">
    <a href="/admin/reviews" class="filter-tab {!! $currentFilter === '' ? 'active' : '' !!}">
        All <span class="tab-count">{!! $allCount !!}</span>
    </a>
    <a href="/admin/reviews?status=pending" class="filter-tab {!! $currentFilter === 'pending' ? 'active' : '' !!}">
        Pending <span class="tab-count">{!! $pendingCount !!}</span>
    </a>
    <a href="/admin/reviews?status=approved" class="filter-tab {!! $currentFilter === 'approved' ? 'active' : '' !!}">
        Approved <span class="tab-count">{!! $approvedCount !!}</span>
    </a>
    <a href="/admin/reviews?status=rejected" class="filter-tab {!! $currentFilter === 'rejected' ? 'active' : '' !!}">
        Rejected <span class="tab-count">{!! $rejectedCount !!}</span>
    </a>
</div>

@if(empty($reviews))
    <div class="empty-state">
        <span class="empty-icon">&#11088;</span>
        <p>No reviews found{!! $currentFilter ? ' with status "' . e($currentFilter) . '"' : '' !!}.</p>
    </div>
@else
    <div class="reviews-list">
        @foreach($reviews as $review)
        <div class="review-card">
            <div class="review-top">
                <div>
                    <div class="review-user">{{ ($review['user_first_name'] ?? '') . ' ' . ($review['user_last_name'] ?? '') }}</div>
                    <div class="review-product">{{ $review['product_type'] ?? '' }}: {{ $review['product_name'] ?? '-' }}</div>
                </div>
                <div class="review-meta">
                    <span class="badge badge-{{ $review['status'] ?? 'pending' }}">{{ ucfirst($review['status'] ?? 'Pending') }}</span>
                    <div class="review-date">{{ \Carbon\Carbon::parse($review['created_at'])->format('M d, Y') }}</div>
                </div>
            </div>

            <div class="review-stars">
                @php
                $rating = (int)($review['rating'] ?? 0);
                for ($i = 1; $i <= 5; $i++) {
                    echo $i <= $rating ? '&#9733;' : '&#9734;';
                }
                @endphp
                <span style="font-size:0.8rem;color:#6c757d;margin-left:0.3rem;">({!! $rating !!}/5)</span>
            </div>

            @if(!empty($review['title']))
                <div class="review-title">{{ $review['title'] }}</div>
            @endif

            <div class="review-comment">{!! nl2br(e($review['comment'] ?? '')) !!}</div>

            <div class="review-actions">
                @if(($review['status'] ?? '') !== 'approved')
                <form method="POST" action="/admin/reviews/{!! (int)$review['id'] !!}">
                    @csrf
                    <input type="hidden" name="action" value="approve">
                    <button type="submit" class="btn-approve">Approve</button>
                </form>
                @endif

                @if(($review['status'] ?? '') !== 'rejected')
                <form method="POST" action="/admin/reviews/{!! (int)$review['id'] !!}">
                    @csrf
                    <input type="hidden" name="action" value="reject">
                    <button type="submit" class="btn-reject">Reject</button>
                </form>
                @endif

                <form method="POST" action="/admin/reviews/{!! (int)$review['id'] !!}" class="reply-form">
                    @csrf
                    <input type="hidden" name="action" value="reply">
                    <textarea name="reply" placeholder="Write a reply..." required></textarea>
                    <button type="submit" class="btn-reply">Reply</button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
@endif
@endsection
