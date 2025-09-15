import os
from datetime import datetime
from reportlab.pdfgen import canvas

def gerar_relatorio(planta_id, inicio, fim):
    # Garante pasta de saída
    pasta_saida = os.path.join(os.getcwd(), "output")
    os.makedirs(pasta_saida, exist_ok=True)

    nome_arquivo = f"relatorio_{planta_id}_{datetime.now().strftime('%Y%m%d%H%M%S')}.pdf"
    caminho = os.path.join(pasta_saida, nome_arquivo)

    # Gera PDF simples só pra validar
    c = canvas.Canvas(caminho)
    c.drawString(100, 750, f"Relatório da planta_usuario {planta_id}")
    c.drawString(100, 730, f"Período: {inicio} até {fim}")
    c.save()

    return caminho