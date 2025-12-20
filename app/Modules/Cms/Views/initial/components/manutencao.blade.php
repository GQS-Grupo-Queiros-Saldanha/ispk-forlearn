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
