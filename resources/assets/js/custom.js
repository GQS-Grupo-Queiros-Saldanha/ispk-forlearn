//================================================================================
// Settings
//================================================================================

// Datatables
$.extend(true, $.fn.dataTable.defaults, {
    dom: 'Bfrltip',
    processing: true,
    serverSide: true,
    stateSave: true,
    columnDefs: [
        {
            targets: '_all',
            defaultContent: '<span style="color: #aaa">N/A</span>'
        }
    ],
    conditionalPaging: true,

    // Plugins
    mark: true,
    responsive: true,
    buttons: ['colvis', 'copy', 'print', 'csv', 'excel', 'pdf']
});
$.fn.dataTable.ext.errMode = 'throw';

// jQuery ajax
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

//================================================================================
// Classes
//================================================================================

window.Modal = {
    /**
     * Abre modal de confirmação
     * @param url
     * @param token
     */
    confirm: function (url, token) {
        $(document).on('click', 'button[data-target*=\'#modal_confirm\']', function () {
            var data = JSON.parse($(this).attr('data-action'));

            var $modalConfirm = $('#modal_confirm');
            var $form = $modalConfirm.find('form');
            var $inputMethod = $modalConfirm.find('input[name=_method]');
            var $inputToken = $modalConfirm.find('input[name=_token]');

            $form.attr('action', url + data.route[1]);
            $inputMethod.val(data.method.toUpperCase());
            $inputToken.val(token);
        });
    }
};

// exception list for isValidSelector to prevent deletion of certain localStorage entries.
function isValidSelectorException(selector) {
    return selector.includes("selectedUser");
}

window.Utils = {
    getTextFromSelect: function (element, value) {
        var text = '';
        $(element).children().each(function (k, v) {
            var $self = $(v);
            if ($self.val() == value) {
                text = $self.text();
            }
        });
        return text;
    },

    isValidSelector: function (selector) {
        if (typeof (selector) !== 'string') {
            return false;
        }

        if (isValidSelectorException(selector)) {
            return false;
        }

        try {
            $(selector);
        } catch (error) {
            return false;
        }

        return true;
    },

    stripTags: function (string) {
        return string.replace(/<[^>]*>?/gm, '');
    },

    setSelectedUserOnLoad: function (sessionKey, selectElement) {
        var userId = selectElement ? parseInt(selectElement.value) : null;

        var userInSessionStorage = sessionStorage.getItem(sessionKey);
        if (userInSessionStorage) {
            userId = userInSessionStorage;
        }

        // for localStorage to work the sessionKey needs to be added to isValidSelectorException
        var userInLocalStorage = localStorage.getItem(sessionKey);
        if (userInLocalStorage) {
            userId = userInLocalStorage;
        }

        return userId;
    },

    updatedSelectedUserInSession: function (sessionKey, selectedUser) {
        // for localStorage to work the sessionKey needs to be added to isValidSelectorException
        if (selectedUser) {
            sessionStorage.setItem(sessionKey, selectedUser);
            localStorage.setItem(sessionKey, selectedUser);
        } else {
            sessionStorage.removeItem(sessionKey);
            localStorage.removeItem(sessionKey);
        }
    }
};

window.classCallCheck = function (instance, Constructor) {
    if (!(instance instanceof Constructor)) {
        throw new TypeError('Cannot call a class as a function');
    }
};

