{{-- Pagination links for dashboard lists (set as the default in AppServiceProvider). --}}
@if ($paginator->hasPages())
  <nav class="pager" aria-label="Pages">
    @if ($paginator->onFirstPage())
      <span class="pager__link is-disabled" aria-disabled="true">&larr; Previous</span>
    @else
      <a class="pager__link" href="{{ $paginator->previousPageUrl() }}" rel="prev">&larr; Previous</a>
    @endif

    <span class="pager__info mono">
      Page {{ $paginator->currentPage() }}@if (method_exists($paginator, 'lastPage')) of {{ $paginator->lastPage() }}@endif
    </span>

    @if ($paginator->hasMorePages())
      <a class="pager__link" href="{{ $paginator->nextPageUrl() }}" rel="next">Next &rarr;</a>
    @else
      <span class="pager__link is-disabled" aria-disabled="true">Next &rarr;</span>
    @endif
  </nav>
@endif
