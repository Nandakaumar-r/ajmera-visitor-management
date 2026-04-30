@if ($paginator->hasPages())
    <nav>
        <ul style="display: inline-flex; list-style: none; padding-left: 0; justify-content: center; align-items: center; gap: 6px;">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li>
                    <span style="padding: 8px 16px; background-color: #e9ecef; color: #6c757d; border-radius: 6px; cursor: not-allowed;">« Previous</span>
                </li>
            @else
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}" style="padding: 8px 16px; background-color: #007bff; color: white; border-radius: 6px; text-decoration: none;">« Previous</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li><span style="padding: 8px 16px; color: #6c757d;">{{ $element }}</span></li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li>
                                <span style="padding: 8px 16px; background-color: #198754; color: white; border-radius: 6px;">{{ $page }}</span>
                            </li>
                        @else
                            <li>
                                <a href="{{ $url }}" style="padding: 8px 16px; background-color: #f8f9fa; border: 1px solid #ced4da; color: #212529; border-radius: 6px; text-decoration: none;">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}" style="padding: 8px 16px; background-color: #007bff; color: white; border-radius: 6px; text-decoration: none;">Next »</a>
                </li>
            @else
                <li>
                    <span style="padding: 8px 16px; background-color: #e9ecef; color: #6c757d; border-radius: 6px; cursor: not-allowed;">Next »</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
