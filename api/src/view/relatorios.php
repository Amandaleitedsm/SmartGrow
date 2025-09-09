<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/estiloRelatorios.css">
    <link rel="stylesheet" href="./css/menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <title>Relatórios</title>
</head>
<body>
    <?php include 'includes/menu.php'; ?>
    <div class="home"> 
        <div class="ajustes"> 
            <div class="header"> 
                <h1 class="title">Relatórios</h1> 
                <img src="./arquivos/logo.png" alt="SmartGrow Logo"> 
            </div> 
            <button type="button" id="buttonAdd" class="ButtonAdd"> 
                <i class="fa-solid fa-plus"></i> Gerar relatório
            </button>
            <div class="content" id="contentPlantas">
                <div id="semRelatorios" class="sem-relatorios" style="display: none;">
                <span>:(</span><br>
                <span>Não há relatórios para serem exibidos</span>
                </div>
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

        // Simulação de resposta quando não houverem relatórios
        // Deve estar após a chamada da rota o if else abaixo
        // Deve haver lógica para carregar os relatórios quando existirem, provavelmente por função, tipo o listAll()

        const relatorios = []; // Simula resposta vazia para teste sem relatórios
        if (relatorios.length === 0) {
            document.getElementById("semRelatorios").style.display = "flex";
        } else {
            document.getElementById("semRelatorios").style.display = "none";
            // código que monta os relatórios normalmente
        }

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
                    <h2>Gerar relatório</h2>
                    <form id="formAddRelatorio" class="form-modal">
                        <label for="dataInicio"><strong>Data Início:</strong></label>
                        <input type="date" id="dataInicio" name="dataInicio" required>

                        <label for="dataFim"><strong>Data Fim:</strong></label>
                        <input type="date" id="dataFim" name="dataFim" required>

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

                    console.log("Dados do relatório:", obj);
                    // await api.post("/relatorios", obj);
                    

                    alert("Relatório gerado com sucesso!");
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
