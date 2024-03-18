<div id="modal-recover" class="modal fade form_recover" tabindex="-1">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">

			<form class="modal-body">
				<div class="text-center mb-3">
					<i class="fas fa-user-shield fa-4x text-warning rounded-round p-2 mb-2 mt-1"></i>
					<h5 class="mb-0">Recuperar acceso</h5>
					<span class="d-block text-muted">Se enviará las instrucciones a su email</span>
				</div>

				<div class="form-group form-group-feedback form-group-feedback-right">
					<input type="text" id="identify" name="identify" class="form-control identification" placeholder="Identificación">
					<div class="form-control-feedback">
						<i class="fas fa-id-card text-muted"></i>
					</div>
				</div>
				<div class="form-group form-group-feedback form-group-feedback-right">
					<input type="email" id="email" name="email" class="form-control email" placeholder="Ingresa tu email">
					<div class="form-control-feedback">
						<i class="fas fa-envelope text-muted"></i>
					</div>
				</div>

			</form>
			<div class="modal-footer p-2 alpha-grey">
                <button type="button" class="close-form btn btn-outline alpha-primary text-primary">Cerrar</button>
                <button type="button" class="send-credentials btn bg-blue btn-labeled btn-labeled-left btn-sm"><b><i class="fas fa-check"></i></b> Recuperar</button>
            </div>
		</div>
	</div>
</div>

<script type="text/javascript">

$('.close-form').click(function(e){
    e.preventDefault();
    $('.form_recover').modal('hide');
});

$('.send-credentials').click(function(e){
    e.preventDefault();

    axios.post("{{ url('validate-account') }}",{
        identification: $('#identify').val(),
        email: $('#email').val()
    })
	.then(function (response){
        $('.form_recover').modal('hide');
        toastq.fire({
            title: 'Las credenciales fueron validadas',
            text: 'Se envió un email a su dirección para completar la recuperación de la cuenta',
            type: 'success'
        });
    })
    .catch(function (error){
        var response = error.response;
        
        $.each(response.data.errors, function(index, value){
            $('.' + index).addClass('border-danger');
        });

        toast.fire({
            text: response.data.message,
            type: 'error'
        });
    });
});

$(document).ready(function(){
    $('.form_recover').modal('show');
});
</script>