{{-- <a href="{{ route('study-plan-editions.show', $item->id) }}" class="btn btn-info btn-sm">
    @icon('far fa-eye')
</a>

<a href="{{ route('study-plan-editions.edit', $item->id) }}" class="btn btn-warning btn-sm">
    @icon('fas fa-edit')
</a>

@if (auth()->user()->hasAnyRole(['superadmin','staff_forlearn']))
    <button class="btn btn-success btn-sm" id="btnDuplicate" onclick="duplicateAction({{$item->id}})">
        <i class="fas fa-copy"></i>
    </button>
@endif

<button class='btn btn-sm btn-danger' data-toggle="modal" data-type="delete" data-target="#modal_confirm" data-action="{{ json_encode(['route' => ['study-plan-editions.destroy', $item->id], 'method' => 'delete', 'class' => 'd-inline']) }}" type="submit">
    @icon('fas fa-trash-alt')
</button> --}}

{{-- <a href="{{ route('study-plan-editions.absences', $item->id) }}" class="btn btn-light btn-sm">
    <i class="fas fa-scroll"></i>
    @lang('GA::study-plan-editions.absences')
</a> --}}

{{-- <script>
     function duplicateAction(id){
            $("#exampleModal").modal(); 
            $("#std_id").val(id)
        }
</script>  --}}


<a href="{{ route('course-curricular-year-block.change_state', $item->course_year_block_id) }}" class="btn btn-info btn-sm">
    @icon('fas fa-exchange')
    {{-- <i class="fa-duotone fa-lock-keyhole-open"></i> --}}
</a>    
