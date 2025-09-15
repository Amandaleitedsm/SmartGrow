<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/estiloAnalises.css">
    <link rel="stylesheet" href="./css/menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <title>Análises</title>
</head>
<body>
    <?php include 'includes/menu.php'; ?>
    <div class="home"> 
        <div class="ajustes"> 
            <div class="header"> 
                <h1 class="title">Análises</h1> 
                <img src="./arquivos/logo.png" alt="SmartGrow Logo"> 
            </div> 
            <button type="button" id="buttonAdd" class="ButtonAdd"> 
                <i class="fa-solid fa-plus"></i> Gerar análise
            </button>
            <div class="content" id="contentAnalises"></div>
            <div id="semAnalises" class="sem-analises" style="display: none;">
            <span>:(</span><br>
            <span>Não há análises para serem exibidas</span>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="plantModal" class="modal">
        <div class="modal-content">
            <span id="closeModal" class="close">&times;</span>
            <div id="modalBody"></div>
        </div>
    </div>

    

    <script type="module">
        import ApiService from './ApiService.js';

        const userData = JSON.parse(localStorage.getItem("userData"));
        const token = userData?.data?.token;

        let id = null;
        let role = null;
        let payload = null;

        if (token) {
            payload = JSON.parse(atob(token.split(".")[1]));
            role = payload.public?.Role;
            id = payload.private?.IdUsuario;
        }

        const api = new ApiService(token);
        let API_DATA;

        function getVisualizadas() {
            return JSON.parse(localStorage.getItem("analisesVisualizadas")) || [];
        }

        function setVisualizada(id) {
            let lista = getVisualizadas();
            if (!lista.includes(id)) {
                lista.push(id);
                localStorage.setItem("analisesVisualizadas", JSON.stringify(lista));
            }
        }

        function atualizarSaude(elemento, statusS, statusU) {
            let valor = 0;
            let cor = 'green';

            switch (statusS) {
                case 'Boa': valor += 50; cor = 'green'; break;
                case 'Regular': valor += 35; cor = 'yellow'; break;
                case 'Ruim': valor += 20; cor = 'orange'; break;
                case 'Doente': valor += 5; cor = 'red'; break;
            }
            switch (statusU) {
                case 'Alta': valor += 25; cor = 'green'; break;
                case 'Regular': valor += 50; cor = 'yellow'; break;
                case 'Baixa': valor += 25; cor = 'orange'; break;
            }
            if (valor == 100) cor = 'green';
            else if (valor >= 70) cor = 'yellow';
            else if (valor >= 45) cor = 'orange';
            else cor = 'red';
            elemento.style.width = valor + '%';
            elemento.style.backgroundColor = cor;
        }

        async function carregarAnalises(){
            const container = document.getElementById('contentAnalises');
            container.innerHTML = ""; // limpa antes de renderizar

            try {
                API_DATA = await api.get("/minhas-plantas/analises"); 
                let analises = API_DATA.data.análises;
                const visualizadas = getVisualizadas();

                console.log(analises);

                if (analises.length !== 0){
                    document.getElementById("semAnalises").style.display = "none";
                    if (!Array.isArray(analises)) {
                        analises = [analises];
                    }
                    for (const analise of analises) {
                        API_DATA = await api.getById("/plantas-usuarios", analise.ID_planta_usuario);
                        const detalhes = API_DATA.data.planta;
                        const statusS = analise.status_saude;
                        const statusU = analise.status_umidade;

                        const box = document.createElement('div');
                        box.classList.add('boxPlanta');

                        if (visualizadas.includes(analise.ID)) {
                            box.style.opacity = "0.5";
                        }
                        box.innerHTML = `
                            <div class="cabecalho">
                                <div class="row" style="display: flex; justify-content: space-between; align-items: center;">
                                    <div class="col-6"><h2 style="font-size: 20px;">${detalhes.apelido}</h2></div>
                                    <div class="col-6" style="justify-content: flex-end;">
                                        <button class="btnDetalhes" data-id="${analise.ID}" data-idPU="${analise.ID_planta_usuario}" style="font-size: smaller; background:none; border:none; cursor:pointer;">
                                            Mais detalhes >
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="detalhes">
                                <div class="row" style="display: flex; justify-content: flex-start; align-items: center; ">
                                    <div class="col-8" ">
                                        <p style="font-size: 12px;">${analise.data_analise}</p>
                                        <div class="status-container">
                                            <div class="status-bar">
                                                <div class="status-fill" id="statusFill"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        
                        container.appendChild(box);
                        const fill = box.querySelector('.status-fill');
                        atualizarSaude(fill, statusS, statusU);
                    } 
                } else {
                    document.getElementById("semPlantas").style.display = "flex";
                }
                
            } catch (error) {
                console.error('Erro ao carregar plantas:', error);
            }
        }

        carregarAnalises();

        
        // Quando a página carregar, verifica se tem parâmetro analise na URL
        window.addEventListener("DOMContentLoaded", async () => {
            const params = new URLSearchParams(window.location.search);
            const analiseId = params.get("analise");

            if (analiseId) {
                // espera carregar as análises primeiro
                await carregarAnalises();

                // dispara o mesmo clique do botão "Mais detalhes >"
                const btn = document.querySelector(`.btnDetalhes[data-id="${analiseId}"]`);
                if (btn) {
                    btn.click();
                    setVisualizada(parseInt(analiseId));
                }
            }
        });

        // ------- Botão de detalhes -------
        document.addEventListener("click", async (e) => {
            if (e.target.classList.contains("btnDetalhes")) {
                const idPU = e.target.getAttribute("data-idPU");
                const idAnl = e.target.getAttribute("data-id");
        
                setVisualizada(parseInt(idAnl));

                const boxPlanta = e.target.closest('.boxPlanta');
                if (boxPlanta) {
                    boxPlanta.style.opacity = "0.5";
                }

                API_DATA = await api.getById("/plantas-usuarios", idPU);
                const detalhes = API_DATA.data.planta;
                const planta = detalhes.IdPlanta;
                API_DATA = await api.getById("/plantas", planta);
                const plantas = API_DATA.data.planta; 
                API_DATA = await api.getById("/analises", idAnl);
                const analise = API_DATA.data.análise; 
                API_DATA = await api.getById("/analises/recomendacoes", idAnl);
                const recomendacoes = API_DATA.data.Recomendações;
                const modalBody = document.getElementById("modalBody");
                modalBody.innerHTML = `
                    <h2>${detalhes.apelido}</h2><p>Análise n°${analise.ID}</p>
                    <div style="font-size:30px; color:#aaa;">${analise.imagem 
                        ? `<img src="${analise.imagem}" alt="Imagem da análise" style="width:100%; max-height:150px; object-fit:cover;">` 
                        : `<div style="font-size:30px; color:#aaa;">No Image</div>`}</div>
                    <p><strong>Planta analisada: </strong>${plantas.nomeComum} (${plantas.nome_cientifico})</p>
                    <p><strong>Data da análise: </strong>${analise.data_analise}</p>
                    <p><strong>Status da saúde: </strong>${analise.status_saude}</p>
                    <p><strong>Status da umidade: </strong>${analise.status_umidade}</p>
                    <h3>Recomendação(ões): </h3>
                `;
                if (recomendacoes && recomendacoes.length > 0) {
                    const listaRec = document.createElement('ul');
                    listaRec.style.listStyle = 'none'; // remove bolinhas
                    listaRec.style.padding = '0';

                    recomendacoes.forEach(rec => {
                        const li = document.createElement('li');
                        li.style.marginBottom = '10px';
                        li.style.paddingLeft = '15px'; // tab para parecer indentado
                        li.style.borderLeft = '3px solid #4CAF50'; // opcional: linha vertical para destacar
                        li.style.backgroundColor = '#f9f9f9'; // opcional: fundo leve
                        li.style.padding = '10px';
                        li.style.borderRadius = '5px';

                        li.innerHTML = `
                            <div style="margin-bottom: 5px;"><strong>Motivo:</strong> ${rec.titulo}</div>
                            <div><strong>Recomendação:</strong> ${rec.descricao ? rec.descricao : '-'}</div>
                        `;

                        listaRec.appendChild(li);
                    });

                    modalBody.appendChild(listaRec);
                } else {
                    const semRec = document.createElement('p');
                    semRec.textContent = 'Nenhuma recomendação disponível.';
                    modalBody.appendChild(semRec);
                }


                const modalBodyStyle = document.getElementById("modalBody");
                modalBody.style.width = "650px";          // largura maior para detalhes
                modalBody.style.height = "auto";          // altura se ajusta ao conteúdo
                modalBody.style.maxHeight = "80vh";       // altura máxima da tela
                modalBody.style.overflowY = "auto";       // habilita scroll se precisar
                document.getElementById("plantModal").style.display = "flex";

            }

        });

        document.getElementById("buttonAdd").onclick = async () => {
            try {
                const plantas = await api.getById("/plantas-usuarios/user", id);

                // Monta checkboxes dinamicamente
                let checkboxes = plantas.data.plantas.map(p => 
                    `<div>
                        <input type="checkbox" name="plantas" value="${p.ID}" id="planta-${p.ID}">
                        <label for="planta-${p.ID}">${p.ID} - ${p.apelido}</label>
                    </div>`
                ).join("");

                // Monta o conteúdo do modal
                document.getElementById("modalBody").innerHTML = `
                    <h2>Gerar Análise</h2>
                    <form id="formAddRelatorio" class="form-modal">

                        <label><strong>Escolher plantas:</strong></label>
                        <div>
                            <input type="radio" name="tipoPlanta" value="todas" id="tipo-todas" checked>
                            <label for="tipo-todas">Todas as plantas</label>

                            <input type="radio" name="tipoPlanta" value="selecionar" id="tipo-selecionar">
                            <label for="tipo-selecionar">Selecionar plantas</label>
                        </div>

                        <div id="checkboxPlantas" style="display:none; margin-top:10px;">
                            ${checkboxes}
                        </div>

                        <button type="submit">Gerar</button>
                    </form>
                `;

                const modalBody = document.getElementById("modalBody");
                modalBody.style.width = "min(90%, 600px)";
                modalBody.style.height = "auto";
                modalBody.style.maxHeight = "80vh";
                modalBody.style.overflowY = "auto";

                document.getElementById("plantModal").style.display = "flex";

                // Mostrar/ocultar checkboxes
                document.getElementById("tipo-todas").onclick = () => {
                    document.getElementById("checkboxPlantas").style.display = "none";
                };
                document.getElementById("tipo-selecionar").onclick = () => {
                    document.getElementById("checkboxPlantas").style.display = "block";
                };

                // Envio do formulário
                document.getElementById("formAddRelatorio").onsubmit = async (e) => {
                    e.preventDefault();

                    const dataInicio = document.getElementById("dataInicio").value;
                    const dataFim = document.getElementById("dataFim").value;

                    let plantasSelecionadas = [];
                    const tipoPlanta = document.querySelector('input[name="tipoPlanta"]:checked').value;
                    
                    if(tipoPlanta === "todas") {
                        plantasSelecionadas = plantas.data.plantas.map(p => p.ID);
                    } else {
                        plantasSelecionadas = Array.from(document.querySelectorAll('#checkboxPlantas input:checked')).map(c => c.value);
                    }

                    const obj = {
                        dataInicio,
                        dataFim,
                        plantas: plantasSelecionadas
                    };

                    console.log("Dados do Análise:", obj);
                    // await api.post("/analises", obj);
                    

                    alert("Análise gerado com sucesso!");
                    document.getElementById("plantModal").style.display = "none";
                };

            } catch (error) {
                console.error("Erro ao abrir modal:", error);
            }
        };


        document.getElementById("closeModal").onclick = () => {
            document.getElementById("plantModal").style.display = "none";
        };
        window.onclick = (e) => {
            if (e.target.id === "plantModal") {
                document.getElementById("plantModal").style.display = "none";
            }
        };
    </script>
</body>
</html>
