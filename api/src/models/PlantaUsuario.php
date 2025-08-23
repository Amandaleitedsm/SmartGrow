<?php
    class PlantaUsuario implements JsonSerializable {
        
        public function __construct(
            private ?int $ID = null,
            private ?int $IdUsuario = null,
            private ?int $IdPlanta = null,
            private string $apelido = '',
            private string $localizacao = ''
        ) {}

        public function JsonSerialize(): array {
            return [
                'ID' => $this->ID,
                'IdUsuario' => $this->IdUsuario,
                'IdPlanta' => $this->IdPlanta,
                'apelido' => $this->apelido,
                'localizacao' => $this->localizacao
            ];
        }

        public function getID(): ?int {
            return $this->ID;
        }

        public function setID(?int $ID): self {
            $this->ID = $ID;
            return $this;
        }

        public function getIdUsuario(): ?int {
            return $this->IdUsuario;
        }

        public function setIdUsuario(?int $IdUsuario): self {
            $this->IdUsuario = $IdUsuario;
            return $this;
        }

        public function getIdPlanta(): ?int {
            return $this->IdPlanta;
        }

        public function setIdPlanta(?int $IdPlanta): self {
            $this->IdPlanta = $IdPlanta;
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
    }