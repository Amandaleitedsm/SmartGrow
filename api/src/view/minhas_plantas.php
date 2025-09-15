<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/estiloMinhasPlantas.css">
    <link rel="stylesheet" href="./css/menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">

    <title>Minhas Plantas</title>
</head>
<body>
    <?php include 'includes/menu.php'; ?>
    
    <div class="home"> 
        <div class="ajustes"> 
            <div class="header"> 
                <h1 class="title">Minhas plantas</h1> 
                <img src="./arquivos/logo.png" alt="SmartGrow Logo"> 
            </div> 
            <button type="button" id="buttonAdd" class="ButtonAdd"> 
                <i class="fa-solid fa-plus"></i> Adicionar Planta
            </button>
            <div class="content" id="contentPlantas"></div>
            <div id="semPlantas" class="sem-plantas" style="display: none;">
                <span>:(</span><br>
                <span>Não há plantas para serem exibidas</span>
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

        // ------- Carregar plantas do usuário -------
        async function carregarPlantas() {
            const container = document.getElementById('contentPlantas');
            container.innerHTML = ""; // limpa antes de renderizar

            try {
                API_DATA = await api.getById("/plantas-usuarios/user", id);
                let plantas = API_DATA.data.plantas;
                 
                console.log(plantas);

                if (plantas != null){
                    document.getElementById("semPlantas").style.display = "none";
                    if (!Array.isArray(plantas)) {
                        plantas = [plantas];
                    }
                    for (const planta of plantas) {
                        API_DATA = await api.getById("/plantas", planta.IdPlanta);
                        const detalhes = API_DATA.data.planta;

                        const box = document.createElement('div');
                        box.classList.add('boxPlanta');

                        box.innerHTML = `
                            <div class="cabecalho">
                                <div class="row" style="display: flex; justify-content: space-between; align-items: center;">
                                    <div class="col-6"><h2 style="font-size: 28px;">${planta.apelido}</h2></div>
                                    <div class="col-6" style="justify-content: flex-end;">
                                        <button class="btnDetalhes" data-id="${planta.ID}" data-idPlanta="${planta.IdPlanta}" style="font-size: smaller; background:none; border:none; cursor:pointer;">
                                            Mais detalhes >
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="detalhes">
                                <div class="row" style="display: flex; justify-content: flex-start; align-items: center;">
                                    <div class="col-4">
                                        <div style="font-size:30px; color:#aaa;">No Image</div>
                                    </div>
                                    <div class="col-8" style="margin-left: 20px;">
                                        <p>ID: ${planta.ID}</p>
                                        <p>Localização: ${planta.localizacao}</p>
                                        <p>Nome comum: ${detalhes.nomeComum}</p>
                                        <p>Nome científico: ${detalhes.nome_cientifico}</p>
                                    </div>
                                </div>
                            </div>
                        `;

                        container.appendChild(box);
                    } 
                } else {
                    document.getElementById("semPlantas").style.display = "flex";
                }
                
            } catch (error) {
                console.error('Erro ao carregar plantas:', error);
            }
        }

        carregarPlantas();

        // ------- Botão de detalhes -------
        document.addEventListener("click", async (e) => {
            if (e.target.classList.contains("btnDetalhes")) {
                const idPlanta = e.target.getAttribute("data-idPlanta");
                const idPU = e.target.getAttribute("data-id");
                
                API_DATA = await api.getById("/plantas", idPlanta);
                const detalhes = API_DATA.data.planta;
                API_DATA = await api.getById("/plantas-usuarios", idPU);
                const plantas = API_DATA.data.planta; 

                const modalBody = document.getElementById("modalBody");
                modalBody.innerHTML = `
                    <h2>${detalhes.nomeComum} (${detalhes.nome_cientifico})</h2>
                    <div style="font-size:30px; color:#aaa;">No Image</div>
                    <p><strong>ID:</strong> ${plantas.ID}</p>
                    <p><strong>ID da planta:</strong> ${detalhes.IdPlanta}</p>
                    <p><strong>Apelido:</strong> ${plantas.apelido}</p>
                    <p><strong>Localização:</strong> ${plantas.localizacao ?? 'Não informada'}</p>
                    <p><strong>Tipo:</strong> ${detalhes.tipo}</p>
                    <p><strong>Clima:</strong> ${detalhes.clima}</p>
                    <p><strong>Região de origem:</strong> ${detalhes.regiao_origem}</p>
                    <p><strong>Luminosidade:</strong> ${detalhes.luminosidade}</p>
                    <p><strong>Frequência de rega:</strong> ${detalhes.frequencia_rega}</p>
                    <p><strong>Umidade mínima:</strong> ${detalhes.umidade_min}</p>
                    <p><strong>Umidade máxima:</strong> ${detalhes.umidade_max}</p>
                    <p><strong>Descrição:</strong> ${detalhes.descricao ?? 'Sem descrição cadastrada'}</p>
                `;

                // ------- Botão de excluir -------
                const btnExcluir = document.createElement('button');
                btnExcluir.textContent = "Excluir Planta";
                btnExcluir.style.backgroundColor = "#f44336";
                btnExcluir.style.color = "white";
                btnExcluir.style.border = "none";
                btnExcluir.style.padding = "5px 10px";
                btnExcluir.style.marginTop = "10px";
                btnExcluir.style.cursor = "pointer";

                btnExcluir.onclick = async () => {
                    if (confirm(`Deseja realmente excluir a planta "${plantas.apelido}"?`)) {
                        try {
                            await api.delete("/plantas-usuarios", idPU);
                            alert("Planta excluída com sucesso!");
                            document.getElementById("plantModal").style.display = "none";
                            carregarPlantas();
                        } catch (err) {
                            console.error(err);
                            alert("Erro ao excluir planta. Tente novamente.");
                        }
                    }
                };

                modalBody.appendChild(btnExcluir);

                const modalBodyStyle = document.getElementById("modalBody");
                modalBody.style.width = "650px";          // largura maior para detalhes
                modalBody.style.height = "auto";          // altura se ajusta ao conteúdo
                modalBody.style.maxHeight = "80vh";       // altura máxima da tela
                modalBody.style.overflowY = "auto";       // habilita scroll se precisar
                document.getElementById("plantModal").style.display = "flex";

            }
        });

        // ------- Botão adicionar planta -------
        document.getElementById("buttonAdd").onclick = async () => {
            try {
                const resposta = await api.get("/plantas");
                const listaPlantas = resposta.data.plantas;

                let options = listaPlantas.map(p => 
                    `<option value="${p.ID_planta} - ${p.nome_comum} (${p.nome_cientifico})"></option>`
                ).join("");

                document.getElementById("modalBody").innerHTML = `
                    <h2>Adicionar Planta</h2>
                    <form id="formAddPlanta" class="form-modal">
                        <label for="selectPlanta"><strong>Planta:</strong></label>
                        <input list="plantasList" id="selectPlanta" name="planta" placeholder="Digite ID, Nome Comum ou Nome Científico">
                        <datalist id="plantasList">${options}</datalist>

                        <label for="apelido"><strong>Apelido:</strong></label>
                        <input type="text" id="apelido" name="apelido" placeholder="Ex: Minha Zamioculca">

                        <label for="localizacao"><strong>Localização:</strong></label>
                        <input type="text" id="localizacao" name="localizacao" placeholder="Ex: Sala, Jardim, Varanda">

                        <button type="submit" >
                            Salvar
                        </button>
                    </form>
                `;

                const modalBodyStyle = document.getElementById("modalBody");
                modalBody.style.width = "550px";          // largura menor para formulário
                modalBody.style.height = "auto";
                modalBody.style.maxHeight = "80vh";
                modalBody.style.overflowY = "auto";
                document.getElementById("plantModal").style.display = "flex";


                // ------- Validação e envio -------
                document.getElementById("formAddPlanta").onsubmit = async (e) => {
                    e.preventDefault();

                    const idPlantaSelecionada = document.getElementById("selectPlanta").value.split(" - ")[0]; 
                    const apelido = document.getElementById("apelido").value.trim();
                    const localizacao = document.getElementById("localizacao").value.trim();

                    let aviso = document.getElementById("apelido-aviso");
                    if (!aviso) {
                        aviso = document.createElement("p");
                        aviso.id = "apelido-aviso";
                        aviso.style.color = "red";
                        aviso.style.fontSize = "14px";
                        document.getElementById("apelido").insertAdjacentElement("afterend", aviso);
                    }
                    aviso.textContent = "";

                    try {
                        const API_DATA = await api.getById("/plantas-usuarios/user", id);
                        let plantasUsuario = API_DATA.data.plantas;
                        if (plantasUsuario != null){
                            if (!Array.isArray(plantasUsuario)) {
                                plantasUsuario = [plantasUsuario];
                            }
                            const jaExiste = plantasUsuario.some(p => p.apelido.toLowerCase() === apelido.toLowerCase());

                            if (jaExiste) {
                                aviso.textContent = "⚠️ Já existe uma planta com esse apelido.";
                                return;
                            }
                        }

                        const obj = {
                            Dados: {
                                IdUsuario: id,
                                IdPlanta: idPlantaSelecionada,
                                Apelido: apelido,
                                Localizacao: localizacao
                            }
                        };
                        await api.post("/plantas-usuarios", obj);
                    

                        alert("Planta adicionada com sucesso!");
                        document.getElementById("plantModal").style.display = "none";
                        carregarPlantas();

                    } catch (err) {
                        console.error("Erro ao adicionar planta:", err);
                        aviso.textContent = "Erro ao salvar planta. Tente novamente.";
                    }
                };
            } catch (error) {
                console.error("Erro ao carregar lista de plantas:", error);
            }
        };

        // ------- Fechar modal -------
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