window.Treeview = function ($) {
    /**
     * Constants
     * ====================================================
     */
    var NAME = 'Treeview';
    var DATA_KEY = 'lte.treeview';
    var EVENT_KEY = '.' + DATA_KEY;
    var JQUERY_NO_CONFLICT = $.fn[NAME];

    var Event = {
        SELECTED: 'selected' + EVENT_KEY,
        EXPANDED: 'expanded' + EVENT_KEY,
        COLLAPSED: 'collapsed' + EVENT_KEY,
        LOAD_DATA_API: 'load' + EVENT_KEY
    };

    var Selector = {
        LI: '.nav-item',
        LINK: '.nav-link',
        TREEVIEW_MENU: '.nav-treeview',
        OPEN: '.menu-open',
        DATA_WIDGET: '[data-widget="treeview"]'
    };

    var ClassName = {
        LI: 'nav-item',
        LINK: 'nav-link',
        TREEVIEW_MENU: 'nav-treeview',
        OPEN: 'menu-open'
    };

    var Default = {
        trigger: Selector.DATA_WIDGET + ' ' + Selector.LINK,
        animationSpeed: 300,
        accordion: true

        /**
         * Class Definition
         * ====================================================
         */
    };
    var Treeview = function () {
        function Treeview(element, config) {
            classCallCheck(this, Treeview);

            this._config = config;
            this._element = element;
        }

        // Public

        Treeview.prototype.init = function init() {
            this._setupListeners();
        };

        Treeview.prototype.expand = function expand(treeviewMenu, parentLi) {
            var _this = this;

            var expandedEvent = $.Event(Event.EXPANDED);

            if (this._config.accordion) {
                var openMenuLi = parentLi.siblings(Selector.OPEN).first();
                var openTreeview = openMenuLi.find(Selector.TREEVIEW_MENU).first();
                this.collapse(openTreeview, openMenuLi);
            }

            treeviewMenu.slideDown(this._config.animationSpeed, function () {
                parentLi.addClass(ClassName.OPEN);
                $(_this._element).trigger(expandedEvent);
            });
        };

        Treeview.prototype.collapse = function collapse(treeviewMenu, parentLi) {
            var _this2 = this;

            var collapsedEvent = $.Event(Event.COLLAPSED);

            treeviewMenu.slideUp(this._config.animationSpeed, function () {
                parentLi.removeClass(ClassName.OPEN);
                $(_this2._element).trigger(collapsedEvent);
                treeviewMenu.find(Selector.OPEN + ' > ' + Selector.TREEVIEW_MENU).slideUp();
                treeviewMenu.find(Selector.OPEN).removeClass(ClassName.OPEN);
            });
        };

        Treeview.prototype.toggle = function toggle(event) {
            var $relativeTarget = $(event.currentTarget);
            var treeviewMenu = $relativeTarget.next();

            if (!treeviewMenu.is(Selector.TREEVIEW_MENU)) {
                return;
            }

            event.preventDefault();

            var parentLi = $relativeTarget.parents(Selector.LI).first();
            var isOpen = parentLi.hasClass(ClassName.OPEN);

            if (isOpen) {
                this.collapse($(treeviewMenu), parentLi);
            } else {
                this.expand($(treeviewMenu), parentLi);
            }
        };

        // Private

        Treeview.prototype._setupListeners = function _setupListeners() {
            var _this3 = this;

            $(document).on('click', this._config.trigger, function (event) {
                _this3.toggle(event);
            });
        };

        // Static

        Treeview._jQueryInterface = function _jQueryInterface(config) {
            return this.each(function () {
                var data = $(this).data(DATA_KEY);
                var _config = $.extend({}, Default, $(this).data());

                if (!data) {
                    data = new Treeview($(this), _config);
                    $(this).data(DATA_KEY, data);
                }

                if (config === 'init') {
                    data[config]();
                }
            });
        };

        return Treeview;
    }();

    /**
     * Data API
     * ====================================================
     */

    $(window).on(Event.LOAD_DATA_API, function () {
        $(Selector.DATA_WIDGET).each(function () {
            Treeview._jQueryInterface.call($(this), 'init');
        });
    });

    /**
     * jQuery API
     * ====================================================
     */

    $.fn[NAME] = Treeview._jQueryInterface;
    $.fn[NAME].Constructor = Treeview;
    $.fn[NAME].noConflict = function () {
        $.fn[NAME] = JQUERY_NO_CONFLICT;
        return Treeview._jQueryInterface;
    };

    return Treeview;
}(jQuery);

