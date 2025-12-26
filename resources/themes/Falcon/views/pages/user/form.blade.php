@extends('falcon::layouts.base')

@section('javascripts')
	@parent
	<script src="{{ themes('js/default-select.js') }}" defer></script>
	<script src="{{ themes('js/user-create.js') }}" defer></script>
@endsection

@section('content')
	{!! form_start($form, ['attr' => ['autocomplete' => "off"]]) !!}
	<x-bootstrap::row>
		<x-bootstrap::column breakpoint="EXTRA_SMALL|12;MEDIUM|9">

			<x-bootstrap::card>
				<x-bootstrap::card.header>
					<x-bootstrap::row class="align-items-baseline justify-content-between">
						<x-bootstrap::column breakpoint="EXTRA_SMALL|6;SMALL|auto;MEDIUM|9" class="d-flex flex-column align-items-baseline">
							<h5 class="fs-2 font-weight-semi-bold mb-0 text-nunito py-2 py-xl-0 text-truncate w-100 text-truncate">
								@if(!$model->name)
									User
									<small class="fs-0 text-muted d-block">Register new user</small>
								@else
									{{ $model->name }}
									<small class="fs-0 text-muted d-block">User</small>
								@endif
							</h5>
						</x-bootstrap::column>
						<x-bootstrap::column breakpoint="EXTRA_SMALL|6;SMALL|auto" class="d-flex align-items-baseline">
							{!! form_row($form->submit) !!}
						</x-bootstrap::column>
					</x-bootstrap::row>
				</x-bootstrap::card.header>
				<x-bootstrap::card.body class="bg-light">
					<x-bootstrap::row>
						<x-bootstrap::column breakpoint="EXTRA_SMALL;MEDIUM|3" class="d-flex align-items-start justify-content-center">
							<div class="avatar w-100 h-auto">
								<img id="targetPreview" class="rounded-circle img-thumbnail w-100" src="@if(!empty($model->profile_photo_path)){{ $model->profile_photo_path }}@else{{ route('image-placeholder', ['size' => 512, 'bgColor' => 'EEF0F2', 'textColor' => 'ffffff']) }}@endif" alt="Profile Picture" />
							</div>
						</x-bootstrap::column>
						<x-bootstrap::column breakpoint="EXTRA_SMALL;MEDIUM|9" class="overflow-hidden">
							<div class="fancy-tab">
								<div class="nav-bar">
									<div class="nav-bar-item px-3 px-sm-4 active">Info</div>
									<div class="nav-bar-item px-3 px-sm-4">Role & Permissions</div>
								</div>
								<div class="fancy-tab-contents mt-3">
									<div class="fancy-tab-content active">
										@php
											if($model->roles->count())
												$form->role->setValue($model->roles[0]->id)
										@endphp
										{!! form_row($form->name) !!}
										{!! form_row($form->username) !!}
										{!! form_row($form->email) !!}
										{!! form_row($form->password) !!}
										{!! form_row($form->enabled) !!}
										{!! form_row($form->profile_photo_path) !!}
									</div>
									<div class="fancy-tab-content">
										{!! form_row($form->role, ['attr' => ['class_append' => 'select2', 'data-value' => $model->roles->count() ? $model->roles[0]->id : null]]) !!}
										<x-bootstrap::media variant="primary" class="my-4" icon="fad fa-shield-alt" title="Permissions" subtitle="Here you can set permissions for each user." />
										@php
											$permissions = \App\Models\Permission::all();
											$permissions = $permissions->mapWithKeys(function ($item) {
												return [$item['name'] => [$item['id'], $item['description']]];
											});
											$perms = $model->permissions->pluck('description', 'name');
											$tmp = collect([]);
											$g = null;
											$s = null;

											foreach ($permissions as $perm => $value) {
												list($id, $desc) = $value;
												$group = Str::before($perm, '.');
												$sub = Str::beforeLast(Str::after($perm, '.'), '.');
												//$group = Str::beforeLast($group, '.');
												if (!$tmp->has($group)) $tmp->offsetSet($group, collect([]));
												$g = $tmp->get($group);

												$desc = str_replace('destroy', '<span class="text-danger" data-toggle="tooltip" data-html="true" data-placement="auto" title="<i class=\'fad fa-exclamation-triangle text-warning mr-1\'></i>Careful">delete</span>', $desc);
												$desc = str_replace('index', 'show list of', $desc);
												$desc = str_replace('crypto', 'cryptography', $desc);
												$desc = str_replace('show', 'view', $desc);
												$desc = str_replace('audit', 'audit trail', $desc);
												$desc = str_replace('app', 'application', $desc);

												if (!in_array($sub, ['index', 'create', 'destroy', 'show', 'edit'])) {
													if (!$g->has($sub)) {
														$g->offsetSet($sub, collect([]));
													}

													$s = $g->get($sub);
													$s->offsetSet(Str::afterLast($perm, '.'), collect(['id' => $id, 'name' => $perm, 'desc' => $desc, 'enabled' => $perms->has($perm)]));
													$tmp->offsetSet($group, $g);
												}
												else {
													$g->offsetSet($sub, collect(['id' => $id, 'name' => $perm, 'desc' => $desc, 'enabled' => $perms->has($perm)]));
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
															<x-bootstrap::row>
																<x-bootstrap::column class="pb-4">
																	<button class="btn btn-falcon-default" type="button" data-toggle="check" data-parent="#v-pills-{{ $group }}">
																		<i class="fad fa-check-square mr-1"></i>
																		Check/Uncheck All
																	</button>
																</x-bootstrap::column>
															</x-bootstrap::row>
															@foreach($values as $group => $perms)
																@if(in_array($group, ['index', 'create', 'destroy', 'show', 'edit']))
																	<span id="{{ $perms->get('name') }}" class="form-control-plaintext text-1000 fs-0">
																		<div class="custom-control custom-switch">
																			<input class="custom-control-input" id="permissions[{{ $perms->get('name') }}]" {!! $perms->get('enabled') ? 'checked="checked"' : '' !!} name="permissions[{{ $perms->get('name') }}]" type="checkbox" value="{{ $perms->get('id') }}">
																			<label for="permissions[{{ $perms->get('name') }}]" class="custom-control-label">{!! $perms->get('desc') !!}</label>
																		</div>
																	</span>
																@else
																	@php
																		$title = str_replace('_', '.', $group);
																		$title = collect(explode('.', $title))->map(function ($part) {
																			$part = str_replace('cnp', 'card not present', $part);
																			$part = str_replace('cp', 'card present', $part);
																			$part = str_replace('crypto', 'cryptography', $part);
																			$part = str_replace('show', 'show/view', $part);
																			$part = str_replace('app', 'application', $part);
																			$part = Str::title($part);
																			$part = str_replace('Qr', 'QR', $part);

																			return $part;
																		})->join(' ');
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
																			<div class="custom-control custom-switch">
																				<input class="custom-control-input" id="permissions[{{ $perm->get('name') }}]" {!! $perm->get('enabled') ? 'checked="checked"' : '' !!} name="permissions[{{ $perm->get('name') }}]" type="checkbox" value="{{ $perm->get('id') }}">
																				<label for="permissions[{{ $perm->get('name') }}]" class="custom-control-label">{!! $desc !!}</label>
																			</div>
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
								</div>
							</div>
						</x-bootstrap::column>
					</x-bootstrap::row>
				</x-bootstrap::card.body>
			</x-bootstrap::card>
		</x-bootstrap::column>
		@include('falcon::pages.user.info')
	</x-bootstrap::row>
	{!! form_end($form, $renderRest = true) !!}
@endsection
