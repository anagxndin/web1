#!/usr/bin/env python3
"""
Servidor de desenvolvimento com mock API.
Serve arquivos estáticos + endpoints da API simulados a partir de data.json.

Uso:
    python3 server_mock.py
    # Acesse http://localhost:8000
"""

import json
import os
import re
import urllib.parse
from http.server import HTTPServer, SimpleHTTPRequestHandler
from pathlib import Path

RAIZ = Path(__file__).resolve().parent
CAMINHO_DATA = RAIZ / "src" / "data" / "veiculos.json"


def carregar_dados():
    with open(CAMINHO_DATA, encoding="utf-8") as f:
        raw = json.load(f)

    anuncios = []
    for a in raw.get("anuncios", []):
        foto = (a.get("fotos") or [None])[0]
        anuncios.append({
            "id": a["id"],
            "marca": a["marca"],
            "modelo": a["modelo"],
            "ano_fabricacao": a["ano"],
            "cidade": a["cidade"],
            "estado": a["estado"],
            "valor": a["valor"],
            "foto": "assets/images/" + foto if foto else None,
        })

    usuarios = raw.get("usuarios", [])
    return anuncios, usuarios


def filtrar(anuncios, params):
    marca = params.get("marca", [None])[0]
    modelo = params.get("modelo", [None])[0]
    cidade = params.get("cidade", [None])[0]
    resultado = []
    for a in anuncios:
        if marca and marca.lower() not in a["marca"].lower():
            continue
        if modelo and modelo.lower() not in a["modelo"].lower():
            continue
        if cidade and cidade.lower() not in a["cidade"].lower():
            continue
        resultado.append(a)
    return resultado


class MockHandler(SimpleHTTPRequestHandler):
    def do_GET(self):
        parsed = urllib.parse.urlparse(self.path)
        params = urllib.parse.parse_qs(parsed.query)
        path = parsed.path

        # Mock endpoints da API
        if re.match(r"/?backend/api/anuncios_listar\.php", path):
            anuncios, _ = carregar_dados()
            filtrados = filtrar(anuncios, params)
            body = json.dumps({
                "sucesso": True,
                "mensagem": "ok",
                "anuncios": filtrados,
            }, ensure_ascii=False).encode("utf-8")
            self.send_response(200)
            self.send_header("Content-Type", "application/json; charset=utf-8")
            self.send_header("Access-Control-Allow-Origin", "*")
            self.end_headers()
            self.wfile.write(body)
            return

        # Serve arquivos estáticos normalmente
        return super().do_GET()

    def do_POST(self):
        # Endpoints POST simulados
        if re.match(r"/?backend/api/logout\.php", self.path):
            self.send_response(200)
            self.send_header("Content-Type", "application/json; charset=utf-8")
            self.end_headers()
            self.wfile.write(json.dumps({
                "sucesso": True, "mensagem": "Sessão encerrada.",
                "redirect": "/src/pages/public/login.html"
            }, ensure_ascii=False).encode("utf-8"))
            return

        content_length = int(self.headers.get("Content-Length", 0))
        raw = self.rfile.read(content_length) if content_length else b""

        if re.match(r"/?backend/api/cadastro\.php", self.path):
            body = json.dumps({
                "sucesso": True,
                "mensagem": "Cadastro realizado com sucesso! Faça login para continuar.",
                "redirect": "/src/pages/public/login.html"
            }, ensure_ascii=False).encode("utf-8")
            self.send_response(200)
            self.send_header("Content-Type", "application/json; charset=utf-8")
            self.end_headers()
            self.wfile.write(body)
            return

        if re.match(r"/?backend/api/login\.php", self.path):
            parsed = urllib.parse.parse_qs(raw.decode("utf-8"))
            email = parsed.get("email", [""])[0]
            senha = parsed.get("senha", [""])[0]
            _, usuarios = carregar_dados()
            user = next((u for u in usuarios if u["email"] == email and u["senha"] == senha), None)
            if user:
                body = json.dumps({
                    "sucesso": True,
                    "mensagem": "Login realizado com sucesso!",
                    "redirect": "/src/pages/area-restrita/principalRestrita.html",
                    "usuario": {"id": user["id"], "nome": user["nome"], "email": user["email"]}
                }, ensure_ascii=False).encode("utf-8")
                self.send_response(200)
            else:
                body = json.dumps({
                    "sucesso": False,
                    "mensagem": "E-mail ou senha inválidos."
                }, ensure_ascii=False).encode("utf-8")
                self.send_response(401)
            self.send_header("Content-Type", "application/json; charset=utf-8")
            self.send_header("Access-Control-Allow-Origin", "*")
            self.end_headers()
            self.wfile.write(body)
            return

        if re.match(r"/?backend/api/interesse_criar\.php", self.path):
            body = json.dumps({
                "sucesso": True,
                "mensagem": "Interesse registrado com sucesso! O anunciante entrará em contato em breve."
            }, ensure_ascii=False).encode("utf-8")
            self.send_response(200)
            self.send_header("Content-Type", "application/json; charset=utf-8")
            self.send_header("Access-Control-Allow-Origin", "*")
            self.end_headers()
            self.wfile.write(body)
            return

        if re.match(r"/?backend/api/anuncio_criar\.php", self.path):
            body = json.dumps({
                "sucesso": True,
                "mensagem": "Anúncio criado com sucesso!",
                "redirect": "/src/pages/area-restrita/meus-anuncios.html"
            }, ensure_ascii=False).encode("utf-8")
            self.send_response(200)
            self.send_header("Content-Type", "application/json; charset=utf-8")
            self.send_header("Access-Control-Allow-Origin", "*")
            self.end_headers()
            self.wfile.write(body)
            return

        # Fallback: 404
        self.send_response(404)
        self.send_header("Content-Type", "application/json")
        self.end_headers()
        self.wfile.write(b'{"sucesso":false,"mensagem":"Endpoint nao encontrado"}')

    def log_message(self, fmt, *args):
        print(f"[mock] {args[0]} {args[1]}")


if __name__ == "__main__":
    os.chdir(RAIZ)
    porta = int(os.environ.get("PORT", 8000))
    servidor = HTTPServer(("0.0.0.0", porta), MockHandler)
    print(f"🚗 veloCity mock server rodando em http://localhost:{porta}")
    print(f"   Home: http://localhost:{porta}/src/pages/public/index.html")
    try:
        servidor.serve_forever()
    except KeyboardInterrupt:
        print("\nServidor encerrado.")
        servidor.server_close()
