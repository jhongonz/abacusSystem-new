<div class="modal fade module_form" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header p-2 alpha-grey">
                <h5 class="modal-title"><i class="fas fa-external-link-alt mr-2"></i> &nbsp;Registro de Modulo</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-2">
                <form>
                    <ul class="nav nav-tabs nav-tabs-bottom">
                        <li class="nav-item"><a href="#data-form" class="nav-link active" data-toggle="tab">Ficha Principal</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="data-form">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="name">Nombre</label>
                                        <input type="text" class="form-control form-control-sm name" name="name" id="name" placeholder="Nombre" value="@isset($module){{$module->name()->value()}}@endisset">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="route">Route</label>
                                        <input type="text" class="form-control form-control-sm route" name="route" id="route" placeholder="Route" value="@isset($module){{$module->route()->value()}}@endisset">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="icon">Icono</label>
                                        <input type="text" class="form-control form-control-sm icon" name="icon" id="icon" placeholder="icon" value="@isset($module){{$module->icon()->value()}}@endisset">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="key">Key</label>
                                        <select name="key" id="key" data-placeholder="Seleccione key" class="key form-control form-control-sm select" data-container-css-class="select-sm" data-fouc>
                                            <option value=""></option>
                                            @foreach($menuKeys as $key => $item)
                                            <option value="{{$key}}" @isset($module) @if($module->menuKey()->value() === $key) selected @endif @endisset>{{$key}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer p-2 alpha-grey">
                <button type="button" class="close-form btn btn-outline alpha-primary text-primary">Cerrar</button>
                <button type="button" class="save-data btn btn-primary btn-labeled btn-labeled-left btn-sm"><b><i class="fas fa-check"></i></b> Guardar</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
$('.form-check-input-styled').uniform({
    wrapperClass: 'border-primary text-primary'
});

$('.select').select2({
    minimumResultsForSearch: Infinity
});

$('.close-form').click(function(e){
    e.preventDefault();
    $('.module_form').modal('hide');
});

$('.save-data').click(function(e){
    e.preventDefault();

    axios.post("{{ route('panel.module.store') }}",{
        'id': '{{ $moduleId }}',
        'name': $('#name').val(),
        'route': $('#route').val(),
        'key': $('#key').val(),
        'icon': $('#icon').val(),
	})
    .then(function (response){
        $('.module_form').modal('hide');
        $('#content-data').DataTable().ajax.reload(null, false);

        toast.fire({
            text: 'Registro guardado',
            type: 'success'
        });
    })
    .catch(function (error){
        var objectSelects = [];
        var errors = error.response.data.errors;

        $.each(errors, function(index, element) {
            if (objectSelects.includes(index)) {
                $('.' + index).addClass('has-error');
            } else {
                $('.' + index).addClass('border-danger');
            }
        });

        var msg = errors.response.data.msg;
        toast.fire({
            text: msg ?? 'Faltan datos necesarios',
            type: 'error'
        });
    });
});

$(document).ready(function(){
    $('.module_form').modal('show');
});
</script>
