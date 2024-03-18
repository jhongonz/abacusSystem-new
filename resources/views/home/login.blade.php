@extends('layouts.login')

@section('form_login')
<!-- Login card -->
<form class="login-form">
	<div class="card mb-0 border-0">
		<div class="card-body">
			<div class="text-center mb-3">
				<img class="border-grey-400 p-0 mb-3 mt-0" src="{{ asset('images/logo-creasoft-login.png') }}">
				<h6 class="mb-0 font-weight-normal">Control de acceso</h6>
			</div>

			<div class="form-group form-group-feedback form-group-feedback-left">
				<input type="text" id="user_login" name="user_login" class="form-control login" placeholder="Login">
				<div class="form-control-feedback">
					<i class="fas fa-user-tie text-muted"></i>
				</div>
				<span class="form-text text-danger text-user_login"></span>
			</div>

			<div class="form-group form-group-feedback form-group-feedback-left">
                <div class="input-group">
				    <input type="password" id="password"  name="password" class="form-control password" placeholder="Password">
                    <span class="input-group-prepend">
                        <button class="btn btn-light btn-icon show-password" type="button"><i id="icon-password" class="fas fa-eye text-muted"></i></button>
                    </span>
                </div>

				<div class="form-control-feedback">
					<i class="fas fa-unlock-alt text-muted"></i>
				</div>
				<span class="form-text text-danger text-password"></span>
			</div>

			<div class="form-group d-flex align-items-center">
				<div class="form-check mb-0">
					<label class="form-check-label">
						<input type="checkbox" id="remember" name="remember" class="form-input-styled" data-fouc>
						Recordar
					</label>
				</div>

				<a href="#" class="ml-auto text-grey-400 form-recover text-dark"><u>Olvidaste tu password ?</u></a>
			</div>

			<div class="form-group">
				<button type="submit" class="btn-login btn btn-primary btn-block">Ingresar <i class="fas fa-chevron-right ml-2" style="bg-color:#1186AD"></i></button>

			<span class="form-text text-center text-muted">De continuar, estas confirmando que has leido nuestros <a href="#">Términos &amp; Condiciones</a></span>
		</div>
	</div>
</form>
<!-- /login card -->
@endsection

@push('javascript')
<script type="text/javascript">

$('#user_login').focus();

$('.show-password').click(function(e){
    e.preventDefault();
    var typeInput = $('#password').attr('type');

    if (typeInput == 'password') {
        $('#password').prop('type','text');
        $('#icon-password').removeClass('fa-eye').addClass('fa-eye-slash');
    } else {
        $('#password').prop('type','password');
        $('#icon-password').removeClass('fa-eye-slash').addClass('fa-eye');
    }
});

$('.btn-login').click(function(e){
    e.preventDefault();
    
    axios.post("{{ url('login') }}",{
        login : $('#user_login').val(),
        password : $('#password').val(),
        remember : ($('#remember').prop('checked')) ? true : false
    })
	.then(function (response){
        window.location.href = "{{ url('home') }}";
    })
    .catch(function (error){
        var response = error.response;
        
        if (402 === response.status) {
            $.each(response.data.errors, function(index, value){
                $('.' + index).addClass('border-danger');
            });
        }
        
        toast.fire({
            text: response.data.message,
            type: 'error'
        });
    });
});

$('.form-recover').click(function(e){
    e.preventDefault();

    axios.get("{{ url('/recovery-account') }}",axiosConfig)
	.then(function (response){
        var data = response.data;
        
        $('#content-modal').html(data.html);
    });
});

$(document).ready(function(){
    $('.form-input-styled').uniform();
});
</script>
@endpush