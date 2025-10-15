<?php

namespace App\Helpers;

use DateTime;
use Exception;
use Lang;
use Log;
use Throwable;

class PDFHelper
{

    private static $paper_sizes = ['4a0','2a0','a0','a1','a2','a3','a4','a5','a6','a7','a8','a9','a10','b0','b1','b2','b3','b4','b5','b6','b7','b8','b9','b10','c0','c1','c2','c3','c4','c5','c6','c7','c8','c9','c10','ra0','ra1','ra2','ra3','ra4','sra0','sra1','sra2','sra3','sra4','letter','legal','ledger','tabloid','executive','folio','commercial #10 envelope','catalog #10 1/2 envelope','8.5x11','8.5x14','11x17'];
    private static $orientations = ['landscape', 'portrait'];

    /**
     * @return array
     */
    public static function getPaperSizes(): array
    {
        return self::$paper_sizes;
    }

    /**
     * @return array
     */
    public static function getOrientations(): array
    {
        return self::$orientations;
    }

    /**
     * @return array
     */
    public static function getTranslatedOrientations(): array
    {
        $translated_orientations = [];
        foreach (self::$orientations as $val) {
            $translated_orientations[$val] = __('pdf.orientation_'.$val);
        }
        return $translated_orientations;
    }

    /**
     * @return array
     */
    public static function getTranslatedPaperSizes(): array
    {
        $translated_paper_sizes = [];
        foreach (self::$paper_sizes as $val) {
            $translated_paper_sizes[$val] = ucfirst($val); // __('pdf.paper_size_'.$val);
        }
        return $translated_paper_sizes;
    }
}
