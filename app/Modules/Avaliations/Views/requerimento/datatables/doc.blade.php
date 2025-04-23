     
@foreach ($requerimento as $item)
    @if(($state->code_dev == 25) && ($state->codigo_estudante == $item->user_id) && ($state->art_id==$item->article_id))
      @if ($state->status == 'total')
       <a href="{{ route('academic-path-imported.percurso', $item->user_id) }}?requerimento_id= {{$item->id}}" target="_blank" class="fas fa-file-pdf"
       id="">
       </a>
      
      @endif
   
    @elseif(($state->code_dev == 28) && ($state->codigo_estudante == $item->user_id) && ($state->art_id==$item->article_id))
      @if ($state->status == 'total')
             @php 
               
                 $course = DB::table('user_courses')->where('users_id', $item->user_id)->select('courses_id')->pluck('courses_id')->first();
      
                @endphp
       <a href="{{ route('study-plans.pdf', $course) }}" target="_blank" class="fas fa-file-pdf"
       id="">
       </a>
       
      @endif
    @elseif (($state->codigo_estudante == $item->user_id) && ($state->art_id==$item->article_id) && ($state->code_dev != 25) && ($state->code_dev != 28) && ($state->code_dev != 35))
        @if ($state->status == 'total')
                

                {!! Form::open(['route' => ['document.generate-documentation'], 'method' => 'post', 'target' => '_blank']) !!}

                <input type="number" id="students" name="students" value="{{$item->user_id}}" class="d-none" />
                <input type="number" id="type_document" name="type_document" value="{{$item->codigo_documento}}" class="d-none" />
                <input type="number" id="student_year" name="student_year" value="{{$item->ano}}" class="d-none" />
                <input type="text" id="efeito_type" name="efeito_type" value="{{$item->efeito}}" class="d-none" />
                <input type="text" id="requerimento" name="requerimento" value="{{$item->id}}" class="d-none" />
               

                <button type="submit"tabindex="0" data-bs-toggle="tooltip" data-html="true" target="_blank"
                    href="/reports/generate-declaration-note" class="btn btn-info "
                    style="    padding: 3px 6px 3px 6px;border-radius: 7px;font-size: 12px;">
                    <i class="fas fa-file-pdf"></i>
                </button>
                {!! Form::close() !!}
             

        @endif
    @endif
@endforeach