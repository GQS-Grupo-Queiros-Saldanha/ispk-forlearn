<a href="{{ route('show.scholarship', $item->id) }}" class="btn btn-info btn-sm ">
    <i class="fas fa-eye"></i>
 </a>
<a href="{{ route('edit.scholarship', $item->id) }}" class="btn btn-warning btn-sm">
     <i class="fas fa-edit"></i>
</a>
<a href="{{ route('delete.scholarship', $item->id) }}" class="btn btn-danger btn-sm" onclick="return confirm('Deseja eliminar entidade?')">
    <i class="fas fa-trash"></i>
 </a>