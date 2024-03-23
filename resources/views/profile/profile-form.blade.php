<div class="modal fade form_profile" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header p-2 alpha-grey">
                <h5 class="modal-title"><i class="fas fa-external-link-alt mr-2"></i> &nbsp;Formulario de perfiles</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-2">
                <form>
                    <div class="modal-body p-2">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input type="text" placeholder="Nombre" name="name" id="name" value="@isset($profile){{$profile->name()->value()}}@endisset" class="name form-control form-control-sm">
                                </div>
                            </div>
                            {{--<div class="col-sm-6">
                                <div class="form-group permission">
                                    <label>Permiso</label>
                                    <select name="permission" id="permission" data-placeholder="Seleccione" class="form-control form-control-sm select" data-container-css-class="select-sm" data-fouc>
                                        <option></option>
                                        @foreach(PERMISSION as $index => $item)
                                            <option value="{{$index}}" @isset($profile) @if($profile->pro_permission == $index) selected @endif @endisset>{{$item}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>--}}
                        </div>

                        <div class="form-group">
                            <label>Descripción del perfil</label>
                            <textarea rows="3" cols="5" class="description form-control form-control-sm" name="description" id="description" placeholder="Descripción">@isset($profile){{$profile->description()->value()}}@endisset</textarea>
                        </div>

                        <label class="font-weight-semibold"><i class="fas fa-cubes mr-2"></i>Módulos</label>

                        {{--@foreach($modules as $module)

                            @if (count($module->children) > 0)
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="d-block font-weight-semibold"></label>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" disabled>
                                                <label class="custom-control-label">{{ $module->name }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @foreach($module->children as $child)
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group ml-2">

                                                <div class="form-check form-check-inline">
                                                    <label class="form-check-label">
                                                        <input type="checkbox" class="form-check-input-styled" name="modules" id="modules_{{$child->id}}" value="{{$child->id}}" {{$child->selected}}> {{$child->name}}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <select name="permit" id="permit_{{$child->id}}" data-placeholder="Permiso" class="permit form-control form-control-sm select" data-container-css-class="select-sm" data-fouc>
                                                    <option></option>
                                                    @foreach(PERMISSION as $index => $item)
                                                        <option value="{{$index}}" @if($child->permission == $index) selected @endif>{{$item}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="d-block font-weight-semibold"></label>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="modules custom-control-input" name="modules" id="modules_{{$module->id}}" value="{{$module->id}}" {{$module->selected}}>
                                                <label class="custom-control-label" for="modules_{{$module->id}}">{{ $module->name}}</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <select name="permit" id="permit_{{$module->id}}" data-placeholder="Permiso" class="permit form-control form-control-sm select" data-container-css-class="select-sm" data-fouc>
                                                <option></option>
                                                @foreach(PERMISSION as $index => $item)
                                                    <option value="{{$index}}" @if($module->permission == $index) selected @endif>{{$item}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach--}}

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
        $('.form_profile').modal('hide');
    })

    $('.save-data').click(function(e){
        e.preventDefault();

        var _modules = [];
        $("input[name = modules]").each(function () {
            if($(this).is(':checked')){
                var _idPermit = 'permit_' + $(this).val();

                if ($('#' + _idPermit).val() !== '') {
                    var permit = {
                        id: $(this).val(),
                        pass: $('#' + _idPermit).val()
                    };
                }
                _modules.push(permit);
            }
        });

        axios.post("{{ url('profile/store') }}",{
            idProfile: "{{ $id }}",
            name: $('#name').val(),
            modules: _modules,
            permission: $('#permission').val(),
            description: $('#description').val()
        })
        .then(function (response){
            var data = response.data;

            $('.form_profile').modal('hide');
            $('#content-data').DataTable().ajax.reload(null, false);

            toast.fire({
                text: 'Registro guardado',
                type: 'success'
            });
        })
        .catch(function (response){
            var objectSelects = ['permission'];
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
        $('.form_profile').modal('show');
    });
</script>
