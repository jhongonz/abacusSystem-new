<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>@yield('name_title','Login')</title>

	<!-- Global stylesheets -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
	<link href="{{ asset('styles/global_assets/css/icons/icomoon/styles.min.css') }}" rel="stylesheet" type="text/css">
	<link href="{{ asset('styles/global_assets/css/icons/fontawesome6/css/all.min.css') }}" rel="stylesheet" type="text/css">
	<link href="{{ asset('styles/backend_assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
	<link href="{{ asset('styles/backend_assets/css/bootstrap_limitless.min.css') }}" rel="stylesheet" type="text/css">
	<link href="{{ asset('styles/backend_assets/css/layout.min.css') }}" rel="stylesheet" type="text/css">
	<link href="{{ asset('styles/backend_assets/css/components.min.css') }}" rel="stylesheet" type="text/css">
	<link href="{{ asset('styles/backend_assets/css/colors.min.css') }}" rel="stylesheet" type="text/css">

	<style type="text/css">
		body {
			background-image: url("{{ asset('styles/global_assets/images/backgrounds/user_bg_creasoft.jpg') }}");
			background-repeat: no-repeat;
			background-attachment: fixed;
			background-size: cover;
		}
	</style>
	<!-- /global stylesheets -->

    <link rel="shortcut icon" type="image/jpg" href="{{ asset('styles/css/icons/favicon.png') }}"/>
</head>

<body>

	<!-- Page content -->
	<div class="page-content">

		<!-- Main content -->
		<div class="content-wrapper">

			<!-- Content area -->
			<div class="content d-flex justify-content-center align-items-center">

				@yield('form_login')

			</div>
			<!-- /content area -->

		</div>
		<!-- /main content -->

	</div>
	<!-- /page content -->

<!-- MODAL'S CONTENT -->
<div id="content-modal"></div>
</body>
<!-- Files JS and Javascript -->

<!-- Core JS files -->
<script src="{{ asset('styles/global_assets/js/main/axios.min.js') }}"></script>
<script src="{{ asset('styles/global_assets/js/main/jquery.min.js') }}"></script>
<script src="{{ asset('styles/global_assets/js/main/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('styles/global_assets/js/plugins/loaders/blockui.min.js') }}"></script>
<!-- /core JS files -->

<!-- Theme JS files -->
<script src="{{ asset('styles/global_assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
<script src="{{ asset('styles/global_assets/js/plugins/notifications/sweet_alert.min.js') }}"></script>
<script src="{{ asset('styles/global_assets/js/plugins/forms/selects/bootstrap_multiselect.js') }}"></script>
<script src="{{ asset('styles/global_assets/js/plugins/forms/styling/switchery.min.js') }}"></script>

<script src="{{ asset('styles/backend_assets/js/app.js') }}"></script>
<script src="{{ asset('styles/js/functions.js') }}"></script>
<!-- /theme JS files -->

@stack('javascript')
<!-- /Files JS and Javascript -->
</html>
