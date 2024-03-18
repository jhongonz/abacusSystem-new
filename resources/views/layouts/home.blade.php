<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta http-equiv="Cache-Control" content="nocache, must-revalidate, no-store, max-age=0">
	<meta http-equiv="Pragma" content="no-cache">
	<meta http-equiv="Expires" content="Sun, 02 Jan 1990 00:00:00 GMT">
	<title>@yield('name_title','Gestión Administrativa - Intranet')</title>

	<!-- Global stylesheets -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
	<link href="{{ asset('styles/global_assets/css/icons/icomoon/styles.min.css') }}" rel="stylesheet" type="text/css">
	<link href="{{ asset('styles/global_assets/css/icons/fontawesome6/css/all.min.css') }}" rel="stylesheet" type="text/css">
	<link href="{{ asset('styles/backend_assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
	<link href="{{ asset('styles/backend_assets/css/bootstrap_limitless.min.css') }}" rel="stylesheet" type="text/css">
	<link href="{{ asset('styles/backend_assets/css/layout.min.css') }}" rel="stylesheet" type="text/css">
	<link href="{{ asset('styles/backend_assets/css/components.min.css') }}" rel="stylesheet" type="text/css">
	<link href="{{ asset('styles/backend_assets/css/colors.min.css') }}" rel="stylesheet" type="text/css">
	<link href="{{ asset('styles/global_assets/css/plugins/tables/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css">
	<link href="{{ asset('styles/global_assets/css/plugins/images/jquery.fancybox.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('styles/global_assets/js/plugins/fullcalendar/lib/main.min.css') }}" rel="stylesheet" type="text/css">
	<link href="{{ asset('styles/backend_assets/css/intranet.css') }}" rel="stylesheet" type="text/css">
	<link href="{{ asset('styles/backend_assets/css/custom.css') }}" rel="stylesheet" type="text/css">
    <style>
        .loader-ajax {
            position: fixed;
            display: none;
            left: 0px;
            top: 0px;
            width: 100%;
            height: 100%;
            z-index: 9999;
            background: url("{{ asset('styles/icons/tenor2.gif') }}") 50% 50% no-repeat rgb(255,255,255,0.5);
            opacity: .8;
        }
    </style>
	@yield('styles')
	<!-- /global stylesheets -->

    <link rel="shortcut icon" type="image/jpg" href="{{ asset('styles/icons/favicon.ico') }}"/>
</head>

<body class="navbar-top" style="overflow-y: hidden;">
	<!-- Main navbar -->
	<div class="navbar navbar-expand-md fixed-top navbar-dark bg-panel-primary pr-0" style="background-color: #443F3F;">

		<!-- Header with logos -->
		<div class="navbar-header navbar-dark d-none d-md-flex align-items-md-center bg-panel-primary" style="background-color: #443F3F;">
			<div class="navbar-brand navbar-brand-md">
				<a href="" class="home d-inline-block">
					<img src="{{ url('images/colle-logo.jpg')}}" alt="">
				</a>
			</div>

			<div class="navbar-brand navbar-brand-xs">
				<a href="#" class="d-inline-block">
					<img src="{{ url('images/colle-logo.jpg')}}" alt="">
				</a>
			</div>
		</div>
		<!-- /header with logos -->

		<div class="d-md-none">
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-mobile">
				<i class="fas fa-sign"></i>
			</button>
			<button class="navbar-toggler sidebar-mobile-main-toggle" type="button">
				<i class="fas fa-bars"></i>
			</button>
		</div>

		<!-- Navbar content -->
		<div class="collapse navbar-collapse" id="navbar-mobile">
		  @include('layouts.menu-user')
		</div>
		<!-- /navbar content -->

	</div>
	<!-- /main navbar -->


	<!-- Page content -->
	<div class="page-content">

		<!-- Main sidebar -->
		@include('layouts.menu')
		<!-- /main sidebar -->

		<!-- Main content -->
		<div class="content-wrapper" id="content-body" style="background-color: #FFFFFF">

			<!-- Page header -->
			<div class="page-header p-0 m-0" id="content-header">
				@yield('header')
			</div>
			<!-- /page header -->


			<!-- Content area -->
			<div class="content p-0 m-0" style="overflow-y: auto; height: 100px; position: relative;">
				@yield('content')
			</div>
			<!-- /content area -->

			<!-- Footer -->
			<!-- /footer -->

		</div>
		<!-- /main content -->

	</div>
	<!-- /page content -->

<!-- MODAL'S CONTENT -->
<div id="content-modal"></div>

