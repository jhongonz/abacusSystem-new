<div class="modal fade campus_form" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header p-2 alpha-grey">
                <h5 class="modal-title"><i class="fas fa-external-link-alt mr-2"></i> &nbsp;Registro de Campus</h5>
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
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="name">Nombre</label>
                                        <input type="text" class="form-control form-control-sm name" name="name" id="name" placeholder="Nombre" value="@isset($campus){{$campus->name()->value()}}@endisset">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="route">Phone</label>
                                        <input type="text" class="form-control form-control-sm phone" name="phone" id="phone" placeholder="Telefono" value="@isset($campus){{$campus->phone()->value()}}@endisset">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="route">E-mail</label>
                                        <input type="text" class="form-control form-control-sm email" name="email" id="email" placeholder="E-mail" value="@isset($campus){{$campus->email()->value()}}@endisset">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="route">Dirección</label>
                                        <input type="text" class="form-control form-control-sm address" name="address" id="address" placeholder="Dirección" value="@isset($campus){{$campus->address()->value()}}@endisset">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="observations">Observaciones</label>
                                        <textarea rows="3" cols="5" class="observations form-control form-control form-control-sm" name="observations" placeholder="Observaciones" id="observations">@isset($campus){{$campus->observations()->value()}}@endisset</textarea>
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
    $('.campus_form').modal('hide');
});

$('.save-data').click(function(e){
    e.preventDefault();

    axios.post("{{ route('panel.campus.store') }}",{
        'campusId': '{{ $campusId }}',
        'name': $('#name').val(),
        'address': $('#address').val(),
        'phone': $('#phone').val(),
        'email': $('#email').val(),
        'observations': $('#observations').val(),
	})
    .then(function (response){
        $('.campus_form').modal('hide');
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
    $('.campus_form').modal('show');
});
</script>
