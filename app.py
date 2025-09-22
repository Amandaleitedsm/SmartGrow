# ---------------------------------
# IMPORTAÇÕES NECESSÁRIAS
# ---------------------------------
from flask import Flask, request, jsonify
from flask_cors import CORS
import tensorflow as tf
from tensorflow.keras import models, utils
import numpy as np
from PIL import Image
import io
import os
import mysql.connector
from datetime import datetime

# ---------------------------------
# CONFIGURAÇÃO INICIAL
# ---------------------------------

# Caminhos para os arquivos do seu modelo
CAMINHO_MODELO = "modelo_balanceado.keras"
CAMINHO_CLASSES = "classes_balanceado.txt"
PASTA_UPLOADS = "uploads" # Nome da pasta onde as imagens analisadas serão salvas

# Configuração do seu banco de dados
db_config = {
    'host': 'localhost',
    'user': 'root',
    'password': '',
    'database': 'smartgrow_db' # Nome do seu banco de dados
}

# Variáveis globais que guardarão o modelo e as classes em memória
modelo = None
class_names = None

# ---------------------------------
# FUNÇÃO PARA CARREGAR O MODELO DE IA
# ---------------------------------
def carregar_ia():
    """Carrega o modelo Keras e a lista de classes em memória."""
    global modelo, class_names
    
    if not os.path.exists(CAMINHO_MODELO) or not os.path.exists(CAMINHO_CLASSES):
        print(f"❌ ERRO: Não foi possível encontrar '{CAMINHO_MODELO}' ou '{CAMINHO_CLASSES}'.")
        print("Por favor, garanta que os arquivos do modelo estão na mesma pasta que o app.py.")
        return

    try:
        print("Carregando modelo de IA...")
        modelo = models.load_model(CAMINHO_MODELO)
        with open(CAMINHO_CLASSES, "r") as f:
            class_names = [line.strip() for line in f.readlines()]
        print("✅ Modelo e classes carregados com sucesso!")
    except Exception as e:
        print(f"❌ Erro crítico ao carregar o modelo: {e}")

# ---------------------------------
# FUNÇÃO PARA FAZER A PREVISÃO DA IMAGEM
# ---------------------------------
def prever_imagem(image_bytes):
    """Analisa uma imagem (em formato de bytes) e retorna o diagnóstico."""
    try:
        img = Image.open(io.BytesIO(image_bytes))
        if img.mode == 'RGBA': img = img.convert('RGB')
        
        img = img.resize((180, 180))
        img_array = utils.img_to_array(img)
        img_array = tf.expand_dims(img_array, 0)

        previsao = modelo.predict(img_array, verbose=0)
        score = tf.nn.softmax(previsao[0])
        
        classe_prevista = class_names[np.argmax(score)]
        confianca = 100 * np.max(score)
        
        return {"classe": classe_prevista, "confianca": confianca}
    except Exception as e:
        print(f"Erro durante a previsão: {e}")
        return {"erro": "Não foi possível analisar a imagem."}

# ---------------------------------
# CRIAÇÃO E ROTAS DO SERVIDOR FLASK
# ---------------------------------
app = Flask(__name__)
CORS(app)  # Permite que o seu site (XAMPP) se comunique com este servidor

# Endpoint principal da API
@app.route('/analisar', methods=['POST'])
def analisar_planta_endpoint():
    if modelo is None:
        return jsonify({"erro": "Modelo de IA não está carregado no servidor."}), 500

    if 'imagem' not in request.files or 'id_usuario' not in request.form:
        return jsonify({"erro": "Requisição incompleta. 'imagem' e 'id_usuario' são obrigatórios."}), 400

    id_usuario = request.form['id_usuario']
    file = request.files['imagem']

    if file.filename == '':
        return jsonify({"erro": "Nenhum arquivo selecionado."}), 400

    if file:
        image_bytes = file.read()
        resultado_ia = prever_imagem(image_bytes)

        if 'erro' in resultado_ia:
            return jsonify(resultado_ia), 500

        # --- LÓGICA PARA SALVAR NO BANCO DE DADOS ---
        try:
            conn = mysql.connector.connect(**db_config)
            cursor = conn.cursor()

            # 1. Cria uma nova planta genérica na tabela 'planta_usuario'
            apelido_generico = f"Análise de {datetime.now().strftime('%d/%m/%Y %H:%M')}"
            sql_planta = "INSERT INTO planta_usuario (IdUsuario, IdPlanta, apelido) VALUES (%s, NULL, %s)"
            val_planta = (id_usuario, apelido_generico)
            cursor.execute(sql_planta, val_planta)
            id_nova_planta_usuario = cursor.lastrowid

            # 2. Salva a imagem na pasta 'uploads'
            if not os.path.exists(PASTA_UPLOADS):
                os.makedirs(PASTA_UPLOADS)
            nome_arquivo_imagem = f"analise_{id_nova_planta_usuario}_{datetime.now().strftime('%Y%m%d%H%M%S')}.jpg"
            caminho_salvar = os.path.join(PASTA_UPLOADS, nome_arquivo_imagem)
            with open(caminho_salvar, 'wb') as f:
                f.write(image_bytes)

            # 3. Salva a análise na tabela 'analise_planta'
            status_saude_db = "Doente"
            if "healthy" in resultado_ia['classe'].lower():
                status_saude_db = "Boa"
            
            sql_analise = "INSERT INTO analise_planta (ID_planta_usuario, status_saude, imagem) VALUES (%s, %s, %s)"
            val_analise = (id_nova_planta_usuario, status_saude_db, caminho_salvar)
            cursor.execute(sql_analise, val_analise)
            
            conn.commit()
            print(f"✅ Nova planta genérica (ID: {id_nova_planta_usuario}) e análise salvas para o usuário {id_usuario}.")
            cursor.close()
            conn.close()

        except mysql.connector.Error as err:
            print(f"❌ Erro de banco de dados: {err}")
        
        # Formata a resposta final para o frontend
        status_formatado = f"🌿 SAUDÁVEL ({resultado_ia['classe']})" if status_saude_db == "Boa" else f"⚠️ DOENTE - {resultado_ia['classe']}"
        resposta_frontend = {
            "status": status_formatado,
            "confianca": f"{resultado_ia['confianca']:.2f}%"
        }
        return jsonify(resposta_frontend)

    return jsonify({"erro": "Erro desconhecido no servidor."}), 500

# ---------------------------------
# INICIALIZAÇÃO DO SERVIDOR
# ---------------------------------
if __name__ == '__main__':
    carregar_ia()  # Carrega o modelo uma vez, quando o servidor inicia
    print("🚀 Servidor de IA pronto para receber imagens em http://localhost:5000/analisar" )
    app.run(host='0.0.0.0', port=5000, debug=False)
