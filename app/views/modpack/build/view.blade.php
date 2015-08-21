@extends('layouts/master')
@section('title')
    <title>{{ $build->version }} - {{ $build->modpack->name }} - TechnicSolder</title>
@stop
@section('top')
    <script src="{{{ asset('js/selectize.min.js') }}}"></script>
    <link href="{{{ asset('css/selectize.css') }}}" rel="stylesheet">
@endsection
@section('content')
<div class="page-header">
<h1>Build Management</h1>
</div>
<div class="panel panel-default">
	<div class="panel-heading">
	<div class="pull-right">
		<a href="{{ URL::current() }}" class="btn btn-xs btn-warning">Refresh</a>
		<a href="{{ URL::to('modpack/build/' . $build->id . '?action=edit') }}" class="btn btn-xs btn-danger">Edit</a>
	    <a href="{{ URL::to('modpack/view/' . $build->modpack->id) }}" class="btn btn-xs btn-info">Back to Modpack</a>
	</div>
	Build Info: {{ $build->modpack->name }} - Build {{ $build->version }}
	</div>
	<div class="panel-body">
		<div class="col-md-6">
			<label>Build Version: <span class="label label-default">{{ $build->version }}</span></label><br>
			<label>Minecraft Version: <span class="label label-default">{{ $build->minecraft }}</span></label><br>
			<label>Minecraft MD5: <span class="label label-default">{{ $build->md5 }}</span></label>
		</div>
		<div class="col-md-6">
			<label>Java Version: <span class="label label-default">{{ $build->min_java }}</span></label><br>
			<label>Memory (<i>in MB</i>): <span class="label label-default">{{ $build->min_memory }}</span></label>
		</div>
	</div>
</div>
<div class="alert alert-success" id="success-ajax" style="width: 100%;display: none"></div>
<div class="alert alert-warning" id="warning-ajax" style="width: 100%;display: none"></div>
<div class="alert alert-danger" id="danger-ajax" style="width: 100%;display: none"></div>
<div class="panel panel-default">
	<div class="panel-heading">
	Build Management: {{ $build->modpack->name }} - Build {{ $build->version }}
	</div>
	<div class="panel-body">
		<div class="table-responsive">
		<table class="table">
			<thead>
				<th style="width: 60%">Add a Mod</th>
				<th></th>
				<th></th>
			</thead>
			<tbody>
			<form method="post" action="{{ URL::to('modpack/build/modify') }}" class="mod-add">
			<input type="hidden" name="build" value="{{ $build->id }}">
			<input type="hidden" name="action" value="add">
			<tr id="mod-list-add">
				<td>
					<i class="icon-plus"></i>
					<select class="form-control" name="mod-name" id="mod" placeholder="Select a Mod...">
						@foreach (Mod::all() as $mod)
						<option value="{{ $mod->name }}">{{ $mod->pretty_name }}</option>
						@endforeach
					</select>
				</td>
				<td>
					<select class="form-control" name="mod-version" id="mod-version" placeholder="Select a Modversion...">
					</select>
				</td>
				<td>
					<button type="submit" class="btn btn-success btn-small">Add Mod</button>
				</td>
			</tr>
			</form>
			</tbody>
		</table>
		</div>

		<div class="table-responsive">
		<table class="table">
			<thead>
				<th style="width: 60%">Add a Base Modpack</th>
				<th></th>
				<th></th>
			</thead>
			<tbody>
			<form method="post" action="{{ URL::to('modpack/build/modify') }}" class="base-add">
			<input type="hidden" name="build" value="{{ $build->id }}">
			<input type="hidden" name="action" value="base-add">
			<tr id="base-list-add">
				<td>
					<i class="icon-plus"></i>
					<select class="form-control" name="base-modpack" id="base-modpack" placeholder="Select a Modpack...">
						@foreach (Modpack::where('id', '!=', $build->modpack->id)->get() as $modpack)
						<option value="{{ $modpack->slug }}">{{ $modpack->name }}</option>
						@endforeach
					</select>
				</td>
				<td>
					<select class="form-control" name="base-build" id="base-build" placeholder="Select a build...">
					</select>
				</td>
				<td>
					<button type="submit" class="btn btn-success btn-small">Add Base Modpack</button>
				</td>
			</tr>
			</form>
			</tbody>
		</table>
		</div>
	</div>
