from flask import Flask, request, jsonify
from services.report_generator import gerar_relatorio

app = Flask(__name__)

@app.route("/relatorio", methods=["POST"])
def relatorio():
    dados = request.json
    planta_id = dados.get("planta_usuario_id")
    inicio = dados.get("inicio")
    fim = dados.get("fim")

    # Chama o gerador
    caminho_arquivo = gerar_relatorio(planta_id, inicio, fim)

    return jsonify({"status": "ok", "arquivo": caminho_arquivo})

if __name__ == "__main__":
    app.run(debug=True, port=5001)