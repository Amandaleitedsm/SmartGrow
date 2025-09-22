<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Seus links CSS originais -->
    <link rel="stylesheet" href="./css/estiloAnalises.css">
    <link rel="stylesheet" href="./css/menu.css">
    <!-- Outros links (Font Awesome, Google Fonts) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <title>Análises</title>
</head>
<body>
    <!-- Inclusão do seu menu via PHP -->
    <?php include 'includes/menu.php'; ?>

    <div class="home"> 
        <div class="ajustes"> 
            <div class="header"> 
                <h1 class="title">Análises</h1> 
                <img src="./arquivos/logo.png" alt="SmartGrow Logo"> 
            </div> 
            
            <!-- Botão principal para iniciar a análise -->
            <button type="button" id="buttonAdd" class="ButtonAdd"> 
                <i class="fa-solid fa-plus"></i> Gerar análise
            </button>
            
            <!-- Div onde as análises antigas (do banco de dados ) serão renderizadas -->
            <div class="content" id="contentAnalises"></div>
            
            <!-- Mensagem para quando não houver análises -->
            <div id="semAnalises" class="sem-analises" style="display: none;">
                <span>:(</span>  

                <span>Não há análises para serem exibidas</span>
            </div>
        </div>
    </div>

    <!-- Modal genérico que será preenchido pelo JavaScript -->
    <div id="plantModal" class="modal">
        <div class="modal-content">
            <span id="closeModal" class="close">&times;</span>
            <div id="modalBody"></div>
        </div>
    </div>

    <!-- =================================================================== -->
    <!-- INÍCIO DO CÓDIGO JAVASCRIPT -->
    <!-- =================================================================== -->
    <script type="module">
        // 1. IMPORTAÇÕES
        // Importa a classe ApiService e a nova função para análise de imagem.
        import ApiService, { analisarImagemComAPI } from './ApiService.js';

        // 2. CONFIGURAÇÃO INICIAL E AUTENTICAÇÃO
        // Pega os dados do usuário e o token do localStorage para usar na API.
        const userData = JSON.parse(localStorage.getItem("userData"));
        const token = userData?.data?.token;
        const api = new ApiService(token);
        
        let idUsuario = null;
        if (token) {
            const payload = JSON.parse(atob(token.split(".")[1]));
            idUsuario = payload.private?.IdUsuario;
        }
        
        // 3. FUNÇÃO PARA CARREGAR O HISTÓRICO DE ANÁLISES (CÓDIGO ANTIGO)
        // Esta função busca e exibe as análises já salvas no seu banco de dados.
        // Se der erro no console aqui, não afeta a nova funcionalidade de IA.
        async function carregarAnalises() {
            console.log("Tentando carregar histórico de análises...");
            try {
                // ATENÇÃO: A rota '/minhas-plantas/analises' deve existir na sua API PHP.
                const API_DATA = await api.get("/minhas-plantas/analises"); 
                if (API_DATA && API_DATA.data && API_DATA.data.análises) {
                    const analises = API_DATA.data.análises;
                    console.log("Histórico de análises carregado:", analises);
                    // Aqui você colocaria seu código para renderizar os cards de histórico.
                } else {
                    document.getElementById("semAnalises").style.display = "block";
                }
            } catch (error) {
                console.error('Erro ao carregar histórico de análises (pode ser ignorado se a rota não existir):', error);
                document.getElementById("semAnalises").style.display = "block";
            }
        }
        // Chama a função para carregar o histórico assim que a página abre.
        carregarAnalises();


        // 4. NOVA LÓGICA PARA O BOTÃO "+ GERAR ANÁLISE"
        document.getElementById("buttonAdd").onclick = () => {
    const modalBody = document.getElementById("modalBody");

    // Volta a ser um modal simples que só pede a imagem
    modalBody.innerHTML = `
        <h2>Gerar Nova Análise</h2>
        <p>Selecione uma imagem para análise. Uma nova planta será criada em seu histórico.</p>
        <input type="file" id="imageUpload" accept="image/*" style="margin-top: 15px;"/>
        
        <div id="previewArea" style="margin-top: 20px; text-align: center;"></div>
        <div id="predictionResult" style="margin-top: 10px; font-weight: bold; text-align: center; font-size: 1.1em;"></div>
    `;

    document.getElementById("plantModal").style.display = "flex";

    document.getElementById("imageUpload").addEventListener("change", async (event) => {
        const file = event.target.files[0];
        if (!file) return;

        // Verifica se o idUsuario existe (se o usuário está logado)
        if (!idUsuario) {
            alert("Você precisa estar logado para salvar uma análise.");
            return;
        }

        const previewArea = document.getElementById("previewArea");
        const resultDiv = document.getElementById("predictionResult");

        previewArea.innerHTML = `<img src="${URL.createObjectURL(file)}" ...>`;
        resultDiv.innerText = "Analisando, por favor aguarde...";

        // Chama a API passando o arquivo e o ID do usuário
        const resultado = await analisarImagemComAPI(file, idUsuario);

        if (resultado.erro) {
            resultDiv.innerHTML = `<p style="color: red;">Erro: ${resultado.erro}</p>`;
        } else {
            resultDiv.innerHTML = `
                <p>Resultado: ${resultado.status}</p>
                <p>Confiança: ${resultado.confianca}</p>
                <p style="color: green; margin-top: 15px;">Análise salva com sucesso!</p>
            `;
            // Recarrega o histórico para mostrar a nova análise
            carregarAnalises(); 
        }
    });
};
        // 5. LÓGICA PARA FECHAR O MODAL (CÓDIGO ORIGINAL)
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