</div>
<div class="panel panel-default">
	<div class="panel-heading">
	Base Management: {{ $build->modpack->name }} - Build {{ $build->version }}
	</div>
	<div class="panel-body">

		<div class="table-responsive">
		<table class="table" id="modpack-list">
			<thead>
				<th id="mod-header" style="width: 60%">Modpack</th>
				<th>Build</th>
				<th></th>
			</thead>
			<tbody>
				@foreach ($build->basebuilds as $base)
				<tr>
					<td>{{ HTML::link('modpack/view/'.$base->id, $base->modpack->name) }} ({{ $base->modpack->slug }})</td>
					<td>
						<form method="post" action="{{ URL::to('modpack/build/modify') }}" style="margin-bottom: 0" class="base-version">
							<input type="hidden" class="build-id" name="build_id" value="{{ $build->id }}">
							<input type="hidden" class="base-id" name="base_id" value="{{ $base->id }}">
							<input type="hidden" name="action" value="base-version">
							<div class="form-group input-group">
								<select class="form-control" name="version">
									@foreach ($base->modpack->builds as $base_builds)
									<option value="{{ $base_builds->id }}"{{ $selected = ($base->version == $base_builds->version ? 'selected' : '') }}>{{ $base_builds->version }}</option>
									@endforeach
								</select>
								<span class="input-group-btn">
									<button type="submit" class="btn btn-primary">Change</button>
								</span>
							</div>
						</form>
					</td>
					<td>
						<form method="post" action="{{ URL::to('modpack/build/modify') }}" style="margin-bottom: 0" class="base-delete">
							<input type="hidden" name="build_id" value="{{ $build->id }}">
							<input type="hidden" class="base-id" name="base_id" value="{{ $base->pivot->base_id }}">
							<input type="hidden" name="action" value="base-delete">
							<button type="submit" class="btn btn-danger btn-small">Remove</button>
						</form>
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
		</div>
	</div>
</div>
<div class="panel panel-default">
	<div class="panel-heading">
	Build Management: {{ $build->modpack->name }} - Build {{ $build->version }}
	</div>
	<div class="panel-body">
		<div class="table-responsive">
		<table class="table" id="mod-list">
			<thead>
				<th id="mod-header" style="width: 60%">Mod Name</th>
				<th>Version</th>
				<th></th>
			</thead>
			<tbody>
				@foreach ($modversions as $ver)
				<tr>
					<td>{{ HTML::link('mod/view/'.$ver->mod->id, $ver->mod->pretty_name) }} ({{ $ver->mod->name }})</td>
					<td>
						@if ($build->modversions->contains($ver->id))
						<form method="post" action="{{ URL::to('modpack/build/modify') }}" style="margin-bottom: 0" class="mod-version">
							<input type="hidden" class="build-id" name="build_id" value="{{ $build->id }}">
							<input type="hidden" class="modversion-id" name="modversion_id" value="{{ $ver->pivot->modversion_id }}">
							<input type="hidden" name="action" value="version">
							<div class="form-group input-group">
								<select class="form-control" name="version">
									@foreach ($ver->mod->versions as $version)
									<option value="{{ $version->id }}"{{ $selected = ($ver->version == $version->version ? 'selected' : '') }}>{{ $version->version }}</option>
									@endforeach
								</select>
								<span class="input-group-btn">
									<button type="submit" class="btn btn-primary">Change</button>
								</span>
							</div>
						</form>
						@else
						From Base Modpack
						@endif
					</td>
					<td>
						<form method="post" action="{{ URL::to('modpack/build/modify') }}" style="margin-bottom: 0" class="mod-delete">
							<input type="hidden" name="build_id" value="{{ $build->id }}">
							<input type="hidden" class="modversion-id" name="modversion_id" value="{{ $ver->pivot->modversion_id }}">
							<input type="hidden" name="action" value="delete">
							@if ($build->modversions->contains($ver->id))
							<button type="submit" class="btn btn-danger btn-small">Remove</button>
							@else
							<button type="submit" class="btn btn-danger btn-small" disabled="disabled">Remove</button>
							@endif
						</form>
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
		</div>
	</div>
</div>
@endsection
@section('bottom')
<script type="text/javascript">
var $select = $("#mod").selectize({
			persist: false,
			maxItems: 1,
			sortField: {
				field: 'text',
				direction: 'asc'
			},
		});
