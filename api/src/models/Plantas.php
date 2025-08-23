<?php
    class Plantas implements JsonSerializable {

        public function __construct(
            private ?int $IdPlanta = null,
            private string $nomeComum = '',
            private ?string $nomeCientifico = null,
            private ?string $tipo = null,
            private ?string $clima = null,
            private ?string $regiaoOrigem = null,
            private ?string $luminosidade = null,
            private ?string $frequenciaRega = null,
            private ?float $umidadeMin = null,
            private ?float $umidadeMax = null,
            private ?string $descricao = null
        ) {}

        

        public function JsonSerialize(): array {
            return [
                'IdPlanta' => $this->IdPlanta,
                'nomeComum' => $this->nomeComum,
                'nome_cientifico' => $this->nomeCientifico,
                'tipo' => $this->tipo,
                'clima' => $this->clima,
                'regiao_origem' => $this->regiaoOrigem,
                'luminosidade' => $this->luminosidade,
                'frequencia_rega' => $this->frequenciaRega,
                'umidade_min' => $this->umidadeMin,
                'umidade_max' => $this->umidadeMax,
                'descricao' => $this->descricao
            ];
        }
        
        public function getID(): ?int {
            return $this->IdPlanta;
        }

        public function setID(?int $IdPlanta): self {
            $this->IdPlanta = $IdPlanta;
            return $this;
        }

        public function getNomeComum(): string {
            return $this->nomeComum;
        }
        public function setNomeComum(string $nomeComum): self {
            $this->nomeComum = $nomeComum;
            return $this;
        }

        public function getNomeCientifico(): ?string {
            return $this->nomeCientifico;
        }
        public function setNomeCientifico(?string $nomeCientifico): self {
            $this->nomeCientifico = $nomeCientifico;
            return $this;
        }

        public function getTipo(): ?string {
            return $this->tipo;
        }
        public function setTipo(?string $tipo): self {
            $this->tipo = $tipo;
            return $this;
        }

        public function getClima(): ?string {
            return $this->clima;
        }
        public function setClima(?string $clima): self {
            $this->clima = $clima;
            return $this;
        }

        public function getRegiaoOrigem(): ?string {
            return $this->regiaoOrigem;
        }
        public function setRegiaoOrigem(?string $regiaoOrigem): self {
            $this->regiaoOrigem = $regiaoOrigem;
            return $this;
        }

        public function getLuminosidade(): ?string {
            return $this->luminosidade;
        }
        public function setLuminosidade(?string $luminosidade): self {
            $this->luminosidade = $luminosidade;
            return $this;
        }

        public function getFrequenciaRega(): ?string {
            return $this->frequenciaRega;
        }
        public function setFrequenciaRega(?string $frequenciaRega): self {
            $this->frequenciaRega = $frequenciaRega;
            return $this;
        }

        public function getUmidadeMin(): ?float {
            return $this->umidadeMin;
        }
        public function setUmidadeMin(?float $umidadeMin): self {
            $this->umidadeMin = $umidadeMin;
            return $this;
        }

        public function getUmidadeMax(): ?float {
            return $this->umidadeMax;
        }
        public function setUmidadeMax(?float $umidadeMax): self {
            $this->umidadeMax = $umidadeMax;
            return $this;
        }

        public function getDescricao(): ?string {
            return $this->descricao;
        }
        public function setDescricao(?string $descricao): self {
            $this->descricao = $descricao;
            return $this;
        }
    }