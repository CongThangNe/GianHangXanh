@if ($paginator->hasPages())
<nav class="d-flex justify-content-center align-items-center mb-4" style="margin-top: 4rem;">
    <ul class="pagination pagination-lg shadow-sm" style="gap: 8px;">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
        <li class="page-item disabled" aria-disabled="true">
            <span class="page-link" style="
                        background: #f8f9fa;
                        border: 2px solid #e9ecef;
                        color: #6c757d;
                        border-radius: 10px;
                        padding: 12px 18px;
                        font-weight: 600;
                    ">
                <i class="bi bi-chevron-left"></i> Trước
            </span>
        </li>
        @else
        <li class="page-item">
            <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" style="
                        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
                        border: none;
                        color: white;
                        border-radius: 10px;
                        padding: 12px 18px;
                        font-weight: 600;
                        transition: all 0.3s ease;
                        box-shadow: 0 4px 6px rgba(40, 167, 69, 0.2);
                    " onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 12px rgba(40, 167, 69, 0.3)'"
                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px rgba(40, 167, 69, 0.2)'">
                <i class="bi bi-chevron-left"></i> Trước
            </a>
        </li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
        {{-- "Three Dots" Separator --}}
        @if (is_string($element))
        <li class="page-item disabled" aria-disabled="true">
            <span class="page-link" style="
                            background: transparent;
                            border: none;
                            color: #28a745;
                            font-weight: bold;
                            padding: 12px 8px;
                        ">{{ $element }}</span>
        </li>
        @endif

        {{-- Array Of Links --}}
        @if (is_array($element))
        @foreach ($element as $page => $url)
        @if ($page == $paginator->currentPage())
        <li class="page-item active" aria-current="page">
            <span class="page-link" style="
                                    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
                                    border: none;
                                    color: white;
                                    border-radius: 10px;
                                    padding: 12px 18px;
                                    font-weight: 700;
                                    min-width: 50px;
                                    text-align: center;
                                    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
                                    transform: scale(1.1);
                                ">{{ $page }}</span>
        </li>
        @else
        <li class="page-item">
            <a class="page-link" href="{{ $url }}" style="
                                    background: white;
                                    border: 2px solid #e9ecef;
                                    color: #28a745;
                                    border-radius: 10px;
                                    padding: 12px 18px;
                                    font-weight: 600;
                                    min-width: 50px;
                                    text-align: center;
                                    transition: all 0.3s ease;
                                " onmouseover="this.style.background='#f0fdf4'; this.style.borderColor='#28a745'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(40, 167, 69, 0.15)'"
                onmouseout="this.style.background='white'; this.style.borderColor='#e9ecef'; this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                {{ $page }}
            </a>
        </li>
        @endif
        @endforeach
        @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
        <li class="page-item">
            <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" style="
                        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
                        border: none;
                        color: white;
                        border-radius: 10px;
                        padding: 12px 18px;
                        font-weight: 600;
                        transition: all 0.3s ease;
                        box-shadow: 0 4px 6px rgba(40, 167, 69, 0.2);
                    " onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 12px rgba(40, 167, 69, 0.3)'"
                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px rgba(40, 167, 69, 0.2)'">
                Sau <i class="bi bi-chevron-right"></i>
            </a>
        </li>
        @else
        <li class="page-item disabled" aria-disabled="true">
            <span class="page-link" style="
                        background: #f8f9fa;
                        border: 2px solid #e9ecef;
                        color: #6c757d;
                        border-radius: 10px;
                        padding: 12px 18px;
                        font-weight: 600;
                    ">
                Sau <i class="bi bi-chevron-right"></i>
            </span>
        </li>
        @endif
    </ul>
</nav>

{{-- Pagination Info --}}
<div class="text-center text-muted mb-4" style="font-size: 14px;">
    <span style="color: #28a745; font-weight: 600;">
        Hiển thị {{ $paginator->firstItem() }} - {{ $paginator->lastItem() }}
    </span>
    trong tổng số
    <span style="color: #28a745; font-weight: 600;">
        {{ $paginator->total() }}
    </span>
    sản phẩm
</div>
@endif