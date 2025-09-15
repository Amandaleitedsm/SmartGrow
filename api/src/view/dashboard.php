<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="./css/estiloDashboard.css">
        <link rel="stylesheet" href="./css/menu.css">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
        <title>Document</title>
    </head>
    <body>
        <?php include 'includes/menu.php'; ?>
        <div class="home">
            <div class="ajustes">
                <div class="header">
                    <h1 class="title">Visão geral</h1>
                    <img src="./arquivos/logo.png" alt="SmartGrow Logo">
                </div>
                <h3 id="nPlantas">Plantas cadastradas: </h3>
                <div class="content">
                    <div class="direita">
                        <div class="boxDireita">
                            <p id="nRelatorio">Número de relatórios: </p>
                            <p id="nAnalise">Número de análises: </p>
                            <p id="nMediaAnalises">Média de análises por planta: </p>
                        </div>
                        <div class="boxDireita">
                            <h4>Alertas recentes</h4>
                            <p>Não há alertas no momento.</p>
                        </div>
                    </div>
                    <div class="esquerda">
                        <div class="boxEsquerda">
                            <h4>Relatórios não abertos</h4>
                            <p>Não há relatórios para serem abertos no momento.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script type="module">
            import ApiService from './ApiService.js';

            const userData = JSON.parse(localStorage.getItem("userData"));
            const token = userData?.data?.token;
            let id = null;
            if (token) {
                const payload = JSON.parse(atob(token.split(".")[1]));
                id = payload.private?.IdUsuario;
            }
            const api = new ApiService(token);

            const nAnalises = document.getElementById("nAnalise");
            const nPlantas = document.getElementById("nPlantas");
            const nMediaAnalises = document.getElementById("nMediaAnalises");
            const alertasContainer = document.querySelector('.boxDireita:nth-child(2)');

            // Calcula percentual saúde + umidade
            function calcularStatusPercentual(statusS, statusU) {
                let valor = 0;
                switch (statusS) {
                    case 'Boa': valor += 50; break;
                    case 'Regular': valor += 35; break;
                    case 'Ruim': valor += 20; break;
                    case 'Doente': valor += 5; break;
                }
                switch (statusU) {
                    case 'Alta': valor += 25; break;
                    case 'Regular': valor += 50; break;
                    case 'Baixa': valor += 25; break;
                }
                return valor;
            }

            async function carregarDados() {
                try {
                    // --------- pega plantas do usuário
                    let respPlantas = await api.getById("/plantas-usuarios/user", id);
                    let plantas = respPlantas.data.plantas || [];

                    // --------- pega análises
                    let respAnalises = await api.get("/minhas-plantas/analises");
                    let analises = respAnalises.data.análises || [];
                    if (!Array.isArray(analises)) analises = [analises];

                    // --------- métricas gerais
                    const media = plantas.length > 0 ? analises.length / plantas.length : 0;
                    nPlantas.textContent = "Plantas cadastradas: " + plantas.length;
                    nAnalises.textContent = "Número de análises: " + analises.length;
                    nMediaAnalises.textContent = "Média de análises por planta: " + media.toFixed(1);

                    // --------- alertas recentes (não visualizadas)        
                    // segura campos ausentes e transforma data corretamente
                    let visualizadas = JSON.parse(localStorage.getItem("analisesVisualizadas")) || [];

                    const alertasNaoVisualizadas = analises
                    .filter(a => !visualizadas.includes(a.ID) && a.status_saude && a.status_umidade)
                    .sort((a, b) => new Date(b.data_analise) - new Date(a.data_analise))
                    .slice(0, 5);

                    alertasContainer.innerHTML = '<h4>Alertas recentes</h4>';


                    if (alertasNaoVisualizadas.length === 0) {
                        const p = document.createElement('p');
                        p.textContent = 'Não há alertas no momento.';
                        alertasContainer.appendChild(p);
                    } else {
                        // Requisições paralelas
                        const promises = alertasNaoVisualizadas.map(a => api.getById("/plantas-usuarios", a.ID_planta_usuario));
                        const resultados = await Promise.all(promises);

                        resultados.forEach((resp, index) => {
                            const a = alertasNaoVisualizadas[index];
                            const detalhes = resp.data?.planta || {};
                            const statusPercentual = calcularStatusPercentual(a.status_saude, a.status_umidade);
                            const dataTexto = a.data_analise || 'sem data';
                            const nomePlanta = detalhes.apelido ? `${detalhes.apelido} - ` : '';

                            const p = document.createElement('p');
                            p.style.fontSize = '14px';
                            p.style.marginTop = '5px';
                            p.style.cursor = 'pointer';
                            p.style.color = '#444';
                            p.innerHTML = `<a href="analises.php?analise=${a.ID}">Análise n° ${a.ID} - ${dataTexto} - status: ${statusPercentual}%</a>`;

                            alertasContainer.appendChild(p);
                        });
                    }

                } catch (error) {
                    console.error("Erro ao carregar dados:", error);
                }
            }

            carregarDados();
        </script>
    </body>
</html>
