/**
 * jQuery Dynamic Fields
 * v0.1
 */

(function ($) {
    /**
     * If data-attribute and options in jQuery options is both available, will use data-attribute instead of jQuery options.
     */
    $.fn.dynamicFields = function (_options) {

        var TAG = "jquery-dynamic-field | ";

        // This is the easiest way to have default options.
        var options = $.extend({
            template: ".dynamic-fields-template",
            buttonAdd: ".dynamic-fields-button-add",
            maxFields: Number.MAX_VALUE, // Infinite
            startFields: 0, // Always start with 1 counter fields
        }, _options);

        var stringTemplate = this.data("template") ? this.data("template") : options.template;
        if (stringTemplate.startsWith(".")) { // class
            if (this.find(stringTemplate).length > 0) {
                this.find(stringTemplate).addClass("dynamic-fields-row");
                this.template = this.find(stringTemplate).clone();

                // Added by Nuno Moura
                this.find(stringTemplate).remove();

            } else {
                console.log(TAG + "Cannot find template element for class='" + stringTemplate + "'. Make sure the element is within the dynamic fields.");
                return this;
            }
        } else if (stringTemplate.startsWith("#")) { // id
            if ($(stringTemplate).length > 0) {
                $(stringTemplate).addClass("dynamic-fields-row");
                this.template = $(stringTemplate).clone();
            } else {
                console.log(TAG + "Cannot find template element for id='" + stringTemplate + "'.");
                return this;
            }
        } else {
            console.log(TAG + "Currently, template can only use class (use '.class-name') and id (use '#id-name')");
        }

        var stringButtonAdd = this.data("button-add") ? this.data("button-add") : options.buttonAdd;
        if (stringButtonAdd.startsWith(".")) { // class
            if (this.find(stringButtonAdd).length > 0) {
                this.buttonAdd = this.find(stringButtonAdd);
            } else {
                console.log(TAG + "Cannot find add button element for class='" + stringButtonAdd + "'. Make sure the element is within the dynamic fields.");
                return this;
            }
        } else if (stringButtonAdd.startsWith("#")) { // id
            if ($(stringButtonAdd).length > 0) {
                this.buttonAdd = $(stringButtonAdd);
            } else {
                console.log(TAG + "Cannot find add button element for id='" + stringButtonAdd + "'");
                return this;
            }
        } else {
            console.log(TAG + "Currently, buttonAdd can only use class (use '.class-name') and id (use '#id-name')");
        }

        var self = this;
        var container = this;
        var fieldsCount = options.startFields;

        // Added by Nuno Moura
        var index = $('[data-index]');
        if (index.length) {
            fieldsCount = parseInt(index.last().attr('data-index')) + 1;
        }

        this.buttonAdd.on("click", function (e) {
            e.preventDefault();
            if (fieldsCount < options.maxFields) {
                var clone = self.template.clone(false);
                container.append(clone);

                /* Added by Nuno Moura */

                // Clear inputs
                var inputs = clone.find('input, select, textarea');
                inputs.val('');
                setTimeout(function () {
                    inputs[0].focus();
                    inputs[0].scrollIntoView();
                }, 100);


                // Change ids,name,href,etc from [0] to [x]
                var links = clone.find('input, select, textarea, .nav-link, .tab-pane, label');
                links.each(function () {
                    var self = $(this);

                    // Which attributes, what to replace and what to replace with
                    var attributes = ['id', 'name', 'href', 'for'];
                    var whatToReplace = ['[0]', '_0'];
                    var whatToReplaceWith = ['[' + fieldsCount + ']', '_' + fieldsCount];

                    if (whatToReplace.length === whatToReplaceWith.length) {

                        // For each attribute
                        for (var i = 0; i < attributes.length; i++) {
                            var selector = attributes[i];
                            var attribute = self.attr(selector);

                            // If attribute exists
                            if (attribute) {

                                // If it contains what is to be replcaed, then do it
                                for (var j = 0; j < whatToReplace.length; j++) {
                                    if (attribute.includes(whatToReplace[j])) {
                                        self.attr(selector, attribute.replace(whatToReplace[j], whatToReplaceWith[j]));
                                    }
                                }
                            }
                        }
                    }

                });

                clone.find('[data-role="remove-field"]').on("click", function (e) {
                    e.preventDefault();
                    $(this).parents('.dynamic-fields-row').remove();
                    fieldsCount--;
                });

                // Added (plugins)
                var $order = clone.find('.order');
                var sortable = new Sortable($order[0], {
                    ghostClass: 'bg-warning',
                    onUpdate: function() {
                        sortSelect($select, this.toArray());
                    }
                });

                var $select = clone.find('.selectpicker');
                $select.selectpicker('refresh');
                $select.on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {

                    // Obter value selecionado
                    var option = e.currentTarget.options[clickedIndex];
                    var value = option.value;

                    // Criar ou remover da lista
                    if (isSelected) {
                        var html = '';
                        html += '<li class="list-group-item" data-id="' + value + '">';
                        html += option.innerHTML;
                        html += '</li>';
                        $order.append(html);
                    } else {
                        $order.find('li[data-id="' + value + '"]').remove();
                    }

                    sortSelect($select, sortable.toArray());

                });

                fieldsCount++;
            } else {
                alert("lebih" + fieldsCount + " | " + options.maxFields);
            }
        });

        // Changed by Nuno Moura
        //container.find('[data-role="remove-field"]').remove();
        container.find('[data-role="remove-field"]').on("click", function (e) {
            e.preventDefault();
            $(this).parents('[data-index]').remove();
            fieldsCount--;
        });
    }
})(jQuery);

$(function () {
    var dynamicFields = $('*[data-role="dynamic-fields"]');
    if (dynamicFields.length > 0) {
        dynamicFields.dynamicFields();
    }

    // Load order (if editing)
    var $selects = $('select[class^="selectpicker"][name^=options]');
    if ($selects.length > 0) {
        $selects.each(function(k,v) {
            var $select = $(v);
            var $order = $select.parents('.form-group').find('.order');

            var $options = $select.find('option:selected');
            $options.each(function(k,v) {
                var $option = $(v);
                var html = '';
                html += '<li class="list-group-item" data-id="' + $option.attr('value') + '">';
                html += $option.text();
                html += '</li>';
                $order.append(html);
            });

            // Load SortableJs
            new Sortable($order[0], {
                ghostClass: 'bg-warning',
                onUpdate: function() {
                    sortSelect($select, this.toArray());
                }
            });
        });
    }
});


function sortSelect($select, order) {

    // Move the options to their corresponding position
    for (var i = order.length; i >= 0; i--) {
        var $option = $select.find('option[value="' + order[i] + '"');
        if ($option.length) {
            $option.parent().prepend($option);
        }
    }

    // Refresh selecpicker
    $select.selectpicker('refresh');

    //debug
    var selected = $select.find('option:selected').map(function() {
        return $(this).text();
    }).get();
}