window.DynamicDatatable = class DynamicDatatable {

    /**
     * Constructor
     * @param parent_element Parent element where html will be added
     * @param table_id Table identification
     * @param columns Header columns
     * @param rows Used for initial data only
     * @param delete_text Delete text header column
     * @param modal_type Modal where will read the input values
     * @param action Show/edit/create
     */
    constructor(parent_element, table_id, columns, rows, delete_text, modal_type, action) {
        this.parent_element = parent_element;
        this.table_id = table_id;
        this.columns = columns;
        this.rows = rows;
        this.delete_text = delete_text;
        this.modal_type = modal_type;
        this.action = action;
    }

    addButtons(modalType, rowInMemory = false) {
        var html = '<td style=\'vertical-align: middle;text-align: right;\'><button type=\'button\' data-type=\'delete\' class=\'btn btn-sm btn-danger delete\'><i class=\'dynamic-datatable removebutton fas fa-trash-alt\'></i></button></td>';

        if (modalType === 'modal_transaction') {
            html = "<td style=\'vertical-align: middle;text-align: right;\'>";

            if (rowInMemory) {
                html += "<button type=\'button\' data-type=\'delete\' class=\'btn btn-sm btn-danger delete\'><i class=\'dynamic-datatable removebutton fas fa-trash-alt\'></i></button>";
            } else {
                // "<button type=\'button\' data-type=\'edit\' class=\'btn btn-warning btn-sm edit\'><i class=\'dynamic-datatable editbutton fas fa-edit\'></i></button> " +
                html += "<button type=\'button\' data-type=\'delete\' class=\'btn btn-sm btn-danger refund\'><i class=\'dynamic-datatable removebutton fas fa-undo\'></i></button>";
            }
            html += "</td>";
        }

        return html;
    };

    formSubmitOrder(columns, options) {
        return options.sort(function (a, b) {
            const columnsIndexA = columns.findIndex(function (c) {
                var cName = c.name.substr(-2) === '[]' ? c.name.slice(0, -2) : c.name;
                var aName = a.name.substr(-2) === '[]' ? a.name.slice(0, -2) : a.name;
                return cName === aName;
            });
            const columnsIndexB = columns.findIndex(function (c) {
                var cName = c.name.substr(-2) === '[]' ? c.name.slice(0, -2) : c.name;
                var bName = b.name.substr(-2) === '[]' ? b.name.slice(0, -2) : b.name;
                return cName === bName;
            });
            if (columnsIndexA < columnsIndexB) {
                return -1;
            }
            if (columnsIndexA > columnsIndexB) {
                return 1;
            }
            return 0;
        });
    };

    formSubmit() {

        var $modal = $('#' + this.modal_type);
        var selects = $modal.find('select');
        var inputs = $('#' + this.modal_type + ' div:not(.bs-searchbox) > input:enabled:not([readonly]):not(:checkbox)');
        var textAreas = $('#' + this.modal_type + ' div:not(.bs-searchbox) > textarea:enabled:not([readonly]):not(:checkbox)');
        var checkbox = $modal.find('input:checked:checkbox:enabled:not([readonly])');
        var options = [];

        $.each(selects, function (k, v) {
            var $self = $(v);
            options.push({
                'text': Utils.getTextFromSelect(v, $self.val()),
                'value': $self.val(),
                'name': $self.attr('name'),
                'group': $self.attr('group')
            });
        });

        $.each(checkbox, function (k, v) {
            var $self = $(v);
            options.push({
                'text': typeof $self.attr('text') !== 'undefined' ? $self.attr('text') : $self.attr('placeholder'),
                'value': $self.val(),
                'name': $self.attr('name'),
                'group': $self.attr('group')
            });
        });

        $.each(inputs, function (k, v) {
            var $self = $(v);
            options.push({
                'text': $self.val(),
                'value': $self.val(),
                'name': $self.attr('name'),
                'group': $self.attr('group')
            });
        });

        $.each(textAreas, function (k, v) {
            var $self = $(v);
            options.push({
                'text': $self.val(),
                'value': $self.val(),
                'name': $self.attr('name'),
                'group': $self.attr('group')
            });
        });

        var orderedOptions = this.formSubmitOrder(this.columns, options);
        this.addRow(orderedOptions);
        $modal.modal('hide');
    };

    addEvents() {
        var deleteRow = this.deleteRow;
        var dt = this;

        $('body')
            .on('click', '#' + this.table_id + ' button.delete', function () {
                var $this = this;
                $('#modal_delete_simple').modal('show');
                $('#button_simple_modal_delete')
                    .unbind()
                    .on('click', function () {
                        deleteRow($this, dt);
                        $('#button_simple_modal_delete').modal('hide');
                    });
            })
            // .on('click', '#' + dt.table_id + ' button.edit', function () {
            //     typeof editRowFromTable !== 'undefined' ? editRowFromTable(dt.table_id) : null;
            // })
            .on('click', '#' + dt.table_id + ' button.refund', function () {
                var $this = this;
                $('#modal_refund').modal('show');
                $('#button_modal_refund')
                    .unbind()
                    .on('click', function () {
                        var table = $($this).parent().parent();
                        typeof refundRowFromTable !== 'undefined' ? refundRowFromTable(table) : null;
                        $('#button_modal_refund').modal('hide');
                    })
            });
    };

    // Adding a method to the constructor
    initialize() {
        var modalType = this.modal_type;
        var addbuttons = this.action === 'show' ? '' : this.addButtons(modalType, false);

        var html = '';
        html += '<table class=\'table table-hover\' id=\'' + this.table_id + '\'><thead><tr>';

        //create columns
        $.each(this.columns, function (k, v) {
            html += '<th><span>' + v.text + '</span></th>';
        });

        if (this.action !== 'show') {
            html += '<th style=\'text-align: right;\'>';
            html += '<span>' + this.delete_text + '</span>';
            html += '</th>';
        }

        html += '</tr></thead><tbody>';

        //crate initial rows if they exists
        if (typeof this.rows !== 'undefined') {
            $.each(this.rows, function (k, v) {
                html += '<tr>';
                $.each(v, function (j, i) {
                    if (i instanceof Array) {
                        html += '<td><ul>';
                        $.each(i, function (c, e) {
                            html += '<li><span>' + e.text + '</span><input type=\'hidden\' name=\'' + e.name.replace('[]', '') + '_' + (k) + '_[]\' value=\'' + e.value + '\'></li>';
                        });
                        html += '</ul></td>';
                    } else {
                        html += '<td class=\'inner-content\'><span>' + i.text + '</span><input type=\'hidden\' name=\'' + i.name + '\' value=\'' + i.value + '\'></td>';
                    }
                });
                html += addbuttons;
                html += '</tr>';
            });
        }

        html += '</tbody></table>';

        $(this.parent_element).html(html);

        if (this.action === 'create') {
            if (localStorage.getItem(this.parent_element) != null) {
                if (localStorage.getItem(this.parent_element).length > 0) {
                    $(this.parent_element).find('tbody').html(localStorage.getItem(this.parent_element));
                }
            }
        }

        this.addEvents();
    };

    addRow(data) {
        try {
            var $table = $('#' + this.table_id);
            var tableID = this.table_id;
            var tbody = $table.find('tbody');
            var totalRows = $table.find('tr').length;
            var html = '<tr>';
            var innerElements = [],
                uniqueElements = [];

            $.each(data, function (k, v) {
                if (v.name.indexOf('[]') > -1) {
                    innerElements.push(v);
                    uniqueElements.push(v.name);
                }
            });

            uniqueElements = unique(uniqueElements);

            $.each(data, function (k, v) {
                if (v.name.indexOf('[]') == -1) {
                    var text = tableID === 'table_transactions' && v.text === "" ? "-" : v.text;

                    html += '<td><span>' + text + '</span><input type=\'hidden\' name=\'' + v.name + '[]\' value=\'' + v.value + '\'></td>';
                }
            });

            $.each(uniqueElements, function (u, e) {
                html += '<td><ul>';
                $.each(innerElements, function (j, i) {
                    if (i.name === e) html += '<li><span>' + i.text + '</span><input type=\'hidden\' name=\'' + i.name.replace('[]', '') + '_' + (totalRows - 1) + '_[]\' value=\'' + i.value + '\'></li>';
                });
                html += '</ul></td>';
            });

            html += this.addButtons(this.modal_type, true);
            html += '</tr>';

            $(tbody).append(html);
            this.rows.push(data);

            localStorage.setItem(this.parent_element, $(tbody).html());

        } catch (ex) {

        }

    };

    deleteRow(element, dt) {
        var row = $(element).parent().parent();

        var rowIndex = row[0].rowIndex - 1;
        if (rowIndex > -1) {
            dt.rows.splice(rowIndex, 1);
        }

        row.remove();

        typeof deletedRowFromModal !== 'undefined' ? deletedRowFromModal(dt.table_id) : null
    };
};

