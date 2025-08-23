<?php
    class AnaliseRecomendacao implements JsonSerializable {
        
        public function __construct(
            private ?int $ID_analise = null,
            private ?int $ID_recomendacao = null
        ) {}

        public function JsonSerialize(): array {
            return [
                'ID_analise' => $this->ID_analise,
                'ID_recomendacao' => $this->ID_recomendacao
            ];
        }
        
        public function getIDAnalise(): ?int
        {
            return $this->ID_analise;
        }

        public function setIDAnalise(int $ID_analise): self
        {
            $this->ID_analise = $ID_analise;
            return $this;
        }

        public function getIDRecomendacao(): ?int
        {
            return $this->ID_recomendacao;
        }

        public function setIDRecomendacao(int $ID_recomendacao): self
        {
            $this->ID_recomendacao = $ID_recomendacao;
            return $this;
        }

    }