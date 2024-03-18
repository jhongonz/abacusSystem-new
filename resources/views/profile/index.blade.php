@extends($layout)

@section('header')
<!-- Page header -->
<div class="page-header page-header-light">
    <div class="breadcrumb-line breadcrumb-line-light header-elements-sm-inline pr-0">
        <div class="d-flex">
            <div class="breadcrumb mr-4 text-blue-800">
                <a href="" class="home breadcrumb-item"><i class="fas fa-th-large mr-1"></i>Inicio</a>
                <span class="breadcrumb-item active">Perfiles</span>
            </div>

            <div class="breadcrumb">
                <button type="button" class="new-registry btn btn-outline alpha-orange text-orange-600"><i class="fas fa-plus-square mr-2"></i>Nuevo</button>
            </div>
        </div>

        <div class="header-elements d-inline">
            <div class="header-elements">
                <div class="col m-0 pr-1">
                    <div class="form-group form-group-feedback form-group-feedback-right">
                        <input type="text" class="form-control form-control-sm" name="filter" id="filter" placeholder="Buscar">
                        <div class="form-control-feedback">
                            <i class="icon-search4 font-size-xs text-muted"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /page header -->
@stop

@section('content')
<table class="table display compact table-striped table-bordered" style="width:100%" id="content-data">
    <thead>
        <tr class="bg-grey">
            <th>Id</th>
            <th>Nombre</th>
            <th>Estado</th>
            <th></th>
        </tr>
    </thead>
</table>
@stop

@section('javascript')
@parent
<script type="text/javascript">
var dataFilters = {!! $pagination !!};
var _start = dataFilters.start;
var _search = (dataFilters.filters) ? dataFilters.filters.search : '';

$('.select').select2({
    minimumResultsForSearch: Infinity
});

var table = $('#content-data').DataTable({
    language: { url: "{{ asset('styles/global_assets/css/plugins/tables/Spanish.json') }}" },
    dom: DOM_DATATABLE_PANEL,
    buttons: [{
        text: '<i class="fas fa-sync-alt fa-fw fa-lg text-grey"></i>',
        className: 'btn btn-icon',
        action: function (e, dt, node, config) {
            e.preventDefault();
            $('#content-data').DataTable().ajax.reload(null, false);
        }
    }],
    scrollResize: true,
    scrollY: 100,
    pageLength: 30,
    scrollCollapse: true,
    processing: true,
    serverSide: true,
    displayStart: _start,
    ajax: {
        url: "{{ route('panel.profile.get-profiles') }}",
        type: 'POST',
        data: function(d) {
            d.filters = {
                q: $('#filter').val()
            }
        },
        beforeSend: function() {
            $("#content-data tbody tr").addClass('disabled-row');
        },
        complete: function() {
            $("#content-data tbody tr").removeClass('disabled-row');
        }
    },
    columnDefs: [
        {className:'dt-center',targets:[0]}
    ],
    columns: [
        {data: 'id', className: 'onclick-row', width: 100},
        {data: 'name', className: 'onclick-row'},
        {data: 'state_literal', name: 'state', orderable: false, searchable:false, width: 50},
        {data: 'tools', orderable: false, searchable: false, width: 10}
    ],
    drawCallback: function() {

        $('.editElement').click(function(e){
            e.preventDefault();
            var _id = $(this).data('id');

            axios.get("{{ route('panel.module.get-module') }}/" + _id)
            .then(function (response){
                $('#content-modal').html(response.data.html);
            })
            .catch(function(error){
                toast.fire({
                    text: 'No ha podido realizar la operación, error obteniendo el registro',
                    type: 'error'
                });
            });
        });

        $(".changeState").click(function(e){
            e.preventDefault();

            axios.post("{{ route('panel.profile.change-state-profile') }}",{
                id : $(this).data('id')
            })
            .then(function (response){
                $('#content-data').DataTable().ajax.reload(null, false);
                toast.fire({
                    text: 'Estado actualizado',
                    type: 'success'
                });
            })
            .catch(function (error){
                toast.fire({
                    text: 'No ha podido realizar la operación, error obteniendo el registro',
                    type: 'error'
                });
            });
        });

        $('.deleteElement').click(function(e){
            e.preventDefault();
            var _id = $(this).data('id');

            toastq.fire({
                title: 'Estas seguro de eliminar?',
                text: "No se podrá revertir la acción",
                type: 'warning'
            }).then(function(result) {
                if(result.value) {
                    axios.get("/profiles/delete/" + _id)
                    .then(function (response){
                        $('#content-data').DataTable().ajax.reload(null, false);
                        toast.fire({
                            text: 'Registro eliminado con exito.!',
                            type: 'success'
                        });
                    })
                    .catch(function(error) {
                        toast.fire({
                            text: 'No ha podido realizar la operación para eliminar el registro',
                            type: 'error'
                        });
                    });
                }
            });
        });
    }
});

$('#content-data tbody').on('dblclick', '.onclick-row', function (e) {
    e.preventDefault();
    var data = table.row($(this).closest('tr')).data();

    axios.get("{{ route('panel.module.get-module') }}/" + data.id)
    .then(function (response){
        $('#content-modal').html(response.data.html);
    })
    .catch(function(error){
        toast.fire({
            text: 'No ha podido realizar la operación, error obteniendo el registro',
            type: 'error'
        });
    });
});

$('.new-registry').click(function(e){
    e.preventDefault();

    axios.get("{{ route('panel.module.get-module') }}/")
    .then(function (response){
        $('#content-modal').html(response.data.html);
    })
    .catch(function(error){
        toast.fire({
            text: 'No ha podido realizar la operación, error obteniendo el registro',
            type: 'error'
        });
    });
});

$(document).ready(function(){
    $('#filter').focus();

    if (_search) {
        $('#filter').val(_search);
        $('#content-data').DataTable().ajax.reload();
    }

    $('#filter').keyup(function(e){
        e.preventDefault();

        if (e.which == ENTER || e.which == BACKSPACE) {
            $('#content-data').DataTable().ajax.reload();
        }
    });
});
</script>
@stop
