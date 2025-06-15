<?php
    class CadastroUsuario implements JsonSerializable {

        public function __construct(
            private ?int $ID_planta = null,
            private string $nome_comum = '',
            private ?string $nome_cientifico = null,
            private ?string $tipo = null,
            private ?string $clima = null,
            private ?string $regiao_origem = null,
            private ?string $luminosidade = null,
            private ?string $frequencia_rega = null,
            private ?float $umidade_min = null,
            private ?float $umidade_max = null,
            private ?string $descricao = null
        ) {}

        

        public function JsonSerialize(): array {
            return [
                'ID_planta' => $this->ID_planta,
                'nome_comum' => $this->nome_comum,
                'nome_cientifico' => $this->nome_cientifico,
                'tipo' => $this->tipo,
                'clima' => $this->clima,
                'regiao_origem' => $this->regiao_origem,
                'luminosidade' => $this->luminosidade,
                'frequencia_rega' => $this->frequencia_rega,
                'umidade_min' => $this->umidade_min,
                'umidade_max' => $this->umidade_max,
                'descricao' => $this->descricao
            ];
        }
        
        public function getID_planta(): ?int {
            return $this->ID_planta;
        }

        public function getNome_comum(): string {
            return $this->nome_comum;
        }
        public function setNome_comum(string $nome_comum): self {
            $this->nome_comum = $nome_comum;
            return $this;
        }

        public function getNome_cientifico(): ?string {
            return $this->nome_cientifico;
        }
        public function setNome_cientifico(?string $nome_cientifico): self {
            $this->nome_cientifico = $nome_cientifico;
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

        public function getRegiao_origem(): ?string {
            return $this->regiao_origem;
        }
        public function setRegiao_origem(?string $regiao_origem): self {
            $this->regiao_origem = $regiao_origem;
            return $this;
        }

        public function getLuminosidade(): ?string {
            return $this->luminosidade;
        }
        public function setLuminosidade(?string $luminosidade): self {
            $this->luminosidade = $luminosidade;
            return $this;
        }

        public function getFrequencia_rega(): ?string {
            return $this->frequencia_rega;
        }
        public function setFrequencia_rega(?string $frequencia_rega): self {
            $this->frequencia_rega = $frequencia_rega;
            return $this;
        }

        public function getUmidade_min(): ?float {
            return $this->umidade_min;
        }
        public function setUmidade_min(?float $umidade_min): self {
            $this->umidade_min = $umidade_min;
            return $this;
        }

        public function getUmidade_max(): ?float {
            return $this->umidade_max;
        }
        public function setUmidade_max(?float $umidade_max): self {
            $this->umidade_max = $umidade_max;
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