<?php

namespace App\Http\Controllers;

use App\Modules\GA\Models\Event;
use App\Modules\GA\Models\EventOption;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return Renderable
     */
    public function index()
    {
        $events = Event::with([
            'options',
            'currentTranslation'
        ])->get();

        $calendar = \Calendar::addEvents($events)
            ->setOptions([ //set fullcalendar options
                'firstDay' => 1,
                'lang' => 'pt',
            ])->setCallbacks([ //set fullcalendar callback options (will not be JSON encoded)
                'viewRender' => 'function() {console.log("viewRender");}'
            ]);

        $data = [
            'calendar' => $calendar
        ];

        return view('calendar')->with($data);
    }
}
