<?php
    class PlantaUsuario implements JsonSerializable {
        
        public function __construct(
            private ?int $ID = null,
            private ?int $ID_usuarioplanta = null,
            private string $apelido = '',
            private string $localizacao = ''
        ) {}

        public function getID(): ?int {
            return $this->ID;
        }

        public function getIDUsuarioplanta(): ?int {
            return $this->ID_usuarioplanta;
        }

        public function setIDUsuarioplanta(?int $ID_usuarioplanta): self {
            $this->ID_usuarioplanta = $ID_usuarioplanta;
            return $this;
        }

        public function getApelido(): string {
            return $this->apelido;
        }

        public function setApelido(string $apelido): self {
            $this->apelido = $apelido;
            return $this;
        }

        public function getLocalizacao(): string {
            return $this->localizacao;
        }

        public function setLocalizacao(string $localizacao): self {
            $this->localizacao = $localizacao;
            return $this;
        }
        
        public function JsonSerialize(): array {
            return [
                'ID' => $this->ID,
                'ID_usuarioplanta' => $this->ID_usuarioplanta,
                'apelido' => $this->apelido,
                'localizacao' => $this->localizacao
            ];
        }
    }