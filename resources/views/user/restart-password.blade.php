@extends('layouts.login')

@section('form_login')
<!-- Login card -->
<form class="login-form">
	<div class="card mb-0 border-0">
		<div class="card-body">
			<div class="text-center mb-3">
				<img class="border-grey-400 p-2 mb-3 mt-1" src="{{ asset('styles/css/icons/administracion-48.png') }}">
				<h5 class="mb-0">Restablecer clave de acceso</h5>
			</div>

			@if($activeLink)
				<div class="form-group form-group-feedback form-group-feedback-left">
					<input type="password" id="password" name="password" class="form-control password" placeholder="Nuevo password">
					<div class="form-control-feedback">
						<i class="icon-lock2 text-muted"></i>
					</div>
					<span class="form-text text-danger text-user_login"></span>
				</div>

				<div class="form-group form-group-feedback form-group-feedback-left">
					<input type="password" id="repeat"  name="repeat" class="form-control repeat" placeholder="Repetir password">
					<div class="form-control-feedback">
						<i class="icon-lock2 text-muted"></i>
					</div>
					<span class="form-text text-danger text-password"></span>
				</div>

				<div class="form-group">
					<button type="submit" class="btn-restart btn btn-success btn-block">Restablecer <i class="icon-shield-check ml-2"></i></button>
				</div>
                
                <a href="" class="ml-auto text-grey-400 btn-home text-dark"><u>Ir a principal</u></a>
			@else
				<div class="alert alert-danger alert-dismissible">
					<span class="font-weight-semibold">Oh no!</span> Este link a caducado.
				</div>
			@endif

			<span class="form-text text-center text-muted">De continuar, estas confirmando que has leido nuestros <a href="#">Términos &amp; Condiciones</a></span>

		</div>
	</div>
</form>
<!-- /login card -->
@endsection

@push('javascript')
<script type="text/javascript">
$('.btn-restart').click(function(e){
    e.preventDefault();

    axios.post("{{ url('reset-password') }}",{
        idUser : "{{$idUser}}",
        password : $('#password').val(),
        password_confirmation : $('#repeat').val(),
    })
	.then(function (response){
        var data = response.data;

        toastq.fire({
            title: 'Password restablecido',
            text: 'Tus credenciales de acceso fueron restablecidas',
            type: 'success'
        }).then(function(result) {
            window.location.href = "{{ url('/') }}";
        });
    })
	.catch(function (error){
        var response = error.response;

        console.log(response.data.errors);
        $.each(response.data.errors, function(index, value){
            $('.' + index).addClass('border-danger');
        });

        toast.fire({
            text: response.data.message,
            type: 'error'
        });
    });
});

$('.btn-home').click(function(e){
    e.preventDefault();
    
    window.location.href = "{{ url('/') }}";
});

$(document).ready(function(){
    $('#password').focus();
});
</script>
@endpush