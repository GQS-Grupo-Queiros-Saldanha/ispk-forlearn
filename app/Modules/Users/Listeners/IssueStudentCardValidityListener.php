<?php
 
namespace App\Modules\Users\Listeners;
 
use App\Modules\Users\Events\PaidStudentCardEvent;
use DB;
use Carbon\Carbon;
class IssueStudentCardValidityListener
{
 
 
    /**
     * Handle the event.
     *
     * @param  App\Modules\Users\Events\PaidStudentCardEvent $event
     * @return void
     */
    public function handle(PaidStudentCardEvent $event)
    {
        $user_id = $event->usuario;

        $matriculation = DB::table('matriculations')->where('user_id', $user_id)->first();

        if(isset($matriculation)){

            if(
                DB::table('card_student_status')
                ->where('matriculation_id', $matriculation->id)
                ->exists()
                ){

                    $course = DB::table('courses')
                                ->join('user_courses as uc', 'uc.courses_id', '=', 'courses.id')
                                ->where('uc.users_id', $matriculation->user_id)
                                ->first();

                    $lective_year = DB::table('lective_years')
                                        ->where('id', $matriculation->lective_year)
                                        ->first();

                if(isset($lective_year)){

                   // gerar data de validade 
                    $duration = (int) $course->duration_value;
                    $year = (int) $matriculation->course_year;

                    $left_years =  $duration - $year;

                    $anoFirst = (int) explode('-', $lective_year->start_date)[0];

                    $anoFinal = ($anoFirst + $left_years) + 1;

                    $data_validade = $anoFinal . '-07-31';

                    $data_validade = Carbon::parse($data_validade);


                    DB::table('card_student_status')
                    ->where('matriculation_id', $matriculation->id)
                    ->update(
                        [
                            'valido_ate' => $data_validade
                        ]
                    );   
                    

                }
                    
                                 
                                
                }

        }
    }
}