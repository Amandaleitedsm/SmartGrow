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
            <!-- Conteúdo da página inicial -->
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

        // Pega o token dentro da resposta
        const token = userData?.data?.token;

        console.log("Token recuperado:", token);

        let id = null;
        let role = null;
        let payload = null;

        if (token) {
            payload = JSON.parse(atob(token.split(".")[1])); // decodifica o meio do JWT
            console.log("Payload do token:", payload);

            role = payload.public?.Role;
            id = payload.private?.IdUsuario;

            console.log("Role e ID do usuário:", role, id);
        }
        const api = new ApiService(token);
        let API_DATA;


        const nAnalises = document.getElementById("nAnalise");
        const nPlantas = document.getElementById("nPlantas");
        const nMediaAnalises = document.getElementById("nMediaAnalises");

        API_DATA = await api.getById("/plantas-usuarios/user", id);
        let plantas = API_DATA.data.plantas;
        API_DATA = await api.get("/minhas-plantas/analises");
        let analises = API_DATA.data.análises;
        // se não for array, transforma em array
        if (!Array.isArray(plantas)) {
            plantas = [plantas];
        }
        const media = analises.length / plantas.length;

        nPlantas.textContent = nPlantas.textContent + plantas.length;
        nAnalises.textContent = nAnalises.textContent + analises.length;
        nMediaAnalises.textContent = nMediaAnalises.textContent + media.toFixed(1);
        
    </script>

</body>
</html>