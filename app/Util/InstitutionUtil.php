<?php

namespace App\Util;

use App\Modules\Users\Models\ParameterOption;
use App\Modules\Users\Enum\ParameterEnum;
use App\Modules\Users\Models\User;
use App\Model\Institution;
use DB;

class InstitutionUtil{
    
    public function getDefault(){
        return Institution::where('is_default',1)->first();
    }
    
    public function getEmail($user_id){
        return User::find($user_id)->email ?? null;
    }
    
    public function getUsersByRole($role_id){
        return User::whereHas('roles', function ($q) use ($role_id) {
            $q->whereIn('id', [$role_id]);
        })->leftJoin('user_parameters as u_p9', function ($q) {
            $q->on('users.id', '=', 'u_p9.users_id')->where('u_p9.parameters_id', 1);
        })->get();        
    }
    
    public function getProvinces() {
        return ParameterOption::with([
            'currentTranslation',
            'relatedParameters' => function ($q) {
                $q->whereIn('parameters_id', $this->getProvincesIds());
            }
        ])->where('parameters_id',ParameterEnum::LISTA_DE_PROVINCIAS)->get()->map(function($item){
            return (object)["display_name" => $item->currentTranslation->display_name, "id" => $item->id, 'parameters_id' => $item->relatedParameters[0]->id]; 
        })->all();
    } 
    
    public function getMunicipios($array = []) {
        $parms = sizeof($array) > 0 ? $array : $this->getProvincesIds();
        return ParameterOption::with('currentTranslation')->whereIn('parameters_id',$parms)->get()->map(function($item){
            return (object)["display_name" => $item->currentTranslation->display_name, "id" => $item->id, 'parameters_id' => $item->parameters_id]; 
        })->all();
    }
    
    private function getProvincesIds(){
        return [
            ParameterEnum::LISTA_MUNICIPIOS_LUANDA,
            ParameterEnum::LISTA_MUNICIPIOS_BENGO,
            ParameterEnum::LISTA_MUNICIPIOS_BENGUELA,
            ParameterEnum::LISTA_MUNICIPIOS_BIE,
            ParameterEnum::LISTA_MUNICIPIOS_CABINDA,
            ParameterEnum::LISTA_MUNICIPIOS_CUANDO_CUBANGO,
            ParameterEnum::LISTA_MUNICIPIOS_CUNENE,
            ParameterEnum::LISTA_MUNICIPIOS_HUAMBO,
            ParameterEnum::LISTA_MUNICIPIOS_HUILA,
            ParameterEnum::LISTA_MUNICIPIOS_CUANZA_NORTE,
            ParameterEnum::LISTA_MUNICIPIOS_CUANZA_SUL,
            ParameterEnum::LISTA_MUNICIPIOS_LUNDA_NORTE,
            ParameterEnum::LISTA_MUNICIPIOS_LUNDA_SUL,
            ParameterEnum::LISTA_MUNICIPIOS_MALANGE,
            ParameterEnum::LISTA_MUNICIPIOS_MOXICO,
            ParameterEnum::LISTA_MUNICIPIOS_UIGE,
            ParameterEnum::LISTA_MUNICIPIOS_NAMIBE,
            ParameterEnum::LISTA_MUNICIPIOS_ZAIRE
        ];
    }
    

    
}