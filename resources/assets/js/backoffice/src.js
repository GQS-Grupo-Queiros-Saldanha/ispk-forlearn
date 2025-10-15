setTimeout(function(){
  jQuery.noConflict(); (function( $ ) { $(function() {  }); })(jQuery);
},500);

var Modal = {
    confirm: function (url, token) {

        //MODAL CONFIRM
        $(document).on("click", "button[data-target*='#modal_confirm']", function () {
            var data = JSON.parse($(this).attr("data-action"));
            var modal_confirm = $("#modal_confirm");
            var form = $(modal_confirm.find("form"));
            var input1 = $(modal_confirm.find("input")[0]);
            var input2 = $(modal_confirm.find("input")[1]);

            form.attr('action', url + data.route[1]);
            input1.val(data.method.toUpperCase());
            input2.val(token);
        });
    }
};

/**
 * Treeview
 */

var classCallCheck = function (instance, Constructor) {
    if (!(instance instanceof Constructor)) {
      throw new TypeError("Cannot call a class as a function");
    }
  };

var Treeview = function ($) {
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


class DynamicDatatable {

    /*
    * parent_element -> Parent element where html will be added
    * table_id -> Table identification
    * columns -> header columns
    * rows -> Used for initial data only
    * delete_text -> Delete text header column
    * modal_type -> Modal where will read the input values
    * action -> show/edit/create
    */

    constructor(parent_element,table_id,columns,rows,delete_text,modal_type, action) {
        this.parent_element = parent_element;
        this.table_id = table_id;
        this.columns = columns;
        this.rows = rows;
        this.delete_text = delete_text;
        this.modal_type = modal_type;
        this.action = action;
    }

    addButtons(modalType){
        var html = "<td style='vertical-align: middle;text-align: center;'><button type='button' data-type='delete' class='btn btn-danger btn-sm delete'><i class='dynamic-datatable removebutton fas fa-trash-alt'></i></button></td>";
        return html;
    };

    formSubmit(){
        var $modal = $("#"+this.modal_type);
        var selects = $modal.find("select");
        var inputs = $("#" + this.modal_type + " div:not(.bs-searchbox) > input:enabled:not([readonly])");
        var checkbox = $modal.find("input:checked:checkbox:enabled:not([readonly])");
        var options = [];


        $.each(selects, function(k,v){
            options.push({"text": Utils.getTextFromSelect(v,$(v).val()), "value":$(v).val(), "name": $(v).attr("name")});
        });


        $.each(checkbox, function(k,v){
            options.push({"text": $(v).attr("text"), "value":$(v).val(), "name": $(v).attr("name")});
        });


        $.each(inputs, function(k,v){
            options.push({"text": $(v).val(), "value":$(v).val(), "name": $(v).attr("name")});
        });



        this.addRow(options);
        $("#"+this.modal_type).modal("hide");
    };

    addEvents(){
        var deleteRow = this.deleteRow;
        $("body").on('click', "#"+this.table_id + " button.delete", function() {
            deleteRow(this);
        });
    };

    // Adding a method to the constructor
    initialize() {

        var modalType = this.modal_type;
        var addbuttons = this.action === 'show' ? '' : this.addButtons(modalType);
        var html = "";

        html += "<table class='table table-hover dataTable no-footer dtr-inline' id='"+this.table_id+"'><thead><tr>";

        //create columns
        $.each(this.columns, function(k,v){
            html += "<th><span>" + v.text + "</span></th>";
        });

        if(this.action != 'show'){
            html += "<th style='text-align: center;'>";
            html += "<span>"+this.delete_text+"</span></th>";
        }

        html += "</tr></thead><tbody>";

        //crate initial rows if they exists
        if(typeof this.rows != 'undefined'){
            $.each(this.rows, function(k,v){
                html += "<tr>";

                $.each(v, function(j,i){
                    if(i instanceof Array){
                        html += "<td><ul>";
                        $.each(i, function(c,e){
                            html += "<li><span>"+e.text+"</span><input type='hidden' name='"+e.name.replace("[]","")+"_"+(k+1)+"_[]' value='"+e.value+"'></li>";
                        });
                        html += "</ul></td>";
                    }else{
                        html += "<td class='inner-content'><span>"+i.text+"</span><input type='hidden' name='"+i.name+"' value='"+i.value+"'></></td>";
                    }

                });
                html += addbuttons;
                html += "</tr>";
            });
        }

        html += "</tbody></table>";

        $(this.parent_element).html(html);

        if(this.action === 'create'){
            if(localStorage.getItem(this.parent_element) != null){
                if(localStorage.getItem(this.parent_element).length > 0){
                    $(this.parent_element).find("tbody").html(localStorage.getItem(this.parent_element));
                }
            }
        }

        this.addEvents();
    };

    addRow(data) {
        try{
            var tbody = $("#"+this.table_id).find("tbody");
            var totalRows = $("#"+this.table_id).find("tr").length;
            var html = "<tr>";
            var innerElements = [], uniqueElements = [];

            $.each(data, function(k,v){
                if(v.name.indexOf("[]") > -1){
                    innerElements.push(v);
                    uniqueElements.push(v.name);
                }
            });

            uniqueElements = unique(uniqueElements);

            $.each(data, function(k,v){
                if(v.name.indexOf("[]") == -1){
                    html += "<td><span>"+v.text+"</span><input type='hidden' name='"+v.name+"[]' value='"+v.value+"'></></td>";
                }
            });


            $.each(uniqueElements, function(u,e){
                html += "<td><ul>";
                $.each(innerElements, function(j,i){
                    if(i.name === e)
                        html += "<li><span>"+i.text+"</span><input type='hidden' name='"+i.name.replace("[]","")+"_"+totalRows+"_[]' value='"+i.value+"'></li>";
                });
                html += "</ul></td>";
            });

            html += this.addButtons(this.modal_type);
            html += "</tr>";

            $(tbody).append(html);

            localStorage.setItem(this.parent_element, $(tbody).html());

        }catch(ex){

        }

    };

    deleteRow(element){
        $(element).parent().parent().remove();
    };
}

var Utils = {
    getTextFromSelect: function(element, value){
        var text = "";
        $(element).children().each(function(k,v){
            if($(v).val() == value){
                text = $(v).text();
            }
        });
        return text;
    }
};

// obtem uma lista unica de items
function unique(list) {
    var result = [];
    $.each(list, function(i, e) {
        if ($.inArray(e, result) == -1) result.push(e);
    });
    return result;
}

$(document).ready(function(){

    //verifica se os elementos existem na página, caso contrário limpa a localstorage
    for (var i = 0; i < localStorage.length; i++){
        if($(localStorage.key(i)).length <= 0)
            localStorage.removeItem(localStorage.key(i));
    }
});

setTimeout(function(){

    try{
        // esta função coloca como activo o menu lateral e abre o menu parent se tiver um submenu
        $.each($(".has-treeview a.nav-link.active"), function(){
            var $this = $(this), $parent = $(this).parent().parent().parent();
            var $firstChildren = $($parent.children()[0]);

            if($this.hasClass("active")){
                if(!$parent.hasClass("menu-open")){
                $parent.addClass("menu-open");
                }

                if(!$firstChildren.hasClass("active")){
                    $firstChildren.addClass("active");
                }
            }
        });
    }catch(ex){

    }
},500);
