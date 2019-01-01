@if ($paginator->hasPages())
    <nav class="pagination is-centered is-small">
        <ul class="pagination-list">
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li>
                        <span class="pagination-ellipsis">{{ $element }}</span>
                    </li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li>
                            @if ($page == $paginator->currentPage())
                                <a class="pagination-link is-current" aria-current="page">{{ $page }}</a>
                            @else
                                <a class="pagination-link" href="{{ $url }}">{{ $page }}</a>
                            @endif
                        </li>
                    @endforeach
                @endif
            @endforeach
        </ul>
    </nav>
@endif
