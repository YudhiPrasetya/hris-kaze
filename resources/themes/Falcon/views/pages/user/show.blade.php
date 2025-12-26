@extends('falcon::layouts.base')

@section('content')
	<x-bootstrap::row>
		<x-bootstrap::column breakpoint="EXTRA_SMALL|12;MEDIUM|12;LARGE|9">
			<x-bootstrap::card>
				<x-bootstrap::card.header>
					<x-bootstrap::row class="align-items-baseline justify-content-between">
						<x-bootstrap::column breakpoint="EXTRA_SMALL|6;SMALL|auto;MEDIUM|8" class="d-flex flex-column align-items-baseline">
							<h5 class="fs-2 font-weight-semi-bold mb-0 text-nunito py-2 py-xl-0 text-truncate w-100 text-truncate">
								{{ $model->name }}<br />
								<small class="fs-0 text-muted d-block">User</small>
							</h5>
						</x-bootstrap::column>
						<x-bootstrap::column breakpoint="EXTRA_SMALL|6;SMALL|auto" class="d-flex align-items-baseline">
							<a href="{{ route('user.edit', ['user' => $model->id]) }}" class="btn-falcon-success btn mr-1" role="button"><i class="fad fa-pencil-alt mr-2"></i>Edit</a>
							<button class="btn-falcon-danger btn" type="button" onclick="event.preventDefault(); document.getElementById('delete-form-{{ $model->id }}').submit();">
								<i class="fad fa-trash-alt mr-2"></i>Delete
							</button>
							<form id="delete-form-{{ $model->id }}" action="{{ route('user.destroy', ['user' => $model->id]) }}" method="POST" style="display: none;">
								<input type="hidden" name="_method" value="DELETE">
								<input type="hidden" name="_token" value="{{ csrf_token() }}">
							</form>
						</x-bootstrap::column>
					</x-bootstrap::row>
				</x-bootstrap::card.header>
				<x-bootstrap::card.body class="bg-light">
					<x-bootstrap::row>
						<x-bootstrap::column breakpoint="EXTRA_SMALL;MEDIUM|3" class="d-flex flex-column align-items-center justify-content-start">
							<div class="avatar w-100 h-auto">
								<img class="rounded-circle img-thumbnail w-100" src="{{ $model->profile_photo_path }}" alt="Profile Picture" />
							</div>
						</x-bootstrap::column>
							<x-bootstrap::column breakpoint="EXTRA_SMALL;MEDIUM|9" class="overflow-hidden">
							<div class="fancy-tab">
								<div class="nav-bar">
									<div class="nav-bar-item px-3 px-sm-4 active">Info</div>
									<div class="nav-bar-item px-3 px-sm-4">Role & Permissions</div>
									<div class="nav-bar-item px-3 px-sm-4">Browser Sessions</div>
								</div>
								<div class="fancy-tab-contents mt-3">
									<div class="tab-content fancy-tab-content active">
										<div class="form-group">
											<h6 class="text-600 control-label mb-1">Name</h6>
											<span id="name" class="form-control-plaintext text-1000 fs-0">{{ $model->name }}</span>
										</div>
										<div class="form-group">
											<h6 class="text-600 control-label mb-1">Username</h6>
											<span id="username" class="form-control-plaintext text-1000 fs-0">{{ $model->username }}</span>
										</div>
										<div class="form-group">
											<h6 class="text-600 control-label mb-1">Role</h6>
											<span id="username" class="form-control-plaintext text-1000 fs-0">{{ $model->roles[0]->alias }}</span>
										</div>
										<div class="form-group">
											<h6 class="text-600 control-label mb-1">Email</h6>
											<span id="email" class="form-control-plaintext text-1000 fs-0"><a href="mailto:{{ $model->email }}">{{ $model->email }}</a></span>
										</div>
										<div class="form-group">
											<h6 class="text-600 control-label mb-1">Enabled</h6>
											<span id="enabled" class="form-control-plaintext text-1000 fs-0">
											@if($model->enabled)
												<span class="badge badge-success">Yes</span>
											@else
												<span class="badge badge-danger">No</span>
											@endif
											</span>
										</div>
										<div class="form-group">
											<h6 class="text-600 control-label mb-1">Verified At</h6>
											<span id="email_verified_at" class="form-control-plaintext text-1000 fs-0">{{ empty($model->email_verified_at) ? 'Not Verified' : $model->email_verified_at }}</span>
										</div>
										<div class="form-group">
											<h6 class="text-600 control-label mb-1">Last Login</h6>
											<span id="last_login" class="form-control-plaintext text-1000 fs-0">{{ $model->last_login }}</span>
										</div>
									</div>
									<div class="fancy-tab-content">
										<div class="form-group">
											<h6 class="text-600 control-label mb-1">Role</h6>
											<span id="last_login" class="form-control-plaintext text-1000 fs-0">{{ $model->roles[0]->alias }}</span>
										</div>
										<x-bootstrap::media variant="primary" class="my-4" icon="fad fa-shield-alt" title="Permissions" subtitle="Below is the list of permissions of <strong>{{ $model->roles[0]->alias }}</strong> role." />
										@php
											$permissions = \App\Models\Permission::all();
											$permissions = $permissions->pluck('description', 'name');
											$perms = $model->permissions->pluck('description', 'name');
											$tmp = collect([]);
											$g = null;
											$s = null;

											foreach ($permissions as $perm => $desc) {
												$group = Str::before($perm, '.');
												$sub = Str::beforeLast($perm, '.');
												//$group = Str::beforeLast($group, '.');
												if (!$tmp->has($group)) $tmp->offsetSet($group, collect([]));
												$g = $tmp->get($group);
												clock($group, $g, $sub);

												$desc = str_replace('destroy', '<span class="text-danger" data-toggle="tooltip" data-html="true" data-placement="auto" title="<i class=\'fad fa-exclamation-triangle text-warning mr-1\'></i>Careful">delete</span>', $desc);
												$desc = str_replace('index', 'view list of', $desc);
												$desc = str_replace('crypto', 'cryptography', $desc);
												$desc = str_replace('show', 'view', $desc);
												$desc = str_replace('audit', 'audit trail', $desc);

												if (!in_array($sub, ['index', 'create', 'destroy', 'show', 'edit', 'download'])) {
													if (!$g->has($sub)) {
														$g->offsetSet($sub, collect([]));
													}

													$s = $g->get($sub);
													$s->offsetSet(Str::afterLast($perm, '.'), collect(['name' => $perm, 'desc' => $desc, 'enabled' => $perms->has($perm)]));
													$tmp->offsetSet($group, $g);
												}
												else {
													$g->offsetSet($sub, collect(['name' => $perm, 'desc' => $desc, 'enabled' => $perms->has($perm)]));
												}
											}

											$tmp = $tmp->sortKeys();
										@endphp
										<x-bootstrap::row>
											<x-bootstrap::column breakpoint="EXTRA_SMALL|4" class="border-right">
												<div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
													@foreach($tmp as $group => $values)
														@php
															$title = str_replace('.', ' ', $group);
															$title = str_replace('_', ' ', $title);
															$title = collect(explode(' ', $title))->map(function($str) {
																return strlen($str) < 3 ? Str::upper($str) : Str::title($str);
															})->join(' ');
														@endphp
														<a class="nav-link {{ $loop->first ? 'active' : '' }}" id="v-pills-{{ $group }}-tab" data-toggle="pill" href="#v-pills-{{ $group }}" role="tab" aria-controls="v-pills-{{ $group }}" aria-selected="true">{{ $title }}</a>
													@endforeach
												</div>
											</x-bootstrap::column>
											<x-bootstrap::column>
												<div class="tab-content" id="v-pills-tabContent">
													@foreach($tmp as $group => $values)
														<div class="tab-pane fade show {{ $loop->first ? 'active' : '' }}" id="v-pills-{{ $group }}" role="tabpanel" aria-labelledby="v-pills-{{ $group }}-tab">
															@foreach($values as $group => $perms)
																@if(in_array($group, ['index', 'create', 'destroy', 'show', 'edit', 'download']))
																	<span id="{{ $perms->get('name') }}" class="form-control-plaintext text-1000 fs-0">
																		<i class="far fa-{{ $perms->get('enabled') ? 'check' : 'times' }} mr-1 fs-0 text-{{ $perms->get('enabled') ? 'success' : 'danger' }}" style="width: 1em"></i>
																		{!! $perms->get('desc') !!}
																	</span>
																@else
																	@php
																		$title = str_replace('_', '.', $group);
																		$title = collect(explode('.', $title))->map(function ($part) {
																			$part = str_replace('cnp', 'card not present', $part);
																			$part = str_replace('cp', 'card present', $part);
																			$part = str_replace('crypto', 'cryptography', $part);
																			$part = str_replace('show', 'show/view', $part);
																			$part = Str::title($part);
																			$part = str_replace('Qr', 'QR', $part);

																			return $part;
																		})->join(' ');
																		//$title = Str::title($title);
																	@endphp
																	<div class="media mb-2 {{ $loop->first ? '' : 'mt-4' }} overflow-hidden">
																		<div class="media-body">
																			<h6 class="mb-0 fs-0 text-primary position-relative"><span class="pr-3">{{ $title }}</span>
																				<span class="border-top position-absolute absolute-vertical-center w-100"></span>
																			</h6>
																		</div>
																	</div>
																	@foreach($perms as $perm)
																		@php
																			$desc = str_replace(['qr qr', 'qr'], 'QR', $perm->get('desc'));
																		@endphp
																		<span id="{{ $perm->get('name') }}" class="form-control-plaintext text-1000 fs-0">
																			<i class="far fa-{{ $perm->get('enabled') ? 'check' : 'times' }} mr-1 fs-0 text-{{ $perm->get('enabled') ? 'success' : 'danger' }}" style="width: 1em"></i>
																			{!! $desc !!}
																		</span>
																	@endforeach
																@endif
															@endforeach
														</div>
													@endforeach
												</div>
											</x-bootstrap::column>
										</x-bootstrap::row>
									</div>
									<div class="fancy-tab-content">
										@php
											$sessions = $sessions(false);
										@endphp
										<p class="text-nunito text-600">
											If necessary, you may logout of all of your other browser sessions across all of your devices.<br />
											If you feel your account has been compromised, you should also update your password.
										</p>
										@if (count($sessions) > 0)
										<!-- Other Browser Sessions -->
											@foreach ($sessions as $session)
												<div class="flex flex-row">
													<div class="mr-2">
														@if ($session->agent['is_desktop'])
															<span class="fad fa-desktop-alt"></span>
														@else
															<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round" class="w-8 h-8 text-gray-500">
																<path d="M0 0h24v24H0z" stroke="none"></path><rect x="7" y="4" width="10" height="16" rx="1"></rect><path d="M11 5h2M12 17v.01"></path>
															</svg>
														@endif
													</div>

													<div class="ml-2 text-nunito">
														<div class="text-sm text-600">
															{{ $session->agent['platform'] }} - {{ $session->agent['browser'] }}
														</div>
														<div>
															<div class="text-xs text-500">
																{{ $session->ip_address }},
																@if ($session->is_current_device)
																	<span class="text-success font-semibold">{{ __('This device') }}</span>
																@else
																	{{ __('Last active') }} {{ $session->last_active }}
																@endif
															</div>
														</div>
													</div>
												</div>
											@endforeach
										@endif
									</div>
								</div>
							</div>
						</x-bootstrap::column>
					</x-bootstrap::row>
				</x-bootstrap::card.body>
			</x-bootstrap::card>
		</x-bootstrap::column>
		@include('falcon::pages.user.info')
	</x-bootstrap::row>
@endsection
