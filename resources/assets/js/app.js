"use strict";
try {

    // Jquery
    window.$ = window.jQuery = require('jquery');

    // Toastr
    window.toastr = require('toastr');

    // Bootstrap
    window.Popper = require('popper.js').default;
    require('bootstrap/dist/js/bootstrap');
    require('bootstrap/js/dist/modal');

    // Datatables
    require('datatables.net');
    require('datatables.net-bs4');
    require('datatables.net-buttons');
    require('datatables.net-buttons-bs4');
    require('datatables.net-responsive');
    require('datatables.net-responsive-bs4');
    require('datatables.net-select');
    require('datatables.net-buttons/js/buttons.colVis.js')(); // Column visibility
    require('datatables.net-buttons/js/buttons.html5.js')();  // HTML 5 file export
    require('datatables.net-buttons/js/buttons.flash.js')();  // Flash file export
    require('datatables.net-buttons/js/buttons.print.js')();  // Print view button
    require('mark.js');
    require('datatables.mark.js');

    // Datatables - 3rd party plugins
    window.JSZip = require('jszip');
    window.pdfMake = require('pdfmake');
    window.pdfFonts = require('pdfmake/build/vfs_fonts');
    window.pdfMake.vfs = pdfFonts.pdfMake.vfs;
    delete window.pdfFonts;

    // SortableJS
    window.Sortable = require('sortablejs').default;

    // Custom
    require('./custom');

} catch (e) {
    console.error(e);
}
