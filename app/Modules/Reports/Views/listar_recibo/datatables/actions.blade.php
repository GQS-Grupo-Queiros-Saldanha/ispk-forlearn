<a href="{{ asset($item->path) }}" target="_blank" class="btn btn-info btn-sm">
    <i class="fas fa-receipt"></i>
</a>

@if(auth()->user()->can('manage-requests-others'))

@endif