var mod = $select[0].selectize;
var $select = $("#mod-version").selectize({
			persist: false,
			maxItems: 1,
			sortField: {
					field: 'text',
					direction: 'asc'
				},
		});
var modversion = $select[0].selectize;
var $select = $("#base-modpack").selectize({
			persist: false,
			maxItems: 1,
			sortField: {
				field: 'text',
				direction: 'asc'
			},
		});
var modpack = $select[0].selectize;
var $select = $("#base-build").selectize({
			persist: false,
			maxItems: 1,
			sortField: {
					field: 'text',
					direction: 'asc'
				},
		});
var modpackbuild = $select[0].selectize;

$(".mod-version").submit(function(e) {
	e.preventDefault();
	$.ajax({
		type: "POST",
		url: "{{ URL::to('modpack/modify/version') }}",
		data: $(this).serialize(),
		success: function (data) {
			console.log(data.reason);
			if(data.status == 'success'){
				$("#success-ajax").stop(true, true).html("Modversion Updated").fadeIn().delay(2000).fadeOut();
			} else if(data.status == 'failed') {
				$("#warning-ajax").stop(true, true).html("Unable to update modversion").fadeIn().delay(2000).fadeOut();
			}
		},
		error: function (xhr, textStatus, errorThrown) {
			$("#danger-ajax").stop(true, true).html(textStatus + ': ' + errorThrown).fadeIn().delay(3000).fadeOut();
		}
	});
});

$(".base-version").submit(function(e) {
	e.preventDefault();
	$.ajax({
		type: "POST",
		url: "{{ URL::to('modpack/modify/base-version') }}",
		data: $(this).serialize(),
		success: function (data) {
			console.log(data.reason);
			if(data.status == 'success'){
				$("#success-ajax").stop(true, true).html("Modpack Updated").fadeIn().delay(2000).fadeOut();
			} else if(data.status == 'failed') {
				$("#warning-ajax").stop(true, true).html("Unable to update modpack").fadeIn().delay(2000).fadeOut();
			}
		},
		error: function (xhr, textStatus, errorThrown) {
			$("#danger-ajax").stop(true, true).html(textStatus + ': ' + errorThrown).fadeIn().delay(3000).fadeOut();
		}
	});
});

$(".mod-delete").submit(function(e) {
	e.preventDefault();
	$.ajax({
		type: "POST",
		url: "{{ URL::to('modpack/modify/delete') }}",
		data: $(this).serialize(),
		success: function (data) {
			console.log(data.reason);
			if(data.status == 'success'){
				$("#success-ajax").stop(true, true).html("Modversion Deleted").fadeIn().delay(2000).fadeOut();
			} else {
				$("#warning-ajax").stop(true, true).html("Unable to delete modversion").fadeIn().delay(2000).fadeOut();
			}
		},
		error: function (xhr, textStatus, errorThrown) {
			$("#danger-ajax").stop(true, true).html(textStatus + ': ' + errorThrown).fadeIn().delay(3000).fadeOut();
		}
	});
	$(this).parent().parent().fadeOut();
});

$(".base-delete").submit(function(e) {
	e.preventDefault();
	$.ajax({
		type: "POST",
		url: "{{ URL::to('modpack/modify/base-delete') }}",
		data: $(this).serialize(),
		success: function (data) {
			console.log(data.reason);
			if(data.status == 'success'){
				$("#success-ajax").stop(true, true).html("Modpack Deleted").fadeIn().delay(2000).fadeOut();
			} else {
				$("#warning-ajax").stop(true, true).html("Unable to delete Modpack").fadeIn().delay(2000).fadeOut();
			}
		},
		error: function (xhr, textStatus, errorThrown) {
			$("#danger-ajax").stop(true, true).html(textStatus + ': ' + errorThrown).fadeIn().delay(3000).fadeOut();
		}
	});
	$(this).parent().parent().fadeOut();
});

