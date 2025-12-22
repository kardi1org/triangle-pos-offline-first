@can('edit_mejas')
    <a href="{{ route('mejas.edit', $data->id) }}" class="btn btn-info btn-sm">
        <i class="bi bi-pencil"></i>
    </a>
@endcan
@can('show_mejas')
    <a href="{{ route('mejas.show', $data->id) }}" class="btn btn-primary btn-sm">
        <i class="bi bi-eye"></i>
    </a>
@endcan
@can('delete_mejas')
    <button id="delete" class="btn btn-danger btn-sm" onclick="
        event.preventDefault();
        if (confirm('Are you sure? It will delete the data permanently!')) {
        document.getElementById('destroy{{ $data->id }}').submit()
        }
        ">
        <i class="bi bi-trash"></i>
        <form id="destroy{{ $data->id }}" class="d-none" action="{{ route('mejas.destroy', $data->id) }}" method="POST">
            @csrf
            @method('delete')
        </form>
    </button>
@endcan