window.DynamicDatatableTransactions = class DynamicDatatable {

    /**
     * Constructor
     * @param parent_element Parent element where html will be added
     * @param table_id Table identification
     * @param columns Header columns
     * @param rows Used for initial data only
     * @param delete_text Delete text header column
     * @param modal_type Modal where will read the input values
     * @param action Show/edit/create
     */
    constructor(parent_element, table_id, columns, rows, delete_text, modal_type, action) {
        this.parent_element = parent_element;
        this.table_id = table_id;
        this.columns = columns;
        this.rows = rows;
        this.delete_text = delete_text;
        this.modal_type = modal_type;
        this.action = action;
    }

    addButtons(modalType, rowInMemory = false, showReceipt = null) {
        var html = '<td style=\'vertical-align: middle;text-align: right;\'><button type=\'button\' data-type=\'delete\' class=\'btn btn-sm btn-danger delete\'><i class=\'dynamic-datatable removebutton fas fa-trash-alt\'></i></button></td>';

        if (modalType === 'modal_transaction') {
            html = "<td style=\'vertical-align: middle;text-align: right;\'>";

            if (rowInMemory) {
                html += "<button type=\'button\' data-type=\'delete\' class=\'btn btn-sm btn-danger delete\'><i class=\'dynamic-datatable removebutton fas fa-trash-alt\'></i></button>";
            } else {
                html += "<button type=\'button\' data-type=\'delete\' class=\'btn btn-sm btn-danger refund\'><i class=\'dynamic-datatable removebutton fas fa-undo\'></i></button>";

                if (showReceipt) {
                    html += "<button type=\'button\' data-type=\'receipt\' data-transaction=\'" + showReceipt + "\' class=\'btn btn-sm btn-info receipt ml-1\'><i class=\'dynamic-datatable receiptbutton fas fa-receipt\'></i></button>";
                }
            }
            html += "</td>";
        }

        return html;
    };

    formSubmitOrder(columns, options) {
        return options.sort(function (a, b) {
            const columnsIndexA = columns.findIndex(function (c) {
                return c.name.slice(0, -2) === a.name;
            });
            const columnsIndexB = columns.findIndex(function (c) {
                return c.name.slice(0, -2) === b.name;
            });
            return columnsIndexA - columnsIndexB;
        });
    };

    formSubmit() {

        var $modal = $('#' + this.modal_type);
        var selects = $modal.find('select');
        var inputs = $('#' + this.modal_type + ' div:not(.bs-searchbox) > input:enabled:not([readonly]):not(:checkbox)');
        var textAreas = $('#' + this.modal_type + ' div:not(.bs-searchbox) > textarea:enabled:not([readonly]):not(:checkbox)');
        var checkbox = $modal.find('input:checked:checkbox:enabled:not([readonly])');
        var options = [];

        $.each(selects, function (k, v) {
            var $self = $(v);
            options.push({
                'text': Utils.getTextFromSelect(v, $self.val()),
                'value': $self.val(),
                'name': $self.attr('name'),
                'group': $self.attr('group')
            });
        });

        $.each(checkbox, function (k, v) {
            var $self = $(v);
            options.push({
                'text': typeof $self.attr('text') !== 'undefined' ? $self.attr('text') : $self.attr('placeholder'),
                'value': $self.val(),
                'name': $self.attr('name'),
                'group': $self.attr('group')
            });
        });

        $.each(inputs, function (k, v) {
            var $self = $(v);
            options.push({
                'text': $self.val(),
                'value': $self.val(),
                'name': $self.attr('name'),
                'group': $self.attr('group')
            });
        });

        $.each(textAreas, function (k, v) {
            var $self = $(v);
            options.push({
                'text': $self.val(),
                'value': $self.val(),
                'name': $self.attr('name'),
                'group': $self.attr('group')
            });
        });

        var orderedOptions = this.formSubmitOrder(this.columns, options);
        this.addRow(orderedOptions);
        $modal.modal('hide');
    };

    addEvents() {
        var deleteRow = this.deleteRow;
        var dt = this;

        $('body')
            .on('click', '#' + this.table_id + ' button.delete', function () {
                var $this = this;
                $('#modal_delete_simple').modal('show');
                $('#button_simple_modal_delete')
                    .unbind()
                    .on('click', function () {
                        deleteRow($this, dt);
                        $('#button_simple_modal_delete').modal('hide');
                    });
            })
            // .on('click', '#' + dt.table_id + ' button.edit', function () {
            //     typeof editRowFromTable !== 'undefined' ? editRowFromTable(dt.table_id) : null;
            // })
            .on('click', '#' + dt.table_id + ' button.refund', function () {
                var $this = this;
                $('#modal_refund').modal('show');
                $('#button_modal_refund')
                    .unbind()
                    .on('click', function () {
                        var table = $($this).parent().parent();
                        typeof refundRowFromTable !== 'undefined' ? refundRowFromTable(table) : null;
                        $('#button_modal_refund').modal('hide');
                    })
            })
            .on('click', '#' + dt.table_id + ' button.receipt', function () {
                var elementData = $(this).data();
                var transactionId = elementData && elementData.transaction ? elementData.transaction : null;
                typeof generateReceiptForTransaction !== 'undefined' ? generateReceiptForTransaction(transactionId) : null
            });
    };

    // Adding a method to the constructor
    initialize() {
        var modalType = this.modal_type;
        var _this = this;
        // var addbuttons = this.action === 'show' ? '' : this.addButtons(modalType, false, true);

        var html = '';
        html += '<table class=\'table table-hover\' id=\'' + this.table_id + '\'><thead><tr>';

        //create columns
        $.each(this.columns, function (k, v) {
            html += '<th><span>' + v.text + '</span></th>';
        });

        if (this.action !== 'show') {
            html += '<th style=\'text-align: right;\'>';
            html += '<span>' + this.delete_text + '</span>';
            html += '</th>';
        }

        html += '</tr></thead><tbody>';

        //crate initial rows if they exists
        if (typeof this.rows !== 'undefined') {
            $.each(this.rows, function (k, v) {
                html += '<tr>';
                $.each(v, function (j, i) {
                    if (i instanceof Array) {
                        html += '<td><ul>';
                        $.each(i, function (c, e) {
                            html += '<li><span>' + e.text + '</span><input type=\'hidden\' name=\'' + e.name.replace('[]', '') + '_' + (k) + '_[]\' value=\'' + e.value + '\'></li>';
                        });
                        html += '</ul></td>';
                    } else {
                        html += '<td class=\'inner-content\'><span>' + i.text + '</span><input type=\'hidden\' name=\'' + i.name + '\' value=\'' + i.value + '\'></td>';
                    }
                });

                var paymentsRowTransactionID = v[1].value === 'payment' ? v[0].value : null;
                html += _this.action === 'show' ? '' : _this.addButtons(modalType, false, paymentsRowTransactionID);
                html += '</tr>';
            });
        }

        html += '</tbody></table>';

        $(this.parent_element).html(html);

        if (this.action === 'create') {
            if (localStorage.getItem(this.parent_element) != null) {
                if (localStorage.getItem(this.parent_element).length > 0) {
                    $(this.parent_element).find('tbody').html(localStorage.getItem(this.parent_element));
                }
            }
        }

        this.addEvents();
    };

    addRow(data) {
        try {
            var $table = $('#' + this.table_id);
            var tableID = this.table_id;
            var tbody = $table.find('tbody');
            var totalRows = $table.find('tr').length;
            var html = '<tr>';
            var innerElements = [],
                uniqueElements = [];

            $.each(data, function (k, v) {
                if (v.name.indexOf('[]') > -1) {
                    innerElements.push(v);
                    uniqueElements.push(v.name);
                }
            });

            uniqueElements = unique(uniqueElements);

            $.each(data, function (k, v) {
                if (v.name.indexOf('[]') == -1) {
                    var text = tableID === 'table_transactions' && v.text === "" ? "-" : v.text;

                    html += '<td><span>' + text + '</span><input type=\'hidden\' name=\'' + v.name + '[]\' value=\'' + v.value + '\'></td>';
                }
            });

            $.each(uniqueElements, function (u, e) {
                html += '<td><ul>';
                $.each(innerElements, function (j, i) {
                    if (i.name === e) html += '<li><span>' + i.text + '</span><input type=\'hidden\' name=\'' + i.name.replace('[]', '') + '_' + (totalRows - 1) + '_[]\' value=\'' + i.value + '\'></li>';
                });
                html += '</ul></td>';
            });

            html += this.addButtons(this.modal_type, true, false);
            html += '</tr>';

            $(tbody).append(html);
            this.rows.push(data);

            localStorage.setItem(this.parent_element, $(tbody).html());

        } catch (ex) {

        }

    };

    deleteRow(element, dt) {
        var row = $(element).parent().parent();

        var rowIndex = row[0].rowIndex - 1;
        if (rowIndex > -1) {
            dt.rows.splice(rowIndex, 1);
        }

        row.remove();

        typeof deletedRowFromModal !== 'undefined' ? deletedRowFromModal(dt.table_id) : null
    };
};

