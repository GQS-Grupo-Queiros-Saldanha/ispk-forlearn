<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boletim Acadêmico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --approved-color: #d4edda;
            --recovery-color: #f8d7da;
            --exam-color: #fff3cd;
            --approved-text: #155724;
            --recovery-text: #721c24;
            --exam-text: #856404;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }
        
        .card {
            border: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            border-radius: 12px;
            overflow: hidden;
        }
        
        .table-container {
            max-height: 500px;
            overflow-y: auto;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }
        
        .table {
            margin-bottom: 0;
            font-size: 0.9rem;
        }
        
        .table thead {
            position: sticky;
            top: 0;
            background-color: #343a40;
            z-index: 10;
        }
        
        .table th {
            border-bottom: 2px solid #495057;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
        }
        
        .status-aprovado {
            background-color: var(--approved-color) !important;
            color: var(--approved-text);
            font-weight: 600;
        }
        
        .status-recurso {
            background-color: var(--recovery-color) !important;
            color: var(--recovery-text);
            font-weight: 600;
        }
        
        .status-exame {
            background-color: var(--exam-color) !important;
            color: var(--exam-text);
            font-weight: 600;
        }
        
        .btn-download {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            border: none;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-download:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(106, 17, 203, 0.25);
        }
        
        .student-header {
            background: linear-gradient(135deg, #2c3e50 0%, #4a6491 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 12px 12px 0 0;
        }
        
        .summary-card {
            background: white;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }
        
        .summary-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2c3e50;
        }
        
        .summary-label {
            font-size: 0.85rem;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        @media (max-width: 768px) {
            .table-container {
                max-height: 400px;
            }
            
            .table {
                font-size: 0.85rem;
            }
            
            .student-header h2 {
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <div class="card">
                    <!-- Cabeçalho com informações do aluno -->
                    <div class="student-header">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h2 class="mb-1" id="studentName">Carregando...</h2>
                                <p class="mb-0 opacity-75" id="studentInfo">Matrícula: <span id="matricula">-</span> | Curso: <span id="curso">-</span></p>
                            </div>
                            <button class="btn btn-download" onclick="downloadBoletim()">
                                <i class="bi bi-download me-2"></i>Baixar Boletim
                            </button>
                        </div>
                    </div>

                    <!-- Cards de resumo -->
                    <div class="p-4 bg-light">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="summary-card text-center">
                                    <div class="summary-value" id="totalDisciplinas">0</div>
                                    <div class="summary-label">Disciplinas</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="summary-card text-center">
                                    <div class="summary-value" id="totalAprovadas">0</div>
                                    <div class="summary-label">Aprovadas</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="summary-card text-center">
                                    <div class="summary-value" id="totalExame">0</div>
                                    <div class="summary-label">Exame</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="summary-card text-center">
                                    <div class="summary-value" id="totalRecurso">0</div>
                                    <div class="summary-label">Recurso</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabela de disciplinas -->
                    <div class="p-4">
                        <div class="table-container">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="40%">Disciplina</th>
                                        <th width="15%">Nota</th>
                                        <th width="15%">Faltas</th>
                                        <th width="15%">Média</th>
                                        <th width="15%">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="disciplinasTable">
                                    <!-- Dados serão inseridos via JavaScript -->
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Carregando...</span>
                                            </div>
                                            <p class="mt-2 text-muted">Carregando dados...</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Variável global para armazenar o ID da matrícula
        let currentMatriculationId = null;
        
        // Função para processar os dados recebidos da API
        function processarDadosBoletim(data) {
            console.log('Dados recebidos:', data);
            
            // Atualizar variável global com o ID
            currentMatriculationId = data.id;
            
            // Atualizar informações do aluno
            document.getElementById('matricula').textContent = data.matricula || '-';
            document.getElementById('curso').textContent = data.dados?.curso || '-';
            
            // Atualizar nome do aluno (ajuste conforme sua estrutura de dados)
            const studentName = data.dados?.nome || 'Aluno não identificado';
            document.getElementById('studentName').textContent = studentName;
            
            // Processar disciplinas
            const tableBody = document.getElementById('disciplinasTable');
            tableBody.innerHTML = '';
            
            let totalAprovadas = 0;
            let totalExame = 0;
            let totalRecurso = 0;
            
            if (data.disciplinas && data.disciplinas.length > 0) {
                data.disciplinas.forEach(disciplina => {
                    const status = disciplina.status || 'aprovado'; // Supondo que o status venha do backend
                    const statusClass = getStatusClass(status);
                    const statusText = getStatusText(status);
                    
                    // Contar por status
                    if (status === 'aprovado') totalAprovadas++;
                    else if (status === 'exame') totalExame++;
                    else if (status === 'recurso') totalRecurso++;
                    
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="fw-medium">${disciplina.nome || 'Disciplina não informada'}</td>
                        <td>${disciplina.nota || '-'}</td>
                        <td>${disciplina.faltas || '0'}</td>
                        <td class="fw-semibold">${disciplina.media || '-'}</td>
                        <td><span class="badge ${statusClass}">${statusText}</span></td>
                    `;
                    tableBody.appendChild(row);
                });
            } else {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-journal-x display-6 d-block mb-3"></i>
                            Nenhuma disciplina encontrada
                        </td>
                    </tr>
                `;
            }
            
            // Atualizar resumo
            document.getElementById('totalDisciplinas').textContent = data.disciplinas?.length || 0;
            document.getElementById('totalAprovadas').textContent = totalAprovadas;
            document.getElementById('totalExame').textContent = totalExame;
            document.getElementById('totalRecurso').textContent = totalRecurso;
        }
        
        // Função para determinar a classe CSS do status
        function getStatusClass(status) {
            switch(status.toLowerCase()) {
                case 'aprovado': return 'status-aprovado';
                case 'recurso': return 'status-recurso';
                case 'exame': return 'status-exame';
                default: return 'bg-secondary';
            }
        }
        
        // Função para formatar o texto do status
        function getStatusText(status) {
            switch(status.toLowerCase()) {
                case 'aprovado': return 'Aprovado';
                case 'recurso': return 'Recurso';
                case 'exame': return 'Exame';
                default: return status;
            }
        }
        
        // Função para baixar o boletim em PDF
        function downloadBoletim() {
            if (!currentMatriculationId) {
                alert('ID da matrícula não disponível');
                return;
            }
            
            // Criar URL dinâmica com o ID
            const pdfUrl = `boletim_pdf/${currentMatriculationId}`;
            
            // Criar link temporário para download
            const link = document.createElement('a');
            link.href = pdfUrl;
            link.target = '_blank';
            link.download = `boletim_${currentMatriculationId}.pdf`;
            
            // Simular clique para iniciar download
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            // Feedback visual opcional
            const btn = document.querySelector('.btn-download');
            const originalHTML = btn.innerHTML;
            btn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Baixando...';
            btn.disabled = true;
            
            setTimeout(() => {
                btn.innerHTML = originalHTML;
                btn.disabled = false;
            }, 2000);
        }
        
        // Exemplo de chamada à API (substitua pela sua lógica real)
        function carregarDados() {
            // Simulação de dados - substitua pela sua chamada AJAX real
            const mockData = {
                matricula: "20230001",
                disciplinas: [
                    { nome: "Matemática Avançada", nota: 8.5, faltas: 2, media: 8.0, status: "aprovado" },
                    { nome: "Programação Web", nota: 6.0, faltas: 5, media: 6.5, status: "exame" },
                    { nome: "Banco de Dados", nota: 4.5, faltas: 3, media: 4.0, status: "recurso" },
                    { nome: "Redes de Computadores", nota: 9.0, faltas: 1, media: 9.0, status: "aprovado" },
                    { nome: "Engenharia de Software", nota: 7.5, faltas: 0, media: 7.5, status: "aprovado" },
                    { nome: "Inteligência Artificial", nota: 5.5, faltas: 4, media: 5.0, status: "exame" },
                    { nome: "Sistemas Operacionais", nota: 8.0, faltas: 2, media: 8.0, status: "aprovado" }
                ],
                dados: {
                    nome: "João Silva Santos",
                    curso: "Ciência da Computação",
                    periodo: "2023.1"
                },
                id: 12345 // ID para o PDF
            };
            
            // Para usar com sua API real:
            /*
            fetch('sua_api_endpoint')
                .then(response => response.json())
                .then(data => {
                    processarDadosBoletim(data);
                })
                .catch(error => {
                    console.error('Erro:', error);
                });
            */
            
            // Usando dados mockados para demonstração
            setTimeout(() => {
                processarDadosBoletim(mockData);
            }, 1000);
        }
        
        // Carregar dados quando a página carregar
        document.addEventListener('DOMContentLoaded', carregarDados);
    </script>
</body>
</html>