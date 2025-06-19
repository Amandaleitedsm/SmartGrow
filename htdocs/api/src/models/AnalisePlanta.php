<?php
    class AnalisePlanta implements JsonSerializable {

        public function __construct(
            private ?int $ID = null,
            private ?int $ID_planta_usuario = null,
            private ?string $data_analise = null,  // DateTime ou string
            private ?string $status_saude = null,  // 'Boa', 'Regular', 'Ruim', 'Doente'
            private ?string $status_umidade = null // 'Baixo', 'Alto', 'Regular'
        ) {}

        public function JsonSerialize(): array {
            return [
                'ID' => $this->ID,
                'ID_planta_usuario' => $this->ID_planta_usuario,
                'data_analise' => $this->data_analise,
                'status_saude' => $this->status_saude,
                'status_umidade' => $this->status_umidade
            ];
        }
        public function getID(): ?int {
            return $this->ID;
        }

        public function getIDPlantaUsuario(): ?int {
            return $this->ID_planta_usuario;
        }

        public function setIDPlantaUsuario(?int $ID_planta_usuario): self {
            $this->ID_planta_usuario = $ID_planta_usuario;
            return $this;
        }

        public function getDataAnalise(): ?string {
            return $this->data_analise;
        }

        public function setDataAnalise(?string $data_analise): self {
            $this->data_analise = $data_analise;
            return $this;
        }

        public function getStatusSaude(): ?string {
            return $this->status_saude;
        }

        public function setStatusSaude(?string $status_saude): self {
            $this->status_saude = $status_saude;
            return $this;
        }

        public function getStatusUmidade(): ?string {
            return $this->status_umidade;
        }

        public function setStatusUmidade(?string $status_umidade): self {
            $this->status_umidade = $status_umidade;
            return $this;
        }

    }
