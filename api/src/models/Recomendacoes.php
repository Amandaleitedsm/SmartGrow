<?php
    class Recomendacoes implements JsonSerializable {
        
        public function __construct(
            private ?int $ID = null,
            private string $titulo = '',
            private string $descricao = ''
        ) {}
        
        public function JsonSerialize(): array {
            return [
                'ID' => $this->ID,
                'titulo' => $this->titulo,
                'descricao' => $this->descricao
            ];
        }

        public function getID(): ?int {
            return $this->ID;
        }

        public function setID(int $id): self {
            $this->ID = $id;
            return $this;
        }

        public function getTitulo(): string {
            return $this->titulo;
        }

        public function setTitulo(string $titulo): self {
            $this->titulo = $titulo;
            return $this;
        }

        public function getDescricao(): string {
            return $this->descricao;
        }

        public function setDescricao(string $descricao): self {
            $this->descricao = $descricao;
            return $this;
        }

    }