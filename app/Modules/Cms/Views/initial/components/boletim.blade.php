<?php
use App\Modules\Cms\Controllers\mainController;
?>

<style> 
    .tabela_pauta tbody tr td { font-weight: normal !important; } 
    .tabela_pauta tbody tr .text-bold { font-weight: 600 !important; } 
    .bg0 { background-color: #2f5496 !important; color: white; } 
    .bg1 { background-color: #8eaadb !important; } 
    .bg2 { background-color: #d9e2f3 !important; } 
    .bg3 { background-color: #fbe4d5 !important; } 
    .bg4 { background-color: #f4b083 !important; } 
    .bgmac { background-color: #a5c4ff !important; } 
    .cf1 { background-color: #4888ffdb !important; } 
    .rec { background-color: #a5c4ff !important; } 
    .fn { background-color: #1296ff !important; } 
    .bo1 { border: 1px solid white!important; } 
    table tr .small, table tr .small { font-size: 11px !important; } 
    .for-green { background-color: #00ff89 !important; } 
    .for-blue { background-color: #cce5ff !important; z-index: 1000; } 
    .for-red { background-color: #f5342ec2 !important; } 
    .for-yellow { background-color: #f39c12 !important; } 
    .boletim_text { font-weight: normal !important; } 
    .barra { color: #f39c12 !important; font-weight: bold; } 
    .semestreA, .semestre2{ } 
</style>

@if (isset($articles['dividas']['pending']) && $articles['dividas']['pending'] > 0)
    <div class="alert alert-warning text-dark font-bold">
        Para visualizar as notas lançadas, dirija-se a Tesouraria para regularizar os seus pagamentos!
    </div>
@elseif (auth()->check() && auth()->user()->id != 592)
    <div class="card border-warning shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-center mb-3">
                <i class="bi bi-tools fs-3 text-warning me-3"></i>
                <div>
                    <h5 class="card-title fw-bold text-dark mb-1">MANUTENÇÃO PROGRAMADA</h5>
                    <p class="card-text text-muted mb-0">
                        <i class="bi bi-clock-history me-1"></i>
                        Em andamento • Previsão: 48 horas
                    </p>
                </div>
            </div>
            
            <div class="alert alert-warning text-dark fw-bold py-3 px-3 mb-3">
                <i class="bi bi-exclamation-circle-fill me-2"></i>
                O sistema está em manutenção para melhorias técnicas.
            </div>
            
            <!-- Barra de Progresso com ID para controle JS -->
            <div class="progress mb-3" style="height: 12px;">
                <div id="maintenanceProgressBar" 
                    class="progress-bar bg-warning progress-bar-striped progress-bar-animated" 
                    role="progressbar" 
                    style="width: 0%;"
                    aria-valuenow="0" 
                    aria-valuemin="0" 
                    aria-valuemax="100">
                </div>
            </div>
            
            <div class="row text-muted small mb-2">
                <div class="col-6">
                    <span><i class="bi bi-calendar-check me-1"></i> Início: <span id="startTime">{{ now()->format('d/m H:i') }}</span></span>
                </div>
                <div class="col-6 text-end">
                    <span><i class="bi bi-calendar-event me-1"></i> Término: <span id="endTime">{{ now()->addHours(48)->format('d/m H:i') }}</span></span>
                </div>
            </div>
            
            <!-- Contador Regressivo em Tempo Real -->
            <div class="alert alert-light border text-center py-2 mt-2">
                <div class="row">
                    <div class="col-3">
                        <div class="fw-bold text-warning fs-5" id="countdownHours">48</div>
                        <small class="text-muted">Horas</small>
                    </div>
                    <div class="col-3">
                        <div class="fw-bold text-warning fs-5" id="countdownMinutes">00</div>
                        <small class="text-muted">Minutos</small>
                    </div>
                    <div class="col-3">
                        <div class="fw-bold text-warning fs-5" id="countdownSeconds">00</div>
                        <small class="text-muted">Segundos</small>
                    </div>
                    <div class="col-3">
                        <div class="fw-bold text-warning fs-5" id="progressPercent">0%</div>
                        <small class="text-muted">Concluído</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {

        // ⏱ Duração total: 48 horas
        const totalMaintenanceTime = 48 * 60 * 60 * 1000;

        // 🕔 Início: ontem às 17:00
        const maintenanceStartDate = new Date();
        maintenanceStartDate.setDate(maintenanceStartDate.getDate() - 1);
        maintenanceStartDate.setHours(17, 0, 0, 0);
        const maintenanceStartTime = maintenanceStartDate.getTime();

        // 🏁 Fim da manutenção
        const maintenanceEndTime = maintenanceStartTime + totalMaintenanceTime;

        // Atualiza datas no HTML
        const startDate = new Date(maintenanceStartTime);
        const endDate = new Date(maintenanceEndTime);

        document.getElementById('startTime').textContent =
            `${String(startDate.getDate()).padStart(2, '0')}/${String(startDate.getMonth() + 1).padStart(2, '0')} ` +
            `${String(startDate.getHours()).padStart(2, '0')}:${String(startDate.getMinutes()).padStart(2, '0')}`;

        document.getElementById('endTime').textContent =
            `${String(endDate.getDate()).padStart(2, '0')}/${String(endDate.getMonth() + 1).padStart(2, '0')} ` +
            `${String(endDate.getHours()).padStart(2, '0')}:${String(endDate.getMinutes()).padStart(2, '0')}`;

        function updateMaintenanceStatus() {
            const now = Date.now();
            const timePassed = now - maintenanceStartTime;
            const timeRemaining = maintenanceEndTime - now;

            let percentage = Math.min((timePassed / totalMaintenanceTime) * 100, 100);
            percentage = Math.max(percentage, 0);

            const progressBar = document.getElementById('maintenanceProgressBar');
            progressBar.style.width = `${percentage}%`;
            progressBar.setAttribute('aria-valuenow', Math.round(percentage));

            document.getElementById('progressPercent').textContent = `${Math.round(percentage)}%`;

            if (timeRemaining > 0) {
                const hours = Math.floor(timeRemaining / (1000 * 60 * 60));
                const minutes = Math.floor((timeRemaining % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((timeRemaining % (1000 * 60)) / 1000);

                document.getElementById('countdownHours').textContent = String(hours).padStart(2, '0');
                document.getElementById('countdownMinutes').textContent = String(minutes).padStart(2, '0');
                document.getElementById('countdownSeconds').textContent = String(seconds).padStart(2, '0');

                progressBar.classList.remove('bg-warning', 'bg-info', 'bg-success');
                if (percentage > 80) progressBar.classList.add('bg-success');
                else if (percentage > 50) progressBar.classList.add('bg-info');
                else progressBar.classList.add('bg-warning');

            } else {
                progressBar.style.width = '100%';
                progressBar.classList.remove('bg-warning', 'bg-info');
                progressBar.classList.add('bg-success');
                document.getElementById('progressPercent').textContent = '100%';
                document.getElementById('countdownHours').textContent = '00';
                document.getElementById('countdownMinutes').textContent = '00';
                document.getElementById('countdownSeconds').textContent = '00';
            }
        }

        updateMaintenanceStatus();
        setInterval(updateMaintenanceStatus, 1000);
    });
</script>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Adicione no cabeçalho se usar animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
@else
    @if (is_object($percurso) && count($percurso) > 0)
        @php
            $semestres = ['1', '2'];
            $disciplina_count = 0;
            
            // Helper functions para cálculos
            function calcularNotaMAC($pf1_nota, $pf1_percentagem, $pf2_nota, $pf2_percentagem, $oa_nota, $oa_percentagem) {
                $mac_calculo = 0;
                $total_percentagem = 0;
                
                if ($pf1_nota !== null && $pf1_nota !== '') {
                    $mac_calculo += $pf1_nota * $pf1_percentagem;
                    $total_percentagem += $pf1_percentagem;
                }
                
                if ($pf2_nota !== null && $pf2_nota !== '') {
                    $mac_calculo += $pf2_nota * $pf2_percentagem;
                    $total_percentagem += $pf2_percentagem;
                }
                
                if ($oa_nota !== null && $oa_nota !== '') {
                    $mac_calculo += $oa_nota * $oa_percentagem;
                    $total_percentagem += $oa_percentagem;
                }
                
                if ($total_percentagem > 0) {
                    $mac_nota = $mac_calculo / $total_percentagem;
                    return round($mac_nota);
                }
                
                return null;
            }
            
            function determinarEstado($nota, $config, $tipo = 'mac') {
                if ($nota === null || $nota === '') {
                    return ['estado' => '-', 'cor' => 'for-blue'];
                }
                // Verificar se $config existe e tem as propriedades necessárias
                if (!isset($config) || !is_object($config)) {
                    return ['estado' => 'Indefinido', 'cor' => 'for-blue'];
                }
                
                if ($tipo === 'mac') {
                    // APROVADO TEM SEMPRE PRIORIDADE
                    if ($nota >= $config->mac_nota_dispensa && $nota <= 20) {
                        return ['estado' => 'Aprovado(a)', 'cor' => 'for-green'];
                    }

                    // EXAME SÓ SE FOR NEGATIVA
                    if ($nota >= $config->exame_nota_inicial && $nota < $config->mac_nota_dispensa) {
                        return ['estado' => 'Exame', 'cor' => 'for-yellow'];
                    }

                    // RECURSO
                    if ($nota >= 0 && $nota < $config->exame_nota_inicial) {
                        return ['estado' => 'Recurso', 'cor' => 'for-red'];
                    }
                }
                
                if ($tipo === 'exame') {
                    if ($nota >= $config->mac_nota_dispensa && $nota <= 20) {
                        return ['estado' => 'Aprovado(a)', 'cor' => 'for-green'];
                    }
                    if ($nota >= $config->exame_nota_inicial && $nota < $config->mac_nota_dispensa) {
                        return ['estado' => 'Recurso', 'cor' => 'for-red'];
                    }
                    if ($nota >= 0 && $nota < $config->exame_nota_inicial) {
                        return ['estado' => 'Recurso', 'cor' => 'for-red'];
                    }
                }
                
                if ($tipo === 'final') {
                    if ($nota >= 10 && $nota <= 20) {
                        return ['estado' => 'Aprovado(a)', 'cor' => 'for-green'];
                    } else {
                        return ['estado' => 'Reprovado(a)', 'cor' => 'for-red'];
                    }
                }
                
                // Estado padrão se nada se aplicar
                return ['estado' => 'Indefinido', 'cor' => 'for-blue'];
            }

            function calcularEstadoFinal($mac_nota, $exame_nota, $recurso_nota, $especial_nota, $exame_oral_nota, $config, $exam_only, $mac_percentagem, $neen_percentagem) {
                // Inicializar variáveis de resultado
                $resultado = [
                    'estado_final' => 'Indefinido',
                    'color_final' => 'for-blue',
                    'nota_final' => '-',
                    'aprovado' => false,
                    'recurso' => false,
                    'exame' => false,
                    'exame_oral' => false,
                    'classificacao_final' => 0
                ];
                
                // 1. Verificar se tem nota de exame especial (tem prioridade máxima)
                if ($especial_nota !== null) {
                    $estado = determinarEstado($especial_nota, $config, 'final');
                    $resultado['estado_final'] = $estado['estado'];
                    $resultado['color_final'] = $estado['cor'];
                    $resultado['nota_final'] = $especial_nota;
                    $resultado['classificacao_final'] = $especial_nota;
                    $resultado['aprovado'] = ($estado['estado'] === 'Aprovado(a)');
                    return $resultado;
                }
                
                // 2. Verificar se tem nota de recurso
                if ($recurso_nota !== null) {
                    $estado = determinarEstado($recurso_nota, $config, 'final');
                    $resultado['estado_final'] = $estado['estado'];
                    $resultado['color_final'] = $estado['cor'];
                    $resultado['nota_final'] = $recurso_nota;
                    $resultado['classificacao_final'] = $recurso_nota;
                    $resultado['aprovado'] = ($estado['estado'] === 'Aprovado(a)');
                    $resultado['recurso'] = true;
                    return $resultado;
                }
                
                // 3. Verificar se é disciplina apenas de exame
                if ($exam_only == 1 && $exame_nota !== null) {
                    $estado = determinarEstado($exame_nota, $config, 'final');
                    $resultado['estado_final'] = $estado['estado'];
                    $resultado['color_final'] = $estado['cor'];
                    $resultado['nota_final'] = $exame_nota;
                    $resultado['classificacao_final'] = $exame_nota;
                    $resultado['aprovado'] = ($estado['estado'] === 'Aprovado(a)');
                    $resultado['exame'] = true;
                    return $resultado;
                }
                
                // 4. Verificar estado baseado apenas no MAC (se não tem exame)
                if ($exame_nota === null &&  $mac_nota !== null) {
                    $estado = determinarEstado($mac_nota, $config, 'mac');
                    $resultado['estado_final'] = $estado['estado'];
                    $resultado['color_final'] = $estado['cor'];
                    $resultado['nota_final'] = $mac_nota;
                    $resultado['classificacao_final'] = $mac_nota;
                    $resultado['aprovado'] = ($estado['estado'] === 'Aprovado(a)');
                    $resultado['exame'] = ($estado['estado'] === 'Exame');
                    $resultado['recurso'] = ($estado['estado'] === 'Recurso');
                    return $resultado;
                }
                
                // 5. Calcular com exame normal (escrito + oral se necessário)
                if ($exame_nota !== null) {
                    // Calcular classificação após exame
                    $exame_calc = round($exame_nota);
                    $classificacao_exame = 0;
                    
                    if ($exam_only == 1) {
                        $classificacao_exame = $exame_calc;
                    } else {
                        $classificacao_exame = round(($mac_nota * $mac_percentagem) + ($exame_calc * $neen_percentagem));
                    }
                    
                    // Verificar se precisa de exame oral
                    if (isset($config->exame_oral_final, $config->mac_nota_recurso) &&
                        $exame_calc > $config->mac_nota_recurso &&
                        $exame_calc <= round($config->exame_oral_final) &&
                        $exame_oral_nota === null) {
                        // Precisa de exame oral mas ainda não tem
                        $resultado['estado_final'] = 'Exame';
                        $resultado['color_final'] = 'for-yellow';
                        $resultado['nota_final'] = '-';
                        $resultado['classificacao_final'] = $classificacao_exame;
                        $resultado['exame'] = true;
                        $resultado['exame_oral'] = true;
                        return $resultado;
                    }
                    
                    // Se tem exame oral, calcular com ele
                    if ($exame_oral_nota !== null) {
                        $oral_calc = round($exame_oral_nota);
                        if ($exam_only == 1) {
                            $classificacao_exame = $oral_calc;
                        } else {
                            $classificacao_exame = round(($mac_nota * $mac_percentagem) + ($oral_calc * $neen_percentagem));
                        }
                    }
                    
                    // Determinar estado final
                    $estado = determinarEstado($classificacao_exame, $config, 'exame');
                    $resultado['estado_final'] = $estado['estado'];
                    $resultado['color_final'] = $estado['cor'];
                    $resultado['nota_final'] = $classificacao_exame;
                    $resultado['classificacao_final'] = $classificacao_exame;
                    $resultado['aprovado'] = ($estado['estado'] === 'Aprovado(a)');
                    $resultado['exame'] = true;
                    $resultado['exame_oral'] = ($exame_oral_nota !== null);
                    
                    return $resultado;
                }
                
                // 6. Estado padrão (apenas MAC sem exame)
                if ($mac_nota !== null) {
                    $estado = determinarEstado($mac_nota, $config, 'mac');
                    $resultado['estado_final'] = $estado['estado'];
                    $resultado['color_final'] = $estado['cor'];
                    $resultado['nota_final'] = $mac_nota;
                    $resultado['classificacao_final'] = $mac_nota;
                    $resultado['aprovado'] = ($estado['estado'] === 'Aprovado(a)');
                    $resultado['exame'] = ($estado['estado'] === 'Exame');
                    $resultado['recurso'] = ($estado['estado'] === 'Recurso');
                }
                
                return $resultado;
            }

        @endphp
        
        @foreach($semestres as $semestreActual)
            <table class="table tabela_pauta table-striped table-hover tabela_pauta" id="{{ 'tabela_pauta_student' . $semestreActual }}">
                <thead>
                    <tr>
                        <td colspan="3" class="boletim_text">
                            @if (isset($matriculations->course))
                                <b>{{ $matriculations->course }}</b>
                                <as class="barra">|</as> Ano: <b>{{ $matriculations->course_year }}º</b>
                                <as class="barra">|</as> Semestre: <b>{{ $semestreActual . 'º'}}</b>
                                <as class="barra">|</as> Turma: <b>{{ $matriculations->classe }}</b>
                            @endif
                        </td>
                        <td colspan="5" class="text-center bgmac bo1 p-top" style="border-bottom: 1px solid white;">MAC</td>
                        <td colspan="2" class="text-center bg1 p-top">EXAME</td>
                        <td class="text-center cf1 bo1 p-top" colspan="2">CLASSIFICAÇÃO</td>
                        <td class="rec bo1 text-center p-top" colspan="4">EXAME</td>
                        <td class="fn bo1 text-center p-top" colspan="2">CLASSIFICAÇÃO</td>
                    </tr>
                    <tr style="text-align: center">
                        <th class="bg1 bo1">#</th>
                        <th class="text-center small bg1 bo1">CÓDIGO</th>
                        <th class="bg1 bo1">DISCIPLINA</th>
                        <th class="bgmac bo1">PF1</th>
                        <th class="bgmac bo1">PF2</th>
                        <th class="bgmac bo1">OA</th>
                        <th colspan="2" class="bgmac bo1">MÉDIA</th>
                        <th class="bg1 bo1">ESCRITO</th>
                        <th class="bg1 bo1">ORAL</th>
                        <th class="cf1 bo1" colspan="2">MAC + EXAME</th>
                        <th class="rec bo1" colspan="2">RECURSO</th>
                        <th class="rec bo1" colspan="2">ESPECIAL</th>
                        <th class="fn bo1" colspan="2">FINAL</th>
                    </tr>
                </thead>
                
                @foreach ($disciplines as $index => $item_DISC)
                    @if($index[3] == $semestreActual)
                        @php
                            $disciplina_count++;
                            $par = ($disciplina_count % 2 == 0) ? 'bg-white' : null;
                            
                            // Inicializar variáveis com valores padrão seguros
                            $pf1_nota = null;
                            $pf1_percentagem = 0;
                            $pf2_nota = null;
                            $pf2_percentagem = 0;
                            $oa_nota = null;
                            $oa_percentagem = 0;
                            $neen_nota = null;
                            $oral_nota = null;
                            $recurso_nota = null;
                            $especial_nota = null;
                            
                            // Extrair notas do percurso
                            if(isset($percurso[$index])) {
                                foreach ($percurso[$index] as $itemNotas) {
                                    $nota_aluno = ($itemNotas->nota_anluno != null) ? floatval($itemNotas->nota_anluno) : null;
                                    
                                    switch ($itemNotas->MT_CodeDV) {
                                        case 'PF1':
                                            $pf1_nota = $nota_aluno;
                                            $pf1_percentagem = $itemNotas->percentagem_metrica / 100;
                                            break;
                                        case 'PF2':
                                            $pf2_nota = $nota_aluno;
                                            $pf2_percentagem = $itemNotas->percentagem_metrica / 100;
                                            break;
                                        case 'OA':
                                            $oa_nota = $nota_aluno;
                                            $oa_percentagem = $itemNotas->percentagem_metrica / 100;
                                            break;
                                        case 'Neen':
                                            $neen_nota = $nota_aluno;
                                            break;
                                        case 'oral':
                                            $oral_nota = $nota_aluno;
                                            break;
                                        case 'Recurso':
                                            $recurso_nota = $nota_aluno;
                                            break;
                                        case 'Exame_especial':
                                            $especial_nota = $nota_aluno;
                                            break;
                                    }
                                }
                            }
                            
                            // Obter ID da turma de forma segura
                            $id_turma = null;
                            if ($classes && method_exists($classes, 'first')) {
                                $turma = $classes->first(function($item) use ($item_DISC) {
                                    return isset($item_DISC->turma) && $item_DISC->turma == $item->display_name;
                                });
                                $id_turma = $turma ? $turma->id : null;
                            }
                            
                            // Variáveis de configuração
                            $exam_only = isset($item_DISC->e_f) ? $item_DISC->e_f : 0;
                            $mac_percentagem = isset($config->percentagem_mac) ? $config->percentagem_mac / 100 : 0.7;
                            $neen_percentagem = isset($config->percentagem_oral) ? $config->percentagem_oral / 100 : 0.3;
                            
                            // Calcular nota MAC
                            $mac_nota = null;
                            if ($pf1_nota !== null || $pf2_nota !== null || $oa_nota !== null) {
                                $mac_nota = calcularNotaMAC($pf1_nota, $pf1_percentagem, $pf2_nota, $pf2_percentagem, $oa_nota, $oa_percentagem);
                            }
                            
                            // Verificar pautas
                            $p_mac = ($id_turma && isset($item_DISC->id_disciplina) && isset($item_DISC->id_anoLectivo)) 
                                ? mainController::verificar_pauta($id_turma, $item_DISC->id_disciplina, $item_DISC->id_anoLectivo, 'Pauta Frequência')
                                : 0;
                                
                            $p_exame_oral = ($id_turma && isset($item_DISC->id_disciplina) && isset($item_DISC->id_anoLectivo))
                                ? mainController::verificar_pauta($id_turma, $item_DISC->id_disciplina, $item_DISC->id_anoLectivo, 'Pauta de Exame Oral')
                                : 0;
                                
                            $p_recurso = ($id_turma && isset($item_DISC->id_disciplina) && isset($item_DISC->id_anoLectivo))
                                ? mainController::verificar_pauta($id_turma, $item_DISC->id_disciplina, $item_DISC->id_anoLectivo, 'Pauta de Recurso')
                                : 0;
                                
                            $p_especial = ($id_turma && isset($item_DISC->id_disciplina) && isset($item_DISC->id_anoLectivo))
                                ? mainController::verificar_pauta($id_turma, $item_DISC->id_disciplina, $item_DISC->id_anoLectivo, 'Pauta Exame Especial')
                                : 0;
                                
                            $p_final = ($id_turma && isset($item_DISC->id_disciplina) && isset($item_DISC->id_anoLectivo))
                                ? mainController::verificar_pauta($id_turma, $item_DISC->id_disciplina, $item_DISC->id_anoLectivo, 'Pauta Final')
                                : 0;
                            
                            // Calcular estado final UMA ÚNICA VEZ
                            $estado_resultado = calcularEstadoFinal(
                                $mac_nota, 
                                $neen_nota, 
                                $recurso_nota, 
                                $especial_nota, 
                                $oral_nota, 
                                $config, 
                                $exam_only, 
                                $mac_percentagem, 
                                $neen_percentagem
                            );
                            
                            // Atribuir variáveis finais
                            $estado_final = $estado_resultado['estado_final'];
                            $color_final = $estado_resultado['color_final'];
                            $nota_final = $estado_resultado['nota_final'];
                            $aprovado = $estado_resultado['aprovado'];
                            $recurso = $estado_resultado['recurso'];
                            $exame = $estado_resultado['exame'];
                            $exame_oral = $estado_resultado['exame_oral'];
                            $classificacao = $estado_resultado['classificacao_final'];
                            
                            // Determinar estado para exibição no MAC (antes de qualquer exame)
                            $estado_mac_display = determinarEstado($mac_nota, $config, 'mac');
                            $estado_mac_texto = $estado_mac_display['estado'];
                            $color_mac_texto = $estado_mac_display['cor'];
                        @endphp
                        
                        <tbody>
                            <tr class="semestre{{ $semestreActual }} {{ $par ?? '' }}">
                                <td style='text-align: center'>{{ $disciplina_count }}</td>
                                <td style='text-align: center'>{{ $index }}</td>
                                <td style='text-align: left'>{{ $item_DISC->nome_disciplina ?? '' }}</td>
                                
                                <!-- Notas PF1, PF2, OA -->
                                <td class='text-bold text-center'>{{ $pf1_nota !== null ? number_format($pf1_nota, 2) : '-' }}</td>
                                <td class='text-bold text-center'>{{ $pf2_nota !== null ? number_format($pf2_nota, 2) : '-' }}</td>
                                <td class='text-bold text-center'>{{ $oa_nota !== null ? number_format($oa_nota, 2) : '-' }}</td>
                                
                                <!-- Média MAC -->
                                @if ($p_mac > 0)
                                    <td class='text-bold text-center'>{{  $mac_nota !== null ? $mac_nota : '-' }}</td>
                                    <td class="text-bold text-center {{ $color_mac_texto }}">{{ $estado_mac_texto }}</td>
                                @else
                                    <td style='text-align: center'>-</td>
                                    <td style='text-align: center'>-</td>
                                @endif
                                
                                <!-- Exame Escrito -->
                                @if ($neen_nota === null || $aprovado || $recurso)
                                    <td style='text-align: center'>-</td>
                                @else
                                    <td style='text-align: center'>{{ round($neen_nota) }}</td>
                                @endif
                                
                                <!-- Exame Oral -->
                                @if ($oral_nota === null || $aprovado || $recurso || !$exame_oral)
                                    <td style='text-align: center'>-</td>
                                @else
                                    <td style='text-align: center'>{{ round($oral_nota) }}</td>
                                @endif
                                
                                <!-- Classificação MAC + Exame -->
                                @if ($p_final > 0 && ($exame || $exame_oral) && !$recurso && $especial_nota === null)
                                    <td class='text-bold text-center'>{{ $classificacao !== null ? $classificacao : '-' }}</td>
                                    <td class="text-bold text-center {{ $color_final }}">{{ $estado_final }}</td>
                                @else
                                    <td style='text-align: center'>-</td>
                                    <td style='text-align: center'>-</td>
                                @endif
                                
                                <!-- Recurso -->
                                @if ($recurso_nota !== null && $p_recurso > 0 && !$aprovado)
                                    <td style='text-align: center'>{{ round($recurso_nota) }}</td>
                                    <td class="text-bold text-center {{ $color_final }}">{{ $estado_final }}</td>
                                @else
                                    <td style='text-align: center'>-</td>
                                    <td style='text-align: center'>-</td>
                                @endif
                                
                                <!-- Exame Especial -->
                                @if ($especial_nota !== null && $p_especial > 0 && !$aprovado)
                                    <td style='text-align: center'>{{ round($especial_nota) }}</td>
                                    <td class="text-bold text-center {{ $color_final }}">{{ $estado_final }}</td>
                                @else
                                    <td style='text-align: center'>-</td>
                                    <td style='text-align: center'>-</td>
                                @endif
                                
                                <!-- Nota Final -->
                                @if ($p_final > 0 && $nota_final !== '-')
                                    <td class='text-bold text-center'>{{ $nota_final }}</td>
                                    <td class="text-bold text-center {{ $color_final }}">{{ $estado_final }}</td>
                                @else
                                    <td class='text-bold text-center'>-</td>
                                    <td class='text-bold text-center'>-</td>
                                @endif
                            </tr>
                        </tbody>
                    @endif
                @endforeach
            </table>
        @endforeach
        
        @if(!isset($institution))
            <div class="row float-right btn-pdf-boletim" style="margin-right: 0.1!important;">
                <a class="btn" style="background-color:#0082f2;" target="_blank" href="{{ route('main.boletim_pdf', $matriculations->id) }}">
                    <i class="fa fa-file-pdf"></i> Boletim de notas
                </a>
            </div>
        @endif
    @elseif (is_object($percurso))
        <div class="alert alert-warning text-dark font-bold">Nenhuma nota foi lançada neste ano lectivo!</div>
    @else
        <div class="alert alert-warning text-dark font-bold">{{ $percurso }}!</div>
    @endif
@endif