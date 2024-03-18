<ul class="navbar-nav">
	<li class="nav-item">
		<a href="#" class="navbar-nav-link sidebar-control sidebar-main-toggle d-none d-md-block">
			<i class="fas fa-align-justify"></i>
		</a>
	</li>
</ul>

<span class="badge badge-pill ml-md-3 mr-md-auto">&nbsp;</span>
<div class="divfullscreen" style="display: block;">
	<a href="#" class="navbar-nav-link d-none d-md-block fullscreen">
		<i class="fas fa-expand"></i>
	</a>
</div>

<div class="divnormalscreen" style="display: none;">
	<a href="#" class="navbar-nav-link d-none d-md-block normalscreen">
		<i class="fas fa-compress"></i>
	</a>
</div>

@auth
<ul class="navbar-nav">
	<li class="nav-item dropdown dropdown-user">
		<a href="#" class="navbar-nav-link d-flex align-items-center dropdown-toggle" data-toggle="dropdown">
			<img src="" class="rounded-circle mr-2" height="34" alt="">
			<span class="font-size-xs font-weight-semibold text-capitalize"></span>
		</a>

		<div class="dropdown-menu dropdown-menu-right p-0">
			<a href="#" class="my-account dropdown-item"><i class="fas fa-user-edit text-success-600 font-size-sm"></i> Mi cuenta</a>
			<div class="dropdown-divider m-0"></div>
			<a href="{{ route('panel.logout') }}" class="logout dropdown-item"><i class="fas fa-power-off text-danger-600 font-size-sm"></i> Salir</a>
		</div>
	</li>
</ul>
@endauth