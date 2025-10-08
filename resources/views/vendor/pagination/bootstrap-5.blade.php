@php
    function renderUrl($url2)
    {
        $url1 = url()->full();
        $url1Params = [];
        parse_str(parse_url($url1, PHP_URL_QUERY), $url1Params);
        $url2Params = [];
        parse_str(parse_url($url2, PHP_URL_QUERY), $url2Params);
        $combinedParams = array_merge($url1Params, $url2Params);
        $combinedUrl =
            parse_url($url1, PHP_URL_SCHEME) . '://' . parse_url($url1, PHP_URL_HOST) . parse_url($url1, PHP_URL_PATH);
        if ($combinedParams) {
            $combinedUrl .= '?' . http_build_query($combinedParams);
        }
        $x = explode('?', $combinedUrl);
        $url = url(request()->path()) . '?';
        foreach ($x as $key => $value) {
            if ($key > 0) {
                $url .= $value;
            }
        }
        return $url;
    }
@endphp
<nav class="d-flex justify-items-center justify-content-between "
    {{ isset($paginatorPrefix) ? 'id=' . $paginatorPrefix : '' }}>
    @if ($paginator->hasPages())
        <div class="d-flex justify-content-between flex-fill d-sm-none">
            <ul class="pagination">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link">@lang('pagination.previous')</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ renderUrl($paginator->previousPageUrl()) }}"
                            rel="prev">@lang('pagination.previous')</a>
                    </li>
                @endif

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ renderUrl($paginator->nextPageUrl()) }}"
                            rel="next">@lang('pagination.next')</a>
                    </li>
                @else
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link">@lang('pagination.next')</span>
                    </li>
                @endif
            </ul>
        </div>
    @endif

    <div class="d-none flex-sm-fill d-sm-flex align-items-sm-center justify-content-sm-between">
        <div>
            <span class="small text-muted">
                {!! __('Showing') !!}
                <span class="fw-semibold">{{ $paginator->firstItem() ?: 0 }}</span>
                {!! __('to') !!}
                <span class="fw-semibold">{{ $paginator->lastItem() ?: 0 }}</span>
                {!! __('of') !!}
                <span class="fw-semibold">{{ $paginator->total() }}</span>
                {!! __('results') !!}
            </span>
            <div class="page-list d-inline-block">
                <div class="btn-group dropup">
                    <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        {{ $paginator->perPage() }}
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="small dropdown-item @if ($paginator->perPage() == 10) active @endif"
                                href="{{ request()->fullUrlWithQuery(['limit' => 10]) }}"
                                @if ($paginator->perPage() == 10) aria-current="true" @endif>10</a></li>
                        <li><a class="small dropdown-item @if ($paginator->perPage() == 25) active @endif"
                                href="{{ request()->fullUrlWithQuery(['limit' => 25]) }}"
                                @if ($paginator->perPage() == 25) aria-current="true" @endif>25</a></li>
                        <li><a class="small dropdown-item @if ($paginator->perPage() == 50) active @endif"
                                href="{{ request()->fullUrlWithQuery(['limit' => 50]) }}"
                                @if ($paginator->perPage() == 50) aria-current="true" @endif>50</a></li>
                        <li><a class="small dropdown-item @if ($paginator->perPage() == 100) active @endif"
                                href="{{ request()->fullUrlWithQuery(['limit' => 100]) }}"
                                @if ($paginator->perPage() == 100) aria-current="true" @endif>100</a></li>
                    </ul>
                </div> rows per page
            </div>
        </div>

        @if ($paginator->hasPages())
            <div>
                <ul class="pagination">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                            <span class="page-link" aria-hidden="true">&lsaquo;</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ renderUrl($paginator->previousPageUrl()) }}" rel="prev"
                                aria-label="@lang('pagination.previous')">&lsaquo;</a>
                        </li>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <li class="page-item disabled" aria-disabled="true"><span
                                    class="page-link">{{ $element }}</span></li>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <li class="page-item active" aria-current="page"><span
                                            class="page-link">{{ $page }}</span></li>
                                @else
                                    <li class="page-item"><a class="page-link"
                                            href="{{ renderUrl($url) }}">{{ $page }}</a></li>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ renderUrl($paginator->nextPageUrl()) }}" rel="next"
                                aria-label="@lang('pagination.next')">&rsaquo;</a>
                        </li>
                    @else
                        <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                            <span class="page-link" aria-hidden="true">&rsaquo;</span>
                        </li>
                    @endif
                </ul>
            </div>
        @endif
    </div>
</nav>
