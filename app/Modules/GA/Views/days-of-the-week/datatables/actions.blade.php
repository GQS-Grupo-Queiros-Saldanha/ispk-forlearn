<a href="{{ route('days-of-the-week.show', $item->id) }}" class="btn btn-info btn-sm">
    @icon('far fa-eye')
</a>

<a href="{{ route('days-of-the-week.edit', $item->id) }}" class="btn btn-warning btn-sm">
    @icon('fas fa-edit')
</a>

@if(!$item->is_start_of_week)
    <a href="{{ route('days-of-the-week.start_of_week', $item->id) }}" class="btn btn-light btn-sm">
        <i class="fas fa-flag-checkered"></i>
    </a>
@endif
