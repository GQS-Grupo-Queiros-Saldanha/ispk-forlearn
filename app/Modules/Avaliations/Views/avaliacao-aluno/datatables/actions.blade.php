<button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#insertMetrica">
    <i class="fas fa-plus"></i>
</button>


<a href="{{ route('tipo_avaliacao.show', 1) }}" class="btn btn-info btn-sm">
    <i class="far fa-eye"></i>
</a>

{{--@if(auth()->user()->hasAnyRole(['superadmin', 'staff_forlearn']))--}}
    <a href="{{ route('tipo_avaliacao.edit', 1) }}" class="btn btn-warning btn-sm">
        <i class="fas fa-edit"></i>
    </a>
{{--@endif--}}

<button class="btn btn-sm btn-danger" data-toggle="modal" data-type="delete" data-target="#modal_confirm"
        data-action="{{ json_encode(['route' => ['tipo_avaliacao.destroy', 1], 'method' => 'delete', 'class' => 'd-inline']) }}"
        type="submit">
    <i class="fas fa-trash-alt"></i>
</button>

 {{--       <div class="modal fade" id="insertMetrica" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Associar Métrica</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {!! Form::open(['route' => ['avaliacao.store']]) !!}
                        <div class="form-group col">
                            <label>Nome da Métrica</label>
                            {{ Form::text('nome1', null, ['required', 'placeholder' => '']) }}
                        </div>
                        <div class="form-group col">
                            <label>Percentagem</label>
                            {{ Form::number('nome2', null, ['required', 'placeholder' => '', 'min' => 0, 'max' => 100]) }}
                        </div>
                        <div class="form-group col">
                                <label>Tipo de Métrica</label>
                                <select name="tipo_avaliacao1" id="ta1" class="form-control">
                                    <option value=""></option>
                                    
                                </select>
                            </div>
                    {!! Form::close() !!}
                        <hr>
                    {!! Form::open(['route' => ['avaliacao.store']]) !!}
                            <div class="form-group col">
                                <label>Ou Selecione uma Métrica</label>
                                <select name="tipo_avaliacao2" id="ta2" class="form-control">
                                    <option value=""></option>
                                    
                                </select>
                            </div>
                    {!! Form::close() !!}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary">Associar Métrica</button>
                </div>
                </div>
            </div>
        </div>

@section('scripts')
    @parent
    <script>    
       $(document).ready(function(){
            console.log("Ola");
  
        });
    </script>
@endsection --}}
         