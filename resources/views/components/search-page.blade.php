<div class="d-flex justify-content-between mb-3">
    {{-- Entries --}}
<div class="d-flex align-items-center gap-2">
    <select id="entries" class="form-select form-select-sm entries-selector" style="width: 80px;">
        <option value="5" {{ request('per_page', 5) == 5 ? 'selected' : '' }}>5</option>
        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
    </select>
    <span class="ms-1 text-sm">entries</span>
</div>


    {{-- Search --}}
    <div>
        <input type="text" class="form-control form-control-sm search-input"
               placeholder="Search..."
               value="{{ request('search') }}">
    </div>
</div>
