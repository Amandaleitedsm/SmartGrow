<?php
    class UsuarioPlanta implements JsonSerializable {
        
        public function __construct(
            private ?int $ID = null,
            private ?int $ID_usuario = null,
            private ?int $ID_planta = null
        ) {}

        
        public function JsonSerialize(): array {
            return [
                'ID' => $this->ID,
                'ID_usuario' => $this->ID_usuario,
                'ID_planta' => $this->ID_planta
            ];
        }
        
        public function getID(): ?int {
            return $this->ID;
        }

        public function getID_usuario(): ?int {
            return $this->ID_usuario;
        }

        public function setID_usuario(?int $ID_usuario): self {
            $this->ID_usuario = $ID_usuario;
            return $this;
        }

        public function getID_planta(): ?int {
            return $this->ID_planta;
        }

        public function setID_planta(?int $ID_planta): self {
            $this->ID_planta = $ID_planta;
            return $this;
        }
    }