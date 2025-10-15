<?php

namespace App\Exports;

use App\Helpers\LanguageHelper;
use App\Modules\Payments\Models\Transaction;
use App\Modules\Users\Models\User;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;

class Detalhada implements FromView, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function view($teo): View
    {
        
      
        $request = Request()->all();
        $data1 = $request['data1'] == null ? date('Y-m-d') : $request['data1'];
        $data2 = $request['data2'] ?? $data2 = null;
        $article = $request['article'] ?? $article = null;
        $course = $request['curso'] ?? $course = null;
        $student = $request['student'] ?? $student = null;

        if ($data2 == null) {
            $emoluments = Transaction::join('transaction_article_requests', 'transaction_article_requests.transaction_id', '=', 'transactions.id')
                ->join('article_requests', 'article_requests.id', '=', 'transaction_article_requests.article_request_id')
                ->leftJoin('article_translations as at', function ($join) {
                    $join->on('at.article_id', '=', 'article_requests.article_id');
                    $join->on('at.language_id', '=', \DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('at.active', '=', \DB::raw(true));
                })
                ->join('users', 'users.id', '=', 'article_requests.user_id')
                ->leftJoin('matriculations', 'matriculations.user_id', '=', 'users.id')
                ->leftJoin('user_parameters as u_p', function ($join) {
                    $join->on('users.id', '=', 'u_p.users_id')
                        ->where('u_p.parameters_id', 1);
                })
                ->join('transaction_info', 'transaction_info.transaction_id', '=', 'transactions.id')
                ->join('banks', 'transaction_info.bank_id', '=', 'banks.id')
                ->join('user_courses', 'user_courses.users_id', '=', 'users.id')
                ->leftJoin('courses_translations as ct', function ($join) {
                    $join->on('ct.courses_id', '=', 'user_courses.courses_id');
                    $join->on('ct.language_id', '=', \DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ct.active', '=', \DB::raw(true));
                })
                ->leftJoin('user_parameters as up_meca', function ($join) {
                    $join->on('users.id', '=', 'up_meca.users_id')
                        ->where('up_meca.parameters_id', 19);
                })
                ->leftJoin('users as u1', 'u1.id', '=', 'transactions.created_by')
                ->leftJoin('user_parameters as user_va', function ($join) {
                    $join->on('u1.id', '=', 'user_va.users_id')
                        ->where('user_va.parameters_id', 1);
                })
                ->select(
                    'at.article_id as article_id',
                    'at.display_name as article_name',
                    'users.name as user_name',
                    'users.id as user_id',
                    //'transactions.value as price',
                    'transaction_article_requests.value as price',
                    'banks.display_name as bank_name',
                    'transaction_info.reference as reference',
                    'ct.display_name as course_name',
                    'ct.courses_id as course_id',
                    'up_meca.value as matriculation_number',
                    'matriculations.course_year as course_year',
                    'transaction_info.fulfilled_at as fulfilled_at',
                    'u1.name as created_by',
                    'transaction_info.value as valorreferencia'
                )
                //->whereBetween('transactions.created_at', [$date1, $date2]) #\DB::raw('CURDATE()')
                ->whereDate('transactions.created_at', $data1) #\DB::raw('CURDATE()')
                ->where('transactions.type', 'payment')
                ->when($article != null, function ($q) use ($article) {
                        return $q->whereIn('at.article_id', $article);
                })->when($course != null, function ($q) use ($course) {
                        return $q->whereIn('ct.courses_id', $course);
                })
                ->when($student != null, function ($q) use($student) {
                                return $q->whereIn('users.id', $student);
                            })
                ->get();
            $data = ['emoluments' => $emoluments];
            //return view('Reports::excel.income', ['date' => $this->date]);
            return view('Reports::excel.income')->with($data);

        }else{
            
            $funcionario = $teo;
            
            $data = ['funcionario' => $funcionario];
            return view('Reports::excel.detalhada')->with($data);

        }

}
}
