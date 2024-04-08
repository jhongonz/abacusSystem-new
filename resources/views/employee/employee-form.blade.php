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
                    <li class="nav-item"><a href="#laboral" class="nav-link" data-toggle="tab">Datos Laborales</a></li>
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
                                            <option value="{{$index}}">{{$type}}</option>
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
                                    <input type="text" class="birthdate form-control form-control-sm pickadate" id="birthdate" name="birthdate" placeholder="Fecha de nacimiento" value="@isset($employee){{$employee->birthdate()->value()}}@endisset">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <label>Estado Civil</label>
                                <select name="marital_status" id="marital_status" data-placeholder="Seleccione" class="form-control form-control-sm select" data-container-css-class="select-sm" data-fouc>
                                    <option></option>
                                    @foreach(config('configurations.marital-type') as $index => $type)
                                        {{--<option value="{{$index}}" @isset($employee) @if($employee->emp_marital_status == $index) selected @endif @endisset>{{$type}}</option>--}}
                                    @endforeach
                                </select>
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
                            {{--<div class="col-sm-6">
                                <label>Observaciones</label>
                                <textarea rows="3" cols="5" class="observations form-control form-control form-control-sm" name="observations" placeholder="Observaciones" id="observations">@isset($employee){{$employee->emp_observations}}@endisset</textarea>
                            </div>--}}
                        </div>
                        {{--<div class="row">
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
                        </div>--}}
                    </div>
                    {{--<div class="tab-pane fade show" id="laboral">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Cargo Laboral</label>
                                    <input type="text" class="position form-control form-control form-control-sm" name="position" id="position" placeholder="Posicion" value="@isset($employee){{$employee->emp_position}}@endisset">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Area Laboral</label>
                                    <input type="text" class="area form-control form-control form-control-sm" name="area" id="area" placeholder="Área" value="@isset($employee){{$employee->emp_area}}@endisset">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Nivel Profesional</label>
                                    <input type="text" class="level form-control form-control form-control-sm" name="level" id="level" placeholder="Nivel" value="@isset($employee){{$employee->emp_level}}@endisset">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Salario</label>
                                    <input type="text" class="salary form-control form-control form-control-sm" name="salary" id="salary" placeholder="Salario" value="@isset($employee){{$employee->emp_salary}}@endisset" onkeypress="return valideKeyNumber(event,true);">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Contacto de emergencia (Nombre)</label>
                                    <input type="text" class="name_emergency form-control form-control form-control-sm" name="name_emergency" id="name_emergency" placeholder="Contacto de emergencia" value="@isset($employee){{$employee->emp_contact_emergency_name}}@endisset">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Contacto de emergencia</label>
                                    <input type="text" class="phone_emergency form-control form-control form-control-sm" name="phone_emergency" id="phone_emergency" placeholder="Contacto de emergencia" value="@isset($employee){{$employee->emp_contact_emergency_phone}}@endisset" onkeypress="return valideKeyNumber(event);">
                                </div>
                            </div>
                        </div>
                    </div>--}}
                    {{--<div class="tab-pane fade show" id="login">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Login</label>
                                    <input type="text" class="login form-control form-control-sm" name="username" id="username" placeholder="Username" value="@isset($employee){{$employee->user->user_login}}@endisset">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group profile">
                                    <label>Perfil</label>
                                    <select name="profileUser" id="profileUser" data-placeholder="Seleccione" class="form-control form-control-sm select" data-container-css-class="select-sm" @isset($employee) @if($employee->user->profile->pro_id == ROOT_PROFILE_ADMIN) disabled @endif @endisset data-fouc>
                                        <option></option>
                                        @foreach($profiles as $profile)
                                            <option value="{{$profile->pro_id}}" @isset($employee) @if($employee->user->profile->pro_id == $profile->pro_id) selected @endif @endisset>{{$profile->pro_name}}</option>
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
                    </div>--}}
                </div>
            </form>
        </div>
    </div>
@stop

@section('javascript')
@parent
<script type="text/javascript">
    var target = "{!! getTargetForm('employee') !!}";

    $('.form-control-uniform').uniform({
        fileButtonClass: 'action btn bg-blue',
        fileButtonHtml: 'Elegir archivo',
        fileDefaultHtml: 'No hay archivo seleccionado'
    });

    $('#photo').on('change',function(e){
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

    $('.save-data').click(function(e){
        e.preventDefault();

        axios.post("{{ url('employee/save') }}",{
            idEmployee: "{{ $idEmployee }}",
            idUser: "{{ $idUser }}",
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

            position: $('#position').val(),
            area: $('#area').val(),
            level: $('#level').val(),
            salary: $('#salary').val(),
            name_emergency: $('#name_emergency').val(),
            phone_emergency: $('#phone_emergency').val(),

            marital_status: $('#marital_status').val(),
            children: $('#children').val()
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

    $('a[data-toggle = "tab"]').on('shown.bs.tab', function (e) {
        var _target = $(e.target).attr("href");

        if (_target != target) {
            axios.post("{{ url('setting/set-target-form') }}",{
                target: _target,
                url: 'employee',
            });
        }
    });

    $(document).ready(function(){
        if (target) {
            $('.nav-tabs a[href="'+ target +'"]').tab('show');
        } else {
            $('.nav-tabs a[href="#personal"]').tab('show');
        }

        $(".return-site").click(function(e){
            e.preventDefault();

            axios.get("{{ url('employee') }}").then(function (response){
                var data = response.data;
                $("#content-body").html(data.html);
                window.history.pushState("data","Title","{{ url('employee') }}");
            });
        });
    });
</script>
@stop
