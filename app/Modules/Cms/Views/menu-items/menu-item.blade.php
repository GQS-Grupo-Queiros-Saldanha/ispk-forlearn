@switch($action)
    @case('create') @section('title',__('Cms::menu-items.create_menu_item')) @break
@case('show') @section('title',__('Cms::menu-items.menu_item')) @break
@case('edit') @section('title',__('Cms::menu-items.edit_menu_item')) @break
@endswitch

@extends('layouts.backoffice')

@section('content')
    @include('layouts.backoffice.modal_confirm')

    <div class="content-panel">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">
                            @switch($action)
                                @case('create') @lang('Cms::menu-items.create_menu_item') @break
                                @case('show') @lang('Cms::menu-items.menu_item') @break
                                @case('edit') @lang('Cms::menu-items.edit_menu_item') @break
                            @endswitch
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        @switch($action)
                            @case('create') {{ Breadcrumbs::render('menu-items.create') }} @break
                            @case('show') {{ Breadcrumbs::render('menu-items.show', $menu_item) }} @break
                            @case('edit') {{ Breadcrumbs::render('menu-items.edit', $menu_item) }} @break
                        @endswitch
                    </div>
                </div>
            </div>
        </div>

        {{-- Main content --}}
        <div class="content">
            <div class="container-fluid">

                @switch($action)
                    @case('create')
                    {!! Form::open(['route' => ['menu-items.store']]) !!}
                    @break
                    @case('show')
                    {!! Form::model($menu_item) !!}
                    @break
                    @case('edit')
                    {!! Form::model($menu_item, ['route' => ['menu-items.update', $menu_item->id], 'method' => 'put']) !!}
                    @break
                @endswitch

                <div class="row">
                    <div class="col">

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                    ×
                                </button>
                                <h5>@choice('common.error', $errors->count())</h5>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @switch($action)
                            @case('create')
                            <button type="submit" class="btn btn-sm btn-success mb-3">
                                @icon('fas fa-plus-circle')
                                @lang('common.create')
                            </button>
                            @break
                            @case('edit')
                            <button type="submit" class="btn btn-sm btn-success mb-3">
                                @icon('fas fa-save')
                                @lang('common.save')
                            </button>
                            @break
                            @case('show')
                            <a href="{{ route('menu-items.edit', $menu_item->id) }}"
                               class="btn btn-sm btn-warning mb-3">
                                @icon('fas fa-edit')
                                @lang('common.edit')
                            </a>
                            @break
                        @endswitch

                        <div class="card">
                            <div class="card-body">

                                <div class="container-fuid">
                                    <div class="row">
                                        <div class="col-12 col-sm-8">
                                            {{ Form::bsText('code', null, ['placeholder' => __('common.code'), 'disabled' => $action === 'show', 'required'], ['label' => __('common.code')]) }}

                                            <div class="form-group col">
                                                <label for="menus_id">@lang('Cms::menus.menu')</label>
                                                {{ Form::bsLiveSelect('menus_id', $menus, null, ['disabled' => $action === 'show', 'required']) }}
                                            </div>

                                            {{ Form::bsText('external_link', null, ['placeholder' => __('Cms::menu-items.external_link'), 'disabled' => $action === 'show', 'title' => 'Se for link interno tem que começar por /'], ['label' => __('Cms::menu-items.external_link')]) }}

                                            {{--<div class="form-group col">
                                                <label for="icon">@lang('Cms::menu-items.icon')</label>
                                                {{ Form::bsLiveSelectHTML('icon', $icons, null, ['disabled' => $action === 'show']) }}
                                            </div>--}}

                                            <div class="form-group col">
                                                <label for="parent_id">@lang('Cms::menu-items.parent')</label>
                                                {{ Form::bsLiveSelect('parent_id', $menu_items, null, ['disabled' => $action === 'show']) }}
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group">
                                                <label>@lang('Cms::menu-items.necessary_permissions')</label>
                                                <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#modal-permissions">
                                                    @lang('common.add') +
                                                </button>
                                                <table id="table-permissions">
                                                    @if(in_array($action, ['view', 'edit']))
                                                        @foreach($menu_item->permissions as $permission)
                                                            <tr>
                                                                <td>
                                                                    <input type="hidden" name="permissions[]" value="{{ $permission->id }}">{{ $permission->translation->display_name }}
                                                                </td>
                                                                <td>
                                                                    <button class="btn btn-sm btn-danger" onclick="$(this).parents('tr').remove();">
                                                                        X
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Translations -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex p-0">
                                <h3 class="card-title p-3">@lang('translations.languages')</h3>
                                <ul class="nav nav-pills ml-auto p-2">
                                    @foreach($languages as $language)
                                        <li class="nav-item">
                                            <a class="nav-link @if($language->default) active show @endif"
                                               href="#language{{ $language->id }}"
                                               data-toggle="tab">{{ $language->name }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <div class="card-body">
                                <div class="tab-content">
                                    @foreach($languages as $language)
                                        <div class="tab-pane row @if($language->default) active show @endif" id="language{{ $language->id }}">
                                            {{ Form::bsText('display_name['.$language->id.']', $action === 'create' ? old('display_name.'.$language->id) : $translations[$language->id]['display_name'] ?? null, ['placeholder' => __('translations.display_name'), 'disabled' => $action === 'show', !$language->default ?: 'required'], ['label' => __('translations.display_name')]) }}
                                            {{ Form::bsText('description['.$language->id.']', $action === 'create' ? old('description.'.$language->id) : $translations[$language->id]['description'] ?? null, ['placeholder' => __('translations.description'), 'disabled' => $action === 'show', !$language->default ?: 'required'], ['label' => __('translations.description')]) }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {!! Form::close() !!}

            </div>
        </div>
    </div>
@endsection

{{-- Modal permissões --}}
<div class="modal fade" id="modal-permissions" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table id="permissions-table" class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th>@lang('translations.display_name')</th>
                    </tr>
                    </thead>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('common.cancel')</button>
            </div>
        </div>
    </div>
</div>

@section('scripts')
    @parent
    <script>
        $(function () {

            let selected = [];

            let table = $('#permissions-table').DataTable({
                ajax: '{!! route('permissions.ajax') !!}',
                buttons: false,
                columns: [{
                    data: 'display_name',
                    name: 'pt.display_name'
                }],
                language: {
                    url: '{{ asset('lang/datatables/'.App::getLocale().'.json') }}',
                }
            });

            table.on('click', 'tbody tr', function () {

                let item = table.row(this).data();

                let $self = $(this);
                if (!$self.data('selected')) {
                    selected.push(item);
                    $(this).data('selected', true);
                    $(this).addClass('table-info');
                } else {
                    selected = $.grep(selected, function (k, v) {
                        return v.id === item.id;
                    });
                    $(this).removeClass('table-info');
                    $(this).data('selected', false);
                }

                savePermissions(selected);
            });

            $('#menus_id').on('change', getItems).trigger('change');
        });

        function getItems() {

            let $self = $(this);

            $.ajax({
                data: {
                    menus_id: $self.val()
                },
                responseType: 'json',
                url: "{{ route('menus.items.ajax') }}",
            }).done(function (items) {

                let html = '';
                html += '<option value=""></option>';
                $.each(items, function (k, v) {
                    html += '<option value="' + v.id + '"';

                    @if(in_array($action, ['show', 'edit']))
                        if (v.id === parseInt('{{ $menu_item->parent_id }}')) {
                            html += ' selected ';
                        }
                    @endif

                    html += '>';
                    html += v.translation.display_name;
                    html += '</option>';
                });

                let $parentId = $('#parent_id');
                $parentId.html(html);
                $parentId.selectpicker('refresh');

            });
        }

        function savePermissions(items) {
            let html = '';
            for (let i = 0; i < items.length; i++) {
                html += '<tr>';
                html += '<td>';
                html += SimpleHTMLElement('input', {
                    type: 'hidden',
                    name: 'permissions[]',
                    value: items[i].id
                });
                html += items[i].display_name;
                html += '</td>';
                html += '<td>';
                html += '<button class="btn btn-sm btn-danger" onclick="$(this).parents(\'tr\').remove();">X</button>';
                html += '</td>';
                html += '</tr>';
            }

            $('#table-permissions').html(html);
        }
    </script>
@endsection
