<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site em Manutenção</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #1a2a6c, #3a7bd5, #00d2ff);
            color: white;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            overflow-x: hidden;
        }
        
        .maintenance-container {
            max-width: 800px;
            width: 100%;
            text-align: center;
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px 30px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            overflow: hidden;
        }
        
        .maintenance-container::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 30px 30px;
            animation: float 20s linear infinite;
            z-index: -1;
        }
        
        @keyframes float {
            0% { transform: translate(0, 0) rotate(0deg); }
            100% { transform: translate(-30px, -30px) rotate(360deg); }
        }
        
        .icon-container {
            font-size: 80px;
            margin-bottom: 20px;
            color: #FFD700;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        h1 {
            font-size: 2.8rem;
            margin-bottom: 15px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .highlight {
            color: #FFD700;
            font-weight: 700;
        }
        
        .subtitle {
            font-size: 1.3rem;
            margin-bottom: 30px;
            line-height: 1.6;
            opacity: 0.9;
        }
        
        .message {
            background-color: rgba(255, 255, 255, 0.15);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 35px;
            text-align: left;
            border-left: 5px solid #FFD700;
        }
        
        .message p {
            margin-bottom: 15px;
            font-size: 1.1rem;
            line-height: 1.5;
        }
        
        .message p:last-child {
            margin-bottom: 0;
        }
        
        .progress-container {
            background-color: rgba(255, 255, 255, 0.15);
            border-radius: 10px;
            height: 20px;
            margin-bottom: 40px;
            overflow: hidden;
        }
        
        .progress-bar {
            height: 100%;
            width: 75%;
            background: linear-gradient(90deg, #FFD700, #FFA500);
            border-radius: 10px;
            position: relative;
            animation: progress-animation 3s ease-in-out infinite alternate;
        }
        
        @keyframes progress-animation {
            0% { width: 70%; }
            100% { width: 80%; }
        }
        
        .progress-text {
            margin-top: 10px;
            font-size: 1rem;
            opacity: 0.9;
        }
        
        .details {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            margin-bottom: 40px;
            text-align: left;
        }
        
        .detail-item {
            flex: 1;
            min-width: 200px;
            margin: 15px;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            transition: transform 0.3s, background-color 0.3s;
        }
        
        .detail-item:hover {
            transform: translateY(-5px);
            background-color: rgba(255, 255, 255, 0.2);
        }
        
        .detail-item i {
            font-size: 30px;
            margin-bottom: 15px;
            color: #FFD700;
        }
        
        .detail-item h3 {
            font-size: 1.3rem;
            margin-bottom: 10px;
        }
        
        .contact {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .contact p {
            font-size: 1.1rem;
            margin-bottom: 10px;
        }
        
        .email {
            color: #FFD700;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .email:hover {
            color: #FFFFFF;
            text-decoration: underline;
        }
        
        .social-icons {
            margin-top: 25px;
        }
        
        .social-icons a {
            display: inline-block;
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
            width: 45px;
            height: 45px;
            line-height: 45px;
            border-radius: 50%;
            margin: 0 10px;
            font-size: 20px;
            transition: all 0.3s;
        }
        
        .social-icons a:hover {
            background-color: #FFD700;
            color: #1a2a6c;
            transform: translateY(-5px);
        }
        
        .countdown {
            font-size: 1.2rem;
            margin-top: 25px;
            font-weight: 600;
            color: #FFD700;
        }
        
        .footer {
            margin-top: 40px;
            font-size: 0.9rem;
            opacity: 0.7;
        }
        
        @media (max-width: 768px) {
            h1 {
                font-size: 2.2rem;
            }
            
            .subtitle {
                font-size: 1.1rem;
            }
            
            .detail-item {
                min-width: 100%;
            }
        }
        
        @media (max-width: 480px) {
            .maintenance-container {
                padding: 30px 20px;
            }
            
            h1 {
                font-size: 1.8rem;
            }
            
            .icon-container {
                font-size: 60px;
            }
        }
    </style>
</head>
<body>
    <div class="maintenance-container">
        <div class="icon-container">
            <i class="fas fa-tools"></i>
        </div>
        
        <h1>Página em <span class="highlight">Manutenção Profissional</span></h1>
        
        <p class="subtitle">Estamos trabalhando para melhorar sua experiência. Voltamos em breve com novidades!</p>
        
        <div class="message">
            <p><i class="fas fa-info-circle"></i> Estamos realizando uma manutenção programada para trazer melhorias significativas ao nosso site. Pedimos desculpas por qualquer inconveniente e agradecemos sua paciência.</p>
            <p><i class="fas fa-clock"></i> Nossa equipe está trabalhando diligentemente para concluir as atualizações o mais rápido possível.</p>
        </div>
        
        <div class="progress-container">
            <div class="progress-bar"></div>
        </div>
        <div class="progress-text">Progresso da manutenção: 75%</div>
        
        <div class="details">
            <div class="detail-item">
                <i class="fas fa-cogs"></i>
                <h3>Atualizações Técnicas</h3>
                <p>Implementação de novas funcionalidades e otimização do sistema.</p>
            </div>
            
            <div class="detail-item">
                <i class="fas fa-paint-brush"></i>
                <h3>Renovação de Design</h3>
                <p>Melhorias na interface para uma experiência mais intuitiva e agradável.</p>
            </div>
            
            <div class="detail-item">
                <i class="fas fa-shield-alt"></i>
                <h3>Aprimoramento de Segurança</h3>
                <p>Atualizações para garantir a proteção dos seus dados e privacidade.</p>
            </div>
        </div>
        
        <div class="contact">
            <p>Para emergências, entre em contato conosco:</p>
            <a href="mailto:suporte@empresa.com" class="email">suporte@empresa.com</a>
            
            <div class="social-icons">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-linkedin-in"></i></a>
            </div>
            
            <div class="countdown">
                <i class="far fa-clock"></i> Previsão de retorno: 48 horas
            </div>
        </div>
        
        <div class="footer">
            <p>&copy; 2023 Empresa. Todos os direitos reservados.</p>
        </div>
    </div>

    <script>
        // Simulação de contador regressivo
        document.addEventListener('DOMContentLoaded', function() {
            const countdownElement = document.querySelector('.countdown');
            let hours = 48;
            
            function updateCountdown() {
                countdownElement.innerHTML = `<i class="far fa-clock"></i> Previsão de retorno: ${hours} horas`;
                
                if (hours > 0) {
                    hours--;
                }
            }
            
            // Atualiza a cada hora (para demonstração, atualizamos a cada 10 segundos)
            setInterval(updateCountdown, 10000);
            
            // Animações para os ícones de detalhes
            const detailItems = document.querySelectorAll('.detail-item');
            detailItems.forEach(item => {
                item.addEventListener('mouseenter', function() {
                    const icon = this.querySelector('i');
                    icon.style.transform = 'rotate(20deg)';
                });
                
                item.addEventListener('mouseleave', function() {
                    const icon = this.querySelector('i');
                    icon.style.transform = 'rotate(0deg)';
                });
            });
            
            // Animações para a barra de progresso
            const progressBar = document.querySelector('.progress-bar');
            let width = 75;
            let direction = 1;
            
            function animateProgressBar() {
                if (width >= 80) direction = -1;
                if (width <= 70) direction = 1;
                
                width += direction * 0.5;
                progressBar.style.width = width + '%';
                
                // Atualiza o texto de progresso
                document.querySelector('.progress-text').textContent = 
                    `Progresso da manutenção: ${Math.round(width)}%`;
            }
            
            setInterval(animateProgressBar, 3000);
        });
    </script>
</body>
</html>