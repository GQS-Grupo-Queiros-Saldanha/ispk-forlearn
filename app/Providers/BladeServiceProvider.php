<?php

namespace App\Providers;

use Blade;
use File;
use Illuminate\Support\ServiceProvider;

class BladeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Add @var for Variable Assignment
        Blade::directive('var', function($expression) {

            // Strip Open and Close Parenthesis
            $expression = substr(substr($expression, 0, -1), 1);

            list($variable, $value) = explode('\',', $expression, 2);

            // Ensure variable has no spaces or apostrophes
            $variable = trim(str_replace('\'', '', $variable));

            // Make sure that the variable starts with $
            if (!starts_with($variable, '$')) {
                $variable = '$' . $variable;
            }

            $value = trim($value);
            return "<?php {$variable} = {$value}; ?>";
        });

        // Add @asset markup
        Blade::directive('asset', function($file) {

            $file = str_replace(['(', ')', "'"], '', $file);
            $filename = $file;

            // Internal file
            if (!starts_with($file, '//') && !starts_with($file, 'http')) {
                $version = File::lastModified(public_path() . '/' . $file);
                $filename = $file . '?v=' . $version;
                if (!starts_with($filename, '/')) {
                    $filename = '/' . $filename;
                }
            }

            $fileType = substr(strrchr($file, '.'), 1);

            if ($fileType === 'js') {
                return '<script src="' . $filename . '"></script>';
            }

            return '<link href="' . $filename . '" rel="stylesheet" />';
        });

        // Add @icon directive
        Blade::directive('icon', function($expression) {

            // Strip Open and Close Parenthesis
            $expression = substr(substr($expression, 0, -1), 1);
            return '<i class="' . $expression . '"></i>';
        });
    }
}
