@php use Core\SharedContext\Model\ValueObjectStatus; @endphp

<div class="btn-group">
    <button type="button" class="btn btn-sm btn-icon rounded-round text-grey-800" data-toggle="dropdown">
        <i class="fas fa-ellipsis-h fa-fw"></i>
    </button>

    <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right p-0 ml-4">
        <a class="editElement dropdown-item" data-id={{$item['id']}}><i class="fas fa-edit text-primary-600 fa-fw"></i>Editar</a>

        @if($item['state'] === ValueObjectStatus::STATE_ACTIVE)
            <a class="changeState dropdown-item" data-id={{$item['id']}}><i class="fas fa-ban text-danger-600 fa-fw"></i>Suspender</a>
        @elseif(in_array($item['state'], [ValueObjectStatus::STATE_INACTIVE, ValueObjectStatus::STATE_NEW]))
            <a class="changeState dropdown-item" data-id={{$item['id']}}><i class="fas fa-check text-green-600 fa-fw"></i>Activar</a>
        @endif

        <div class="dropdown-divider m-0 p-0"></div>
        <a class="deleteElement dropdown-item" data-id={{$item['id']}}><i class="fas fa-eraser text-danger-600 fa-fw"></i>Eliminar</a>
    </div>
</div>
