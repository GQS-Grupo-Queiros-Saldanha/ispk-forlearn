<button type="button" class="btn btn-danger btn-sm del" value="{{$item->id}}" title="elimina está equivalência de disciplina">
    @icon('fas fa-times')
</button>

<script>
      $('.del').click(function(e) {
        $("#id_tb_equivalence").val($(this).val());
        $("#exampleModal1").modal('show');
    });
</script>