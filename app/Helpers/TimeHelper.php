<?php

namespace App\Helpers;

use DateTime;
use Exception;
use Lang;
use Log;
use Throwable;

class TimeHelper
{

    public static function time_elapsed_string($datetime, $full = false)
    {
        try {
            $now = new DateTime;
            $ago = new DateTime($datetime);
            $diff = $now->diff($ago);

            $diff->w = floor($diff->d / 7);
            $diff->d -= $diff->w * 7;

            $string = [
                'y' => '',
                'm' => '',
                'w' => '',
                'd' => '',
                'h' => '',
                'i' => '',
                's' => ''
            ];
            foreach ($string as $k => &$v) {
                if ($diff->$k) {
                    $v = $diff->$k . ' ' . Lang::choice('time.' . $k, $diff->$k);
                } else {
                    unset($string[$k]);
                }
            }

            if (!$full) {
                $string = array_slice($string, 0, 1);
            }

            return $string ? implode(', ', $string) . ' ' . Lang::get('time.ago') : Lang::get('time.just_now');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return '';
        }
    }
}
