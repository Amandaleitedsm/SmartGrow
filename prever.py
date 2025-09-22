import os
import tensorflow as tf
import numpy as np
from tensorflow.keras import models, utils # Importação corrigida
from PIL import Image
import io

# --- FUNÇÕES DE PREVISÃO (O "CÉREBRO" DA IA) ---

def carregar_modelo_e_classes(caminho_modelo, caminho_classes):
    """
    Carrega um modelo e seu arquivo de classes correspondente.
    Retorna: O modelo carregado e a lista de nomes de classes.
    """
    try:
        print(f"Carregando modelo de '{caminho_modelo}'...")
        modelo = models.load_model(caminho_modelo)
        
        print(f"Carregando classes de '{caminho_classes}'...")
        with open(caminho_classes, "r") as f:
            class_names = [line.strip() for line in f.readlines()]
            
        print("✅ Modelo e classes carregados com sucesso!")
        return modelo, class_names
    except Exception as e:
        print(f"❌ Erro ao carregar modelo ou classes: {e}")
        return None, None


def prever_imagem(image_bytes, modelo, class_names):
    """
    Analisa uma imagem (em formato de bytes) e retorna o diagnóstico.
    
    Args:
        image_bytes: A imagem enviada pelo frontend.
        modelo: O modelo de Keras carregado.
        class_names: A lista de nomes de classes.

    Returns:
        Um dicionário com o status e a confiança da previsão.
    """
    try:
        # Abre a imagem a partir dos bytes recebidos na requisição web
        img = Image.open(io.BytesIO(image_bytes))
        
        # Converte para RGB se a imagem tiver um canal alfa (transparência)
        if img.mode == 'RGBA':
            img = img.convert('RGB')
            
        # Redimensiona a imagem para o tamanho que o modelo foi treinado (180x180)
        img = img.resize((180, 180))
        
        # Converte a imagem para um array numpy e a prepara para o modelo
        img_array = utils.img_to_array(img)
        img_array = tf.expand_dims(img_array, 0)  # Cria um "batch" de 1 imagem

        # Realiza a previsão
        previsao = modelo.predict(img_array, verbose=0)
        score = tf.nn.softmax(previsao[0])
        
        # Pega o resultado com a maior probabilidade
        classe_prevista = class_names[np.argmax(score)]
        confianca = 100 * np.max(score)

        # Formata a resposta
        if "healthy" in classe_prevista.lower():
            status = f"🌿 SAUDÁVEL ({classe_prevista})"
        else:
            status = f"⚠️ DOENTE - {classe_prevista}"

        # Retorna um dicionário estruturado, ideal para uma API
        return {
            "status": status,
            "confianca": f"{confianca:.2f}%"
        }
        
    except Exception as e:
        print(f"Erro durante a previsão da imagem: {e}")
        return {"erro": "Não foi possível analisar a imagem."}

# --------------------------------------------------------------------
# A PARTE ABAIXO FOI REMOVIDA, POIS NÃO É MAIS NECESSÁRIA.
# O SERVIDOR 'app.py' AGORA CONTROLA QUANDO E COMO ESSAS FUNÇÕES SÃO CHAMADAS.
#
# if __name__ == "__main__":
#     ... (código de menu interativo removido) ...
# --------------------------------------------------------------------
