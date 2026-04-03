@if($totalPages > 1)
<div class="admin-pagination">
    <span class="pagination-info">Page {{ (int)$currentPage }} of {{ (int)$totalPages }}</span>
    <div class="pagination-links">
        @if($currentPage > 1)
            <a href="{{ $baseUrl }}?page={{ $currentPage - 1 }}" class="pagination-btn pagination-prev" aria-label="Previous page">&laquo; Prev</a>
        @else
            <span class="pagination-btn pagination-prev disabled" aria-disabled="true">&laquo; Prev</span>
        @endif

        @php
        $start = max(1, $currentPage - 2);
        $end = min($totalPages, $currentPage + 2);
        @endphp

        @if($start > 1)
            <a href="{{ $baseUrl }}?page=1" class="pagination-btn">1</a>
            @if($start > 2)
                <span class="pagination-ellipsis">&hellip;</span>
            @endif
        @endif

        @for($i = $start; $i <= $end; $i++)
            @if($i === (int)$currentPage)
                <span class="pagination-btn pagination-current">{{ $i }}</span>
            @else
                <a href="{{ $baseUrl }}?page={{ $i }}" class="pagination-btn">{{ $i }}</a>
            @endif
        @endfor

        @if($end < $totalPages)
            @if($end < $totalPages - 1)
                <span class="pagination-ellipsis">&hellip;</span>
            @endif
            <a href="{{ $baseUrl }}?page={{ $totalPages }}" class="pagination-btn">{{ $totalPages }}</a>
        @endif

        @if($currentPage < $totalPages)
            <a href="{{ $baseUrl }}?page={{ $currentPage + 1 }}" class="pagination-btn pagination-next" aria-label="Next page">Next &raquo;</a>
        @else
            <span class="pagination-btn pagination-next disabled" aria-disabled="true">Next &raquo;</span>
        @endif
    </div>
</div>

<style>
    .admin-pagination { display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.5rem; flex-wrap: wrap; gap: 0.8rem; }
    .pagination-info { color: #6c757d; font-size: 0.9rem; }
    .pagination-links { display: flex; align-items: center; gap: 0.3rem; flex-wrap: wrap; }
    .pagination-btn { display: inline-flex; align-items: center; justify-content: center; min-width: 36px; height: 36px; padding: 0 0.6rem; border: 1px solid #dee2e6; border-radius: 6px; background: #fff; color: #333; font-size: 0.9rem; text-decoration: none; transition: all 0.2s; cursor: pointer; }
    .pagination-btn:hover:not(.disabled):not(.pagination-current) { background: #FF6B35; color: #fff; border-color: #FF6B35; }
    .pagination-current { background: #FF6B35; color: #fff; border-color: #FF6B35; font-weight: 600; }
    .pagination-btn.disabled { color: #adb5bd; cursor: not-allowed; background: #f8f9fa; }
    .pagination-ellipsis { padding: 0 0.4rem; color: #6c757d; }
    @media (max-width: 600px) { .admin-pagination { flex-direction: column; align-items: center; } }
</style>
@endif
