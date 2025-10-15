let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix
    .setPublicPath('public_html/')

    .js('resources/assets/js/app.js', 'js')

    .sass('resources/assets/sass/app.scss', 'css')
    .sass('resources/assets/sass/vendor.scss', 'css')
    .sass('resources/assets/sass/jquery.steps.scss', 'css')
    .sass('resources/assets/sass/bootstrap-select.min.scss', 'css/backoffice')
    .sass('resources/assets/sass/toastr.min.scss', 'css/backoffice')
    .sass('resources/assets/sass/Myquerybuilder.scss', 'css')

    .scripts('resources/assets/js/dropdown-extra-fields.js', 'public_html/js/dropdown-extra-fields.js')
    .scripts('resources/assets/js/jquery-sortable-lists.min.js', 'public_html/js/jquery-sortable-lists.min.js')
    .scripts('resources/assets/js/query-builder.pt-PT.js', 'public_html/js/query-builder.pt-PT.js')
    .scripts('resources/assets/js/query-builder.standalone.js', 'public_html/js/query-builder.standalone.js')

    .scripts('resources/assets/js/backoffice/bootstrap-datetimepicker.min.js', 'public_html/js/backoffice/bootstrap-datetimepicker.min.js')
    .scripts('resources/assets/js/backoffice/bootstrap-select.min.js', 'public_html/js/backoffice/bootstrap-select.min.js')
    .scripts('resources/assets/js/backoffice/dataTables.conditionalPaging.js', 'public_html/js/backoffice/dataTables.conditionalPaging.js')
    .scripts('resources/assets/js/backoffice/dropdown-extra-fields.js', 'public_html/js/backoffice/dropdown-extra-fields.js')
    .scripts('resources/assets/js/backoffice/dynamic_forms.js', 'public_html/js/backoffice/dynamic_forms.js')
    .scripts('resources/assets/js/backoffice/jquery.dynamic-fields.js', 'public_html/js/backoffice/jquery.dynamic-fields.js')
    .scripts('resources/assets/js/backoffice/moment-with-locales.min.js', 'public_html/js/backoffice/moment-with-locales.min.js')
    .scripts('resources/assets/js/backoffice/multiselect.min.js', 'public_html/js/backoffice/multiselect.min.js')
    .scripts('resources/assets/js/backoffice/select2.sortable.js', 'public_html/js/backoffice/select2.sortable.js')
    .scripts('resources/assets/js/backoffice/src.js', 'public_html/js/backoffice/src.js')
    .scripts('resources/assets/js/backoffice/toastr.min.js', 'public_html/js/backoffice/toastr.min.js')

    .copy('node_modules/@fortawesome/fontawesome-free/webfonts', 'public_html/fonts')

    .options({
        processCssUrls: false
    })

    .webpackConfig({
        resolve: {
            alias: {
                'markjs': 'mark.js/dist/jquery.mark.js'
            }
        }
    })

    .extract([
        'jquery',
        'popper.js',
        'bootstrap',
        'datatables.net',
        'datatables.net-bs4',
        'datatables.net-buttons',
        'datatables.net-buttons-bs4',
        'datatables.net-responsive',
        'datatables.net-responsive-bs4',
        'datatables.net-select',
        'datatables.net-buttons/js/buttons.colVis.js',
        'datatables.net-buttons/js/buttons.html5.js',
        'datatables.net-buttons/js/buttons.flash.js',
        'datatables.net-buttons/js/buttons.print.js',
        'mark.js',
        'datatables.mark.js',
        'jszip',
        'pdfmake',
        'pdfmake/build/vfs_fonts',
        'toastr',
        'sortablejs'
    ]);

/*.sourceMaps()
.webpackConfig({
    devtool: "source-map"
})*/