<!-- MODAL'S LOADER AJAX -->
<div class="loader-ajax"></div>
</body>
<!-- Core JS files -->
<script src="{{ asset('styles/global_assets/js/main/jquery.min.js') }}"></script>
<script src="{{ asset('styles/global_assets/js/main/axios.min.js') }}">"></script>
<script src="{{ asset('styles/global_assets/js/main/jquery-ui.js') }}"></script>
<script src="{{ asset('styles/global_assets/js/main/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('styles/global_assets/js/plugins/loaders/blockui.min.js') }}"></script>
<!-- /core JS files -->

<!-- Theme JS files -->
<script src="{{ asset('styles/global_assets/js/main/jquery.fileDownload.js') }}"></script>
<script src="{{ asset('styles/global_assets/js/main/jquery.redirect.js') }}"></script>
<script src="{{ asset('styles/global_assets/js/plugins/visualization/d3/d3.min.js') }}"></script>
<script src="{{ asset('styles/global_assets/js/plugins/visualization/d3/d3_tooltip.js') }}"></script>
<script src="{{ asset('styles/global_assets/js/plugins/forms/styling/switchery.min.js') }}"></script>
<script src="{{ asset('styles/global_assets/js/plugins/forms/styling/switch.min.js') }}"></script>
<script src="{{ asset('styles/global_assets/js/plugins/ui/moment/moment.min.js') }}"></script>
<script src="{{ asset('styles/global_assets/js/plugins/pickers/daterangepicker.js') }}"></script> <!-- picker -->
<script src="{{ asset('styles/global_assets/js/plugins/pickers/pickadate/picker.js') }}"></script> <!-- picker -->
<script src="{{ asset('styles/global_assets/js/plugins/pickers/pickadate/picker.date.js') }}"></script> <!-- picker -->
<script src="{{ asset('styles/global_assets/js/plugins/pickers/pickadate/picker.time.js') }}"></script> <!-- picker -->
<script src="{{ asset('styles/global_assets/js/plugins/pickers/pickadate/legacy.js') }}"></script>
<script src="{{ asset('styles/global_assets/js/plugins/notifications/sweet_alert.min.js') }}"></script>
<script src="{{ asset('styles/global_assets/js/plugins/notifications/jgrowl.min.js') }}"></script>
<script src="{{ asset('styles/global_assets/js/plugins/notifications/bootbox.min.js') }}"></script>
<script src="{{ asset('styles/global_assets/js/plugins/tables/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('styles/global_assets/js/plugins/tables/datatables/extensions/buttons.min.js') }}"></script>
<script src="{{ asset('styles/global_assets/js/plugins/tables/datatables/dataTables.scrollResize.min.js') }}"></script>
<script src="{{ asset('styles/global_assets/js/plugins/forms/selects/select2.min.js') }}"></script>
<script src="{{ asset('styles/global_assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
<script src="{{ asset('styles/global_assets/js/plugins/forms/selects/bootstrap_multiselect.js') }}"></script>
<script src="{{ asset('styles/global_assets/js/plugins/ui/perfect_scrollbar.min.js') }}"></script>
<script src="{{ asset('styles/global_assets/js/plugins/images/jquery.fancybox.min.js') }}"></script>
<script src="{{ asset('styles/global_assets/js/plugins/fullcalendar/lib/main.min.js') }}"></script>
<script src="{{ asset('styles/global_assets/js/plugins/fullcalendar/lib/locales/es.js') }}"></script>

<script src="{{ asset('styles/backend_assets/js/app.js') }}"></script>
<!-- <script src="{{ asset('styles/global_assets/js/demo_pages/components_modals.js') }}"></script> -->
    <script src="{{ asset('styles/js/functions.js') }}?v={{$versionRandom}}"></script>
    <script src="{{ asset('styles/js/actions.js') }}?v={{$versionRandom}}"></script>
<!-- /theme JS files -->

<script type="text/javascript">
$.fn.dataTable.ext.errMode = 'none';

const ps = new PerfectScrollbar('.sidebar-fixed .sidebar-content', {
    wheelSpeed: 2,
    wheelPropagation: true
});

const ps2 = new PerfectScrollbar(document.querySelector('.content'), {
    wheelSpeed: 2,
    wheelPropagation: true
});

$.datepicker.regional['es'] = {
    closeText: 'Cerrar',
    prevText: '< Ant',
    nextText: 'Sig >',
    currentText: 'Hoy',
    monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
    monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
    dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
    dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
    dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
    weekHeader: 'Sm',
    dateFormat: 'dd/mm/yy',
    firstDay: 1,
    isRTL: false,
    showMonthAfterYear: false,
    yearSuffix: ''
};
$.datepicker.setDefaults($.datepicker.regional['es']);

$(document).ready(function(){
    $('input').attr('autocomplete', 'off');
    $('[data-fancybox="gallery"]').fancybox(FANCYBOX_OPTIONS);
});

</script>
@yield('javascript')
</html>
