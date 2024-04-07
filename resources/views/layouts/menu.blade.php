<div class="sidebar sidebar-light alpha-indigo sidebar-main sidebar-fixed sidebar-expand-md" style="background-color: #ffffff;">

	<!-- Sidebar mobile toggler -->
	<div class="sidebar-mobile-toggler text-center">
		<a href="#" class="sidebar-mobile-main-toggle">
			<i class="fas fa-chevron-left"></i>
		</a>
		Navegación
		<a href="#" class="sidebar-mobile-expand">
			<i class="fas fa-expand"></i>
			<i class="fas fa-compress"></i>
		</a>
	</div>
	<!-- /sidebar mobile toggler -->

	<!-- Sidebar content -->
	<div class="sidebar-content">

		<!-- User menu -->
		<div class="sidebar-user">
			<div class="card-body">
				<div class="media">
					<div class="mr-3">
                        <a data-fancybox="gallery" href="@isset($user){{$user->photo()->value()}}@endisset"><img src="@isset($user){{$user->photo()->value()}}@endisset" width="38" height="38" class="rounded-circle" alt=""></a>
					</div>

					<div class="media-body">
						<div class="media-title font-weight-semibold font-size-sm">@isset($employee){{ $employee->name()->value() }}@endisset</div>
						<div class="font-size-xs text-success-600 font-weight-semibold">
							@isset($profile)<i class="fas fa-user-check font-size-sm"></i> &nbsp;{{ $profile->name()->value() }}@endisset
						</div>
					</div>

					<div class="ml-3 align-self-center">
						<a href="#" class="my-account text-grey-600"><i class="fas fa-cog"></i></a>
					</div>
				</div>
			</div>
		</div>
		<!-- /user menu -->

		<!-- Main navigation -->
		<div class="card card-sidebar-mobile">
			<ul class="nav nav-sidebar" data-nav-type="accordion">

				<!-- Main -->
				<li class="nav-item">
					<a href="{{ url('home') }}" class="nav-link active">
						<i class="icon-home4"></i>
						<span>
							Panel
							<span class="d-block font-weight-normal opacity-50">Funciones principales</span>
						</span>
					</a>
				</li>
				<!-- /main -->

				@if (isset($menu) and count($menu) > 0)
					@foreach($menu as $option)
						@if($option->haveChildren())
							<li class="nav-item nav-item-submenu p-0 @if($option->expanded()) nav-item-open @endif">
								<a href="#" class="nav-link"><i class="fas fa-cubes text-grey-600"></i> <span>{{ $option->name()->value() }}</span></a>
								<ul class="nav nav-group-sub" data-submenu-title="{{ $option->name()->value() }}" @if($option->expanded()) style="display: block;" @endif>
                                @foreach($option->options() as $child)
									@if(null !== $child->route()->value())
                                        <li class="nav-item p-0"><a href="" data-url="{{ url($child->route()->value()) }}" class="nav-link action-menu"><i class="{{ $child->icon()->value() }} text-grey-600"></i>{{ $child->name()->value() }}</a></li>
                                    @endif
								@endforeach
								</ul>
							</li>
						@else
							@if(null !== $option->route()->value())
							<li class="nav-item p-0"><a href="{{ url($option->route()->value()) }}" class="nav-link"><i class="icon-width"></i> <span>{{ $option->name()->value() }}</span></a></li>
							@endif
						@endif
					@endforeach
				@endif
			</ul>
		</div>
		<!-- /main navigation -->

	</div>
	<!-- /sidebar content -->
</div>
