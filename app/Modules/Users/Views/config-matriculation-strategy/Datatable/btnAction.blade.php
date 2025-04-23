@if($item->status == 0)
<a href="{{ route('numSelectedActive', $item->id) }}" class="btn btn-success" id="active"> Active</a>
@endif
