<?php
    class CondicoesPlanta implements JsonSerializable {

        public function __construct(
            private ?int $ID = null,
            private ?int $ID_planta = null,
            private DateTime $data_registro = new DateTime(),  // pode usar DateTime ou string
            private ?float $umidade_atual = null
        ) {}

        public function JsonSerialize(): array {
            return [
                'ID' => $this->ID,
                'ID_planta' => $this->ID_planta,
                'data_registro' => $this->data_registro,
                'umidade_atual' => $this->umidade_atual
            ];
        }
        
        public function getID(): ?int {
            return $this->ID;
        }

        public function setID(?int $id): self {
            $this->ID = $id;
            return $this;
        }

        public function getIDPlanta(): ?int {
            return $this->ID_planta;
        }

        public function setIDPlanta(?int $ID_planta): self {
            $this->ID_planta = $ID_planta;
            return $this;
        }

        public function getDataRegistro(): DateTime {
            return $this->data_registro;
        }

        public function setDataRegistro(DateTime $data_registro): self {
            $this->data_registro = $data_registro;
            return $this;
        }

        public function getUmidadeAtual(): ?float {
            return $this->umidade_atual;
        }

        public function setUmidadeAtual(?float $umidade_atual): self {
            $this->umidade_atual = $umidade_atual;
            return $this;
        }

    }