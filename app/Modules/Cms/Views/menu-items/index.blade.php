@section('title',__('Cms::menu-items.menu_items'))
@extends('layouts.backoffice')

@section('styles')
    @parent
@endsection

<!--suppress VueDuplicateTag -->

@section('content')

    <div class="content-panel">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">@lang('Cms::menu-items.menu_items')</h1>
                    </div>
                    <div class="col-sm-6">
                        {{ Breadcrumbs::render('menu-items') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Main content --}}
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">

                        <a href="{{ route('menu-items.create') }}" class="btn btn-primary btn-sm mb-3">
                            @icon('fas fa-plus-square')
                            @lang('common.new')
                        </a>

                        <div class="card">
                            <div class="card-body">

                                {{ Form::bsSelect('menu', $menus, null, [], ['label' => __('Cms::menus.menu')]) }}

                                <div id="items-container">
                                    <ul class="list-group" id="items">...</ul>
                                </div>

                                <br>
                                <button id="btn-save-order" class="btn btn-sm btn-success" onclick="saveOrder()">
                                    @icon('fas fa-save')&nbsp;@lang('Cms::menu-items.save_order')
                                </button>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- modal confirm --}}
    @include('layouts.backoffice.modal_confirm')
@endsection

@section('scripts')
    @parent
    <script src="{{ asset('js/jquery-sortable-lists.min.js') }}"></script>
    <script>
        var list = null;
        $(function () {
            var $menu = $('#menu');
            $menu.on('change', function () {
                $.ajax({
                    data: {id: $menu.val()},
                    responseType: 'json',
                    url: "{{ route('menu-items.ajax') }}",
                }).done(createMenu);
            }).trigger('change');

            $('#btn-save-order').on('click', saveOrder);

            // Guardar template inicial
            var $itemsContainer = $('#items-container');
            $itemsContainer.attr('oldHTML', $itemsContainer.html());

        });

        function createMenu(data) {
            var html = '';
            for (var i = 0; i < data.length; i++) {
                if (typeof data[i].children !== 'undefined' && data[i].children.length > 0) {
                    html += createItemWithChildren(data[i]);
                } else {
                    html += createItem(data[i]);
                }
            }

            // Dar reset ao plugin
            var $itemsContainer = $('#items-container');
            $itemsContainer.empty();
            $itemsContainer.html($itemsContainer.attr('oldHTML'));

            // Criar plugin
            var $items = $('#items');
            $items.html(html);
            list = $items.sortableLists({
                placeholderClass: 'bg-warning',
                currElClass: 'border border-dark',
                hintClass: 'bg-info',
                ignoreClass: 'clickable',
                insertZone: 200,
                insertZonePlus: true,
                //hintWrapperClass: 'border border-secondary',
            });
        }

        function createItem(item) {
            var html = '';
            html += '<li id="' + item.id + '" class="list-group-item">';
            html += '<div>';
            html += item.translation.display_name;
            html += SimpleHTMLElement('button', {
                type: 'submit',
                method: 'delete',
                class: 'btn btn-sm btn-danger float-right clickable',
                data_id: item.id,
                onclick: 'removeMenuItem(this)'
            }, '<i class="fas fa-trash-alt clickable"></i>');
            html += SimpleHTMLElement('a', {
                class: 'btn btn-sm btn-warning float-right clickable',
                href: generateRoute('{!! route('menu-items.edit', ':id') !!}', item.id)
            }, '<i class="fas fa-edit clickable"></i>');
            html += '</div>';
            html += '</li>';
            return html;
        }

        function createItemWithChildren(item) {
            var html = '';
            html += '<li id="' + item.id + '" class="list-group-item">';
            html += '<div>';
            html += item.translation.display_name;
            html += SimpleHTMLElement('a', {
                class: 'btn btn-sm btn-warning float-right clickable',
                href: generateRoute('{!! route('menu-items.edit', ':id') !!}', item.id)
            }, '<i class="fas fa-edit clickable"></i>');
            html += '</div>';
            html += '<ul class="list-group">';
            for (var i = 0; i < item.children.length; i++) {
                if (typeof item.children[i].children !== 'undefined' && item.children[i].children.length > 0) {
                    html += createItemWithChildren(item.children[i]);
                } else {
                    html += createItem(item.children[i]);
                }
            }
            html += '</ul>';
            html += '</li>';
            return html;
        }

        function removeMenuItem(element) {

            var $self = $(element);

            // Obter dados
            var id = $self.attr('data-id');
            var dataAction = '{!! json_encode(['route' => ['menu-items.destroy', ':id'], 'method' => 'delete', 'class' => 'd-inline']) !!}';
            dataAction = JSON.parse(dataAction);
            dataAction['route'][1] = dataAction['route'][1].replace(':id', id);
            dataAction = JSON.stringify(dataAction);

            // Simular modal de confirmação
            $self.attr('data-toggle', 'modal');
            $self.attr('data-action', dataAction);
            $self.attr('data-target', '#modal_confirm');
            $self.removeAttr('onclick');

            $self.trigger('click');
        }

        function saveOrder() {
            var $self = $(this);
            $self.attr('oldHTML', $self.html());

            $.ajax({
                data: {
                    items: $('#items').sortableListsToArray()
                },
                responseType: 'json',
                url: "{{ route('menu-items.save_order') }}",
                beforeSend: function () {
                    $self.removeClass('btn-danger').addClass('btn-success');
                    $self.html('...');
                }
            }).done(function () {
                $self.html($self.attr('oldHTML'));
            }).fail(function () {
                $self.html($self.attr('oldHTML'));
                $self.removeClass('btn-success').addClass('btn-danger');
            });
        }

        function generateRoute(route, id) {
            if (route.includes(':id')) {
                route = route.replace(':id', id);
            }
            return route;
        }

        // Delete confirmation modal
        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');

    </script>
@endsection
