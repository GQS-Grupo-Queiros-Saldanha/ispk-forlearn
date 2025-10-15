<?php

/**
 *	Reports Helper  
 */

 function matchProvince($sigla){
        switch($sigla){
            case 'LA':
                return 'Luanda';
            case 'BE':
                return 'Bié';
            case 'BO':
                return 'Bengo';
            case 'BA':
                return 'Benguela';
            case 'HO':
                return 'Huambo';
            case 'HA':
                return 'Huíla';
            case 'NE':
                return 'Namibe';
            case 'CE':
                return 'Cunene';
            case 'KK':
                return 'Cuando-Cubango';
            case 'MO':
                return 'Moxico';
            case 'LS':
                return 'Lunda-Sul';
            case 'LN':
                return 'Lunda-Norte';
            case 'CA':
                return 'Cabinda';
            case 'ZE':
                return 'Zaire';
            case 'UE':
                return 'Uíge';
            case 'ME':
                return 'Malange';
            case 'KS':
                return 'Cuanza-Sul';
            case 'KN': 
                return 'Cuanza-Norte';
            default: 
                return 'Luanda';
            
        }
 }