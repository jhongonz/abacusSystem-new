@extends($layout)

@section('header')
    <!-- Page header -->
    <div class="page-header page-header-light">
        <div class="breadcrumb-line breadcrumb-line-light header-elements-sm-inline">
            <div class="d-flex">
                <div class="breadcrumb mr-4 text-blue-800">
                    <a href="#" class="breadcrumb-item return-site text-primary"><i class="fas fa-caret-square-left mr-1"></i> Regresar</a>
                    <span class="breadcrumb-item active">Institution</span>
                    <span class="breadcrumb-item active">Ficha</span>
                </div>

                <div class="breadcrumb">
                    <button type="button" class="save-data btn btn-outline alpha-orange text-orange-600"><i class="fas fa-save mr-2"></i>Guardar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- /page header -->
@stop

@section('content')
    <div class="card m-0 border-0">
        <div class="card-body">
            <form>
                <input type="hidden" name="institutionId" id="institutionId" value="{{$institutionId}}">

                <ul class="nav nav-tabs nav-tabs-bottom">
                    <li class="nav-item"><a href="#institution" class="nav-link" data-toggle="tab">Datos Generales</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show" id="institution">
                        <div class="row">
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label>Código Institucional</label>
                                    <input type="text" class=" identifier form-control form-control form-control-sm" name="code" id="code" placeholder="Código" value="@isset($institution){{$institution->code()->value()}}@endisset">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Nombre de Institución</label>
                                    <input type="text" class="name form-control form-control form-control-sm" name="name" id="name" placeholder="Nombre" value="@isset($institution){{$institution->name()->value()}}@endisset">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Nombre corto</label>
                                    <input type="text" class="name form-control form-control form-control-sm" name="shortname" id="shortname" placeholder="Nombre corto" value="@isset($institution){{$institution->shortname()->value()}}@endisset">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <label>Dirección</label>
                                <input type="text" class="phone form-control form-control form-control-sm" name="address" id="address" placeholder="Dirección" value="@isset($institution){{$institution->address()->value()}}@endisset">
                            </div>
                            <div class="col-sm-3">
                                <label>Telefono</label>
                                <input type="text" class="phone form-control form-control form-control-sm" name="phone" id="phone" placeholder="Telefono" onkeypress="return valideKeyNumber(event)" maxlength="15" value="@isset($institution){{$institution->phone()->value()}}@endisset">
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>E-mail</label>
                                    <input type="text" class="email form-control form-control form-control-sm" name="email" id="email" placeholder="E-mail" value="@isset($institution){{$institution->email()->value()}}@endisset">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <label>Observaciones</label>
                                <textarea rows="3" cols="5" class="observations form-control form-control form-control-sm" name="observations" placeholder="Observaciones" id="observations">@isset($institution){{$institution->observations()->value()}}@endisset</textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 mt-4">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="card">
                                            <div class="card-body text-center p-0 m-0">
                                                <div class="card-img-actions d-inline-block m-2">
                                                    <a data-fancybox="gallery" href="@isset($image){{$image}}@endisset"><img class="showPhoto img-fluid rounded-circle" src="@isset($image){{$image}}@endisset" width="100" height="100" alt=""></a>
                                                </div>
                                                <span class="d-block">Foto perfil</span>

                                                <div class="col-sm-12 p-0 m-0">
                                                    <input type="hidden" name="token" id="token">
                                                    <input type="file" class="form-control-uniform" id="photo" name="photo" data-fouc>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop

@section('javascript')
@parent
<script type="text/javascript">
$('.form-control-uniform').uniform({
    fileButtonClass: 'action btn bg-blue',
    fileButtonHtml: 'Elegir archivo',
    fileDefaultHtml: 'No hay archivo seleccionado'
});

$(".pickadate").datepicker({
    changeMonth: true,
    changeYear: true,
    minDate: '-70Y',
    maxDate: '-15Y'
});

$('.form-check-input-styled').uniform({
    wrapperClass: 'border-primary text-primary'
});

$('.select').select2({
    minimumResultsForSearch: Infinity
});

$('#photo').on('change',function(e) {
    e.preventDefault();
    _data = new FormData();
    _data.append('file', $(this)[0].files[0]);

    axios.post("{{ route('panel.institution.set-logo') }}",_data)
    .then(function (response){
        $('#token').val(response.data.token);
        $('.showPhoto').removeAttr('src');
        $('.showPhoto').attr('src',response.data.url);
    });
});

$('.save-data').click(function(e){
    e.preventDefault();

    axios.post("{{ route('panel.institution.store') }}",{
        institutionId: $('#institutionId').val(),
        code: $('#code').val(),
        name: $('#name').val(),
        shortname: $('#shortname').val(),
        address: $('#address').val(),
        email: $('#email').val(),
        phone: $('#phone').val(),
        observations: $('#observations').val(),
        token: $('#token').val(),
    })
    .then(function (response){

        $('#institutionId').val(response.data.institutionId);

        toast.fire({
            text: 'Registro guardado',
            type: 'success'
        });
    })
    .catch(function ($response) {
        var data = response.data;

        var objectSelects = ['typeDocument','profileUser'];
        var errors = data.errors;

        $.each(errors, function(index, element) {
            if (objectSelects.includes(index)) {
                $('.' + index).addClass('has-error');
            } else {
                $('.' + index).addClass('border-danger');
            }
        });

        toast.fire({
            text: 'Error en datos ingresados',
            type: 'error'
        });
    });
});

$(document).ready(function(){
    $('.nav-tabs a[href="#institution"]').tab('show');

    $(".return-site").click(function(e){
        e.preventDefault();

        axios.get("{{ route('panel.institution.index') }}").then(function (response){
            var data = response.data;
            $("#content-body").html(data.html);
            window.history.pushState("data","Title","{{ route('panel.institution.index') }}");
        });
    });
});
</script>
@stop
