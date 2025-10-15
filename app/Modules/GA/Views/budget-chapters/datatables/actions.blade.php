@php
$contador = 0;
@endphp


@foreach ($budget_articles as $artigos)
    @if ($item->id == $artigos->chapter_id)
        @php
            $contador++;
        @endphp
    @endif
@endforeach


@php
    $last=0;
@endphp

@foreach ($article_last as $artigo)
    @if ($artigo->chapter_id == $item->id)
        @php
            $last=$artigo->code;
            
        @endphp
        @break
    @endif
@endforeach



@if ($contador>0)
    <a href="{{ route('budget_articles.budget', $item->id) }}" class="btn btn-info btn-sm">
    @icon('far fa-eye')
</a>
@endif

<a href="{{ route('budget_chapter.edit', $item->id) }}" class="btn btn-warning btn-sm">
    @icon('fas fa-edit')
</a>

<button class='btn btn-sm btn-danger delete_budget' onclick="pegar(this)" data="{{ $item->id }}" data-toggle="modal"
    data-target="#Modalconfirmar" type="submit">
    @icon('fas fa-trash-alt')
</button> 
<a href="#" data-toggle="modal" data-target="#modal-chapter" onclick="novo(this)"
    data="{{ $item->id }},{{ $item->name }},{{ $last }}" class="btn btn-success btn-sm">
    @icon('fas fa-plus')
</a>
