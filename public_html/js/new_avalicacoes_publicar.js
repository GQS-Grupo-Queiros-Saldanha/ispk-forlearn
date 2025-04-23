const IDENTY = 'ispsml';

function macCalculate(config, pf1, pf1_percentagem, pf2, pf2_percentagem, oa, oa_percentagem) {
   return parseInt(Math.round(  (pf1 * pf1_percentagem) + (pf2 * pf2_percentagem) + (oa * oa_percentagem) ) );
}

function mfCalculate(config, mac, oral) {
    if (config != null && config != undefined) {
        if (config.strategy == IDENTY) {
            //console.log(`${mac} * ${config.percentagem_mac} + ${oral} * ${config.percentagem_oral}`);
            return parseInt(Math.round( (mac * (config.percentagem_mac / 100)) + (oral * (config.percentagem_oral / 100)) ));
        }
    }
    return parseInt((mac * 0.4) + (oral * 0.6));
}

function examePautaCalculate(config, calculo_mac, mac_percentagem, neen_percentagem, neen) {
    if (config != null && config != undefined) {
        if (config.strategy == ISPK) {
            //console.log(`${calculo_mac} * ${mac_percentagem} + ${neen} * ${neen_percentagem}`);
            return parseInt(Math.round((parseFloat(calculo_mac) * mac_percentagem) + (parseFloat(neen) * neen_percentagem)));
        }
    }
    return parseInt((parseFloat(calculo_mac) * mac_percentagem + parseFloat(neen) * neen_percentagem) / (mac_percentagem + neen_percentagem));
}

function tfcMediaCalculate(config, trabalho, trabalho_percentagem, defesa, defesa_percentagem) {
    return parseInt(Math.round((trabalho * (trabalho_percentagem / 100)) + (defesa * (defesa_percentagem / 100))));
}
;