window.Forlearn = class Forlearn {

    /**
     * Function that checks if a model exists
     * @param element JQuery element where the bootstrap validation classes will be added
     * @param route Route for the ajax request
     * @param ignored_id [optional] ID to be ignored
     */
    static checkIfModelFieldExists(element, route, ignored_id) {
        var $self = $(element);
        var value = $self.val();
        var field = $self.attr('name');
        if (value.length > 0) {

            var data = {};
            data.field = field;
            data.value = value;
            data.ignored_id = ignored_id;

            $.ajax({
                url: route,
                method: 'POST',
                data: data,
                dataType: 'json',
                beforeSend: function () {
                    $self.removeClass('is-valid is-invalid');
                }
            }).done(function (data) {
                var isValid = true;
                if ($self[0].type === "email") {
                    var emailValidation = new RegExp('^[A-Za-z0-9._%+-]+@ispm.co.ao$');
                    isValid = emailValidation.test(value);
                }

                if (data.success && isValid) {
                    $self.addClass('is-valid');

                    // TODO: better way to reenable modal submit btn
                    $('#form_modal_transaction .btn.forlearn-btn.add').attr('disabled', false);
                } else {
                    $self.addClass('is-invalid');
                }
            });
        } else {
            $self.addClass('is-invalid');
        }
    }
};

