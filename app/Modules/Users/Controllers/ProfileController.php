<?php

namespace App\Modules\Users\Controllers;

use App\Helpers\LanguageHelper;
use App\Http\Controllers\Controller;
use App\Modules\Users\Models\ParameterGroup;
use App\Modules\Users\Models\User;
use App\Modules\Users\Models\UserParameter;
use Auth;
use DB;
use Exception;
use Illuminate\Http\Response;
use Log;
use Request;
use Throwable;

class ProfileController extends Controller
{

    public function index()
    {
        try {

            $parameter_groups = ParameterGroup::with([
                'currentTranslation',
                'roles',
                'parameters' => function ($q) {
                    $q->with([
                        'currentTranslation',
                        'roles',
                        'options' => function ($q) {
                            $q->with([
                                'currentTranslation',
                                'relatedParametersRecursive'
                            ]);
                        }
                    ]);
                }
            ])->orderBy('order')->get();

            $user = User::whereId(Auth::user()->id)->with(
                [
                    /*'parameters.parameter.options',*/
                    'roles' => function ($q) {
                        $q->with([
                            'currentTranslation'
                        ]);
                    },
                    'parameters' => function ($q) {
                        $q->with([
                            'currentTranslation',
                            'groups',
                            'options'
                        ]);
                    }
                ])->firstOrFail();

            /*$user = Auth::user();

            $parameters = $user->parameters()->with([
                'currentTranslation',
                'options' => function ($q) {
                    $q->with([
                        'currentTranslation'
                    ]);
                }
            ])->get();*/

            $data = [
                'user' => $user,
                'parameter_groups' => $parameter_groups
            ];

            return view('Users::profile.index')->with($data);

        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //DB::beginTransaction();
        //DB::commit();
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //DB::beginTransaction();
        //DB::commit();
    }


    public function destroy($id)
    {
        //DB::beginTransaction();
        //DB::commit();
    }
}
