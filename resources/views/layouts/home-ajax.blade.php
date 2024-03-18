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

@yield('javascript')
<script type="text/javascript">

$('.home').click(function(e){
    e.preventDefault();

    axios.get("{{ url('home') }}")
    .then(function (response){
        var data = response.data;

        $("#content-body").html(data.html);
        window.history.pushState("data","Title","{{ url('home') }}");
    });
});

$(document).ready(function(){
    $('input').attr('autocomplete', 'off');
    $('[data-fancybox="gallery"]').fancybox(FANCYBOX_OPTIONS);
});

</script>
