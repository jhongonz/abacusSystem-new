@extends($layout)

@section('header')
    <!-- Page header -->
    <div class="page-header page-header-light">
        <div class="breadcrumb-line breadcrumb-line-light header-elements-sm-inline">
            <div class="d-flex">
                <div class="breadcrumb mr-4 text-blue-800">
                    <a href="#" class="breadcrumb-item return-site text-primary"><i class="fas fa-caret-square-left mr-1"></i> Regresar</a>
                    <span class="breadcrumb-item active">Empleados</span>
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
                <ul class="nav nav-tabs nav-tabs-bottom">
                    <li class="nav-item"><a href="#personal" class="nav-link" data-toggle="tab">Datos Personales</a></li>
                    <li class="nav-item"><a href="#login" class="nav-link" data-toggle="tab">Datos de Acceso</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show" id="personal">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group typeDocument">
                                    <label>Tipo de Documento</label>
                                    <select name="typeDocument" id="typeDocument" data-placeholder="Seleccione" class="form-control form-control-sm select" data-container-css-class="select-sm" data-fouc>
                                        <option></option>
                                        @foreach(config('configurations.document-type') as $index => $type)
                                            <option value="{{$index}}" @isset($employee) @if($employee->identificationType()->value() == $index) selected @endif @endisset>{{$type}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Nro Identificacion</label>
                                    <input type="text" class=" identifier form-control form-control form-control-sm" name="identifier" id="identifier" placeholder="Nro de Identidad" value="@isset($employee){{$employee->identification()->value()}}@endisset">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Nombres</label>
                                    <input type="text" class="name form-control form-control form-control-sm" name="name" id="name" placeholder="Nombres" value="@isset($employee){{$employee->name()->value()}}@endisset">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Apellidos</label>
                                    <input type="text" class="lastname form-control form-control form-control-sm" name="lastname" id="lastname" placeholder="Apellidos" value="@isset($employee){{$employee->lastname()->value()}}@endisset">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label>Fecha de nacimiento</label>
                                    <input type="text" class="birthdate form-control form-control-sm pickadate" id="birthdate" name="birthdate" placeholder="Fecha de nacimiento" value="@isset($employee){{$employee->birthdate()->toString()}}@endisset">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <label>Telefono</label>
                                <input type="text" class="phone form-control form-control form-control-sm" name="phone" id="phone" placeholder="Telefono" onkeypress="return valideKeyNumber(event)" maxlength="15" value="@isset($employee){{$employee->phone()->value()}}@endisset">
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>E-mail</label>
                                    <input type="text" class="email form-control form-control form-control-sm" name="email" id="email" placeholder="E-mail" value="@isset($employee){{$employee->email()->value()}}@endisset">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <label>Dirección</label>
                                <textarea rows="3" cols="5" class="address form-control form-control form-control-sm" name="address" placeholder="Dirección" id="address">@isset($employee){{$employee->address()->value()}}@endisset</textarea>
                            </div>
                            <div class="col-sm-6">
                                <label>Observaciones</label>
                                <textarea rows="3" cols="5" class="observations form-control form-control form-control-sm" name="observations" placeholder="Observaciones" id="observations">@isset($employee){{$employee->observations()->value()}}@endisset</textarea>
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
                    <div class="tab-pane fade show" id="login">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Login</label>
                                    <input type="text" class="login form-control form-control-sm" name="username" id="username" placeholder="Username" value="@isset($user){{$user->login()->value()}}@endisset">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group profile">
                                    <label>Perfil</label>
                                    <select name="profileUser" id="profileUser" data-placeholder="Seleccione" class="form-control form-control-sm select" data-container-css-class="select-sm" @isset($user) @if($user->profileId()->value() == 1) disabled @endif @endisset data-fouc>
                                        <option></option>
                                        @foreach($profiles as $profile)
                                            <option value="{{$profile->id()->value()}}" @isset($user) @if($user->profileId()->value() == $profile->id()->value()) selected @endif @endisset>{{$profile->name()->value()}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Password</label>
                                    <input type="password" class="password form-control form-control-sm" placeholder="Password" name="password" id="password" value="">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Repetir</label>
                                    <input type="password" class="password form-control form-control-sm" placeholder="Repetir" name="repeat" id="repeat" value="">
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

    axios.post("{{ url('employee/account-image') }}",_data)
    .then(function (response){
        var data = response.data;

        $('#token').val(data.token);
        $('.showPhoto').removeAttr('src');
        $('.showPhoto').attr('src',data.url);
    });
});

$('.save-data').click(function(e){
    e.preventDefault();

    axios.post("{{ url('employee/save') }}",{
        employeeId: "{{ $employeeId }}",
        userId: "{{ $userId }}",
        identifier: $('#identifier').val(),
        typeDocument: $('#typeDocument').val(),
        name: $('#name').val(),
        lastname: $('#lastname').val(),
        email: $('#email').val(),
        login: $('#username').val(),
        phone: $('#phone').val(),
        address: $('#address').val(),
        observations: $('#observations').val(),
        profile: $('#profileUser').val(),
        birthdate: $('#birthdate').val(),
        password: $('#password').val(),
        password_confirmation: $('#repeat').val(),
        token: $('#token').val(),
    })
    .then(function (response){
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
    $('.nav-tabs a[href="#personal"]').tab('show');

    $(".return-site").click(function(e){
        e.preventDefault();

        axios.get("{{ route('panel.employee.index') }}").then(function (response){
            var data = response.data;
            $("#content-body").html(data.html);
            window.history.pushState("data","Title","{{ url('employee') }}");
        });
    });
});
</script>
@stop