//================================================================================
// Functions
//================================================================================

/**
 * Obtém uma lista unica de itens
 * @param list
 * @returns {Array}
 */
function unique(list) {
    var result = [];
    $.each(list, function (i, e) {
        if ($.inArray(e, result) == -1) result.push(e);
    });
    return result;
}

/**
 * Check if value is inside array
 * @param needle What to search
 * @param haystack Where to search
 * @returns {boolean}
 */
window.inArray = function (needle, haystack) {
    for (var i = 0; i < haystack.length; i++) {
        if (haystack[i] === needle) return true;
    }
    return false;
};

/**
 * Cria elemento HTML simples
 * @param name Nome do elemento
 * @param attributes Objeto com as propriedades p.ex. {name: x, value: y}
 * @param text Texto dentro do elemento
 * @returns {string} HTML do objeto criado
 * @constructor
 */
window.SimpleHTMLElement = function (name, attributes, text) {
    var html = '';

    html += '<' + name;
    for (var key in attributes) {
        if (attributes.hasOwnProperty(key)) {
            var newKey = key;
            if (key.includes('data_')) {
                newKey = key.replace('data_', 'data-');
            }

            html += ' ' + newKey;
            if (typeof attributes[key] !== 'undefined') {
                if (typeof attributes[key] === 'string') {

                    // Escape double quotes
                    attributes[key] = attributes[key].replace(/"/g, '&quot;');
                }
                html += '="' + attributes[key] + '"';
            }
        }
    }
    html += '>';

    if (typeof text !== 'undefined' && text !== null) {
        html += text;
    }

    var singles = ['area', 'base', 'br', 'col', 'command', 'embed', 'hr', 'img', 'input', 'keygen', 'link', 'meta', 'param', 'souce', 'track', 'wbr'];
    if (!singles.includes(name)) {
        html += '</' + name + '>';
    }

    return html;
};

//================================================================================
// Events
//================================================================================

$(document).ready(function () {

    // Verifica se os elementos existem na página, caso contrário limpa a localstorage
    for (var i = 0; i < localStorage.length; i++) {
        if (Utils.isValidSelector(localStorage.key(i))) {
            var elem = $(localStorage.key(i));
            if (elem.length <= 0) {
                localStorage.removeItem(localStorage.key(i));
            }
        }
    }

    $.each($('.has-treeview a.nav-link.active'), function () {
        var $this = $(this),
            $parent = $(this).parent().parent().parent();

        var $firstChildren = $($parent.children()[0]);

        if ($this.hasClass('active')) {


            if (!$parent.hasClass('menu-open')) {
                $parent.addClass('menu-open');
            }

            if (!$firstChildren.hasClass('active')) {
                $firstChildren.addClass('active');
            }
        }

        $('.nav-item.has-treeview.menu-open').find('ul').show();

    });

    $.each($('.date'), function (k, v) {
        $(v).datepicker({
            uiLibrary: 'bootstrap4',
            locale: 'pt-br',
            format: 'yyyy-mm-dd'
        });
    });

    // Elimina ficheiros do input type file
    $('.remove-input-attachment').on('click', function () {
        console.log('deleted');
        $(this).parent().find('.attachment').val('');
    });

});

//Reference:
//https://www.onextrapixel.com/2012/12/10/how-to-create-a-custom-file-input-with-jquery-css3-and-php/
(function ($) {
    // Browser supports HTML5 multiple file?
    var multipleSupport = typeof $('<input/>')[0].multiple !== 'undefined',
        isIE = /msie/i.test(navigator.userAgent);

    $.fn.customFile = function () {

        return this.each(function () {

            var $file = $(this).addClass('custom-file-upload-hidden'), // the original file input
                $wrap = $('<div class="file-upload-wrapper">'),
                $input = $('<input type="text" class="file-upload-input" />'),
                // Button that will be used in non-IE browsers
                $button = $('<button type="button" class="btn file-upload-button forlearn-btn"><i class="fas fa-file-upload"></i>Selecionar ficheiro</button>'),
                // Hack for IE
                $label = $('<label class="file-upload-button" for="' + $file[0].id + '"><i class="fas fa-file-upload"></i>Selecionar ficheiro</label>');

            // Hide by shifting to the left so we
            // can still trigger events
            $file.css({
                position: 'absolute',
                left: '-9999px'
            });

            $wrap.insertAfter($file)
                .append($file, $input, (isIE ? $label : $button));

            // Prevent focus
            $file.attr('tabIndex', -1);
            $button.attr('tabIndex', -1);

            $button.click(function () {
                $file.focus().click(); // Open dialog
            });

            $file.change(function () {

                var files = [],
                    fileArr,
                    filename;

                // If multiple is supported then extract
                // all filenames from the file array
                if (multipleSupport) {
                    fileArr = $file[0].files;
                    for (var i = 0, len = fileArr.length; i < len; i++) {
                        files.push(fileArr[i].name);
                    }
                    filename = files.join(', ');

                    // If not supported then just take the value
                    // and remove the path to just show the filename
                } else {
                    filename = $file.val().split('\\').pop();
                }

                $input.val(filename) // Set the value
                    .attr('title', filename) // Show filename in title tootlip
                    .focus(); // Regain focus

            });

            $input.on({
                blur: function () {
                    $file.trigger('blur');
                },
                keydown: function (e) {
                    if (e.which === 13) { // Enter
                        if (!isIE) {
                            $file.trigger('click');
                        }
                    } else if (e.which === 8 || e.which === 46) { // Backspace & Del
                        // On some browsers the value is read-only
                        // with this trick we remove the old input and add
                        // a clean clone with all the original events attached
                        $file.replaceWith($file = $file.clone(true));
                        $file.trigger('change');
                        $input.val('');
                    } else if (e.which === 9) { // TAB

                    } else { // All other keys
                        return false;
                    }
                }
            });

        });

    };

    // Old browser fallback
    if (!multipleSupport) {
        $(document).on('change', 'input.customfile', function () {

            var $this = $(this),
                // Create a unique ID so we
                // can attach the label to the input
                uniqId = 'customfile_' + (new Date()).getTime(),
                $wrap = $this.parent(),

                // Filter empty input
                $inputs = $wrap.siblings().find('.file-upload-input')
                    .filter(function () {
                        return !this.value;
                    }),

                $file = $('<input type="file" id="' + uniqId + '" name="' + $this.attr('name') + '"/>');

            // 1ms timeout so it runs after all other events
            // that modify the value have triggered
            setTimeout(function () {
                // Add a new input
                if ($this.val()) {
                    // Check for empty fields to prevent
                    // creating new inputs when changing files
                    if (!$inputs.length) {
                        $wrap.after($file);
                        $file.customFile();
                    }
                    // Remove and reorganize inputs
                } else {
                    $inputs.parent().remove();
                    // Move the input so it's always last on the list
                    $wrap.appendTo($wrap.parent());
                    $wrap.find('input').focus();
                }
            }, 1);

        });
    }

}(jQuery));

$('input[type=file]').customFile();