$(".mod-add").submit(function(e) {
	e.preventDefault();
	if($("#mod-version").val()){
		$.ajax({
			type: "POST",
			url: "{{ URL::to('modpack/modify/add') }}",
			data: $(this).serialize(),
			success: function (data) {
				if(data.status == 'success'){
					$("#mod-list-add").after('<tr><td>' + data.pretty_name + '</td><td>' + data.version + '</td><td></td></tr>');
					//$("#success-ajax").stop(true, true).html("Mod " + data.pretty_name + " added at " + data.version).fadeIn().delay(2000).fadeOut();
				} else {
					$("#warning-ajax").stop(true, true).html("Unable to add mod. Reason: " + data.reason).fadeIn().delay(2000).fadeOut();
				}
			},
			error: function (xhr, textStatus, errorThrown) {
				$("#danger-ajax").stop(true, true).html(textStatus + ': ' + errorThrown).fadeIn().delay(3000).fadeOut();
			}
		});
	} else {
		$("#warning-ajax").stop(true, true).html("Please select a Modversion").fadeIn().delay(2000).fadeOut();
	}
});

$(".base-add").submit(function(e) {
	e.preventDefault();
	if($("#base-build").val()){
		$.ajax({
			type: "POST",
			url: "{{ URL::to('modpack/modify/base-add') }}",
			data: $(this).serialize(),
			success: function (data) {
				if(data.status == 'success'){
					$("#base-list-add").after('<tr><td>' + data.name + '</td><td>' + data.version + '</td><td></td></tr>');
					//$("#success-ajax").stop(true, true).html("Mod " + data.pretty_name + " added at " + data.version).fadeIn().delay(2000).fadeOut();
				} else {
					$("#warning-ajax").stop(true, true).html("Unable to add modpack. Reason: " + data.reason).fadeIn().delay(2000).fadeOut();
				}
			},
			error: function (xhr, textStatus, errorThrown) {
				$("#danger-ajax").stop(true, true).html(textStatus + ': ' + errorThrown).fadeIn().delay(3000).fadeOut();
			}
		});
	} else {
		$("#warning-ajax").stop(true, true).html("Please select a Build").fadeIn().delay(2000).fadeOut();
	}
});

function refreshModVersions() {
	modversion.disable();
	modversion.clearOptions();
	$.ajax({
		type: "GET",
		url: "{{ URL::to('api/mod/') }}/" + mod.getValue(),
		success: function (data) {
			if (data.versions.length === 0){
				$("#warning-ajax").stop(true, true).html("No Modversions found for " + data.pretty_name).fadeIn().delay(2000).fadeOut();
				$("#mod-version").attr("placeholder", "No Modversions found...");
			} else {
				$(data.versions).each(function(e, m) {
					modversion.addOption({value: m, text: m});
					modversion.refreshOptions(false);
					$("#mod-version").attr("placeholder", "Select a Modversion...");
				});
			}
		},
		error: function (xhr, textStatus, errorThrown) {
			$("#danger-ajax").stop(true, true).html(textStatus + ': ' + errorThrown).fadeIn().delay(3000).fadeOut();
		}
	});
	modversion.enable();
}

function refreshModpackBuilds() {
	modpackbuild.disable();
	modpackbuild.clearOptions();
	$.ajax({
		type: "GET",
		url: "{{ URL::to('api/modpack/') }}/" + modpack.getValue() + "/?k={{ Config::get('solder.app_key') }}",
		success: function (data) {
			if (data.builds.length === 0){
				$("#warning-ajax").stop(true, true).html("No Builds found for " + data.name).fadeIn().delay(2000).fadeOut();
				$("#modpack-build").attr("placeholder", "No Builds found...");
			} else {
				$(data.builds).each(function(e, m) {
					modpackbuild.addOption({value: m, text: m});
					modpackbuild.refreshOptions(false);
					$("#modpack-build").attr("placeholder", "Select a Build...");
				});
			}
		},
		error: function (xhr, textStatus, errorThrown) {
			$("#danger-ajax").stop(true, true).html(textStatus + ': ' + errorThrown).fadeIn().delay(3000).fadeOut();
		}
	});
	modpackbuild.enable();
}

mod.on('change', refreshModVersions);
modpack.on('change', refreshModpackBuilds);

$( document ).ready(function() {
	$("#modpack-list").dataTable({
			"order": [[ 0, "asc" ]],
			"autoWidth": false,
			"columnDefs": [
			{ "width": "60%", "targets": 0 },
			{ "width": "30%", "targets": 1 }
		]
	});
	$("#mod-list").dataTable({
    	"order": [[ 0, "asc" ]],
    	"autoWidth": false,
    	"columnDefs": [
			{ "width": "60%", "targets": 0 },
			{ "width": "30%", "targets": 1 }
		]
    });
    refreshModVersions();
	refreshModpackBuilds();
});
</script>
@endsection
