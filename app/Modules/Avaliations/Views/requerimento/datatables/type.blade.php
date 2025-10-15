
{{$state->nome}} {{ isset($state->discipline) ?" ( #".$state->code_discipline." - ".$state->discipline." ) ": ''}}
{{ isset($state->metric) ?"(" . $state->metric  . ")": ''}}