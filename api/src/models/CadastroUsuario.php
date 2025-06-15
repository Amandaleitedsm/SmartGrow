<?php
    class CadastroUsuario implements JsonSerializable {
        
        public function __construct(
            private ?int $ID_usuario = null,
            private string $nome = '',
            private string $email = '',
            private string $senhaHash = '',
            private string $dataCadastro = '',
            private string $dataAtualizacao = '',
            private bool $ativo = true
        ) {}

        

        public function JsonSerialize(): array {
            return [
                'ID_usuario' => $this->ID_usuario,
                'nome' => $this->nome,
                'email' => $this->email,
                'senhaHash' => $this->senhaHash,
                'dataCadastro' => $this->dataCadastro,
                'dataAtualizacao' => $this->dataAtualizacao,
                'ativo' => $this->ativo
            ];
        }

        public function getID_usuario(): ?int {
            return $this->ID_usuario;
        }

        public function getNome(): string {
            return $this->nome;
        }

        public function setNome(string $nome): self {
            $this->nome = $nome;
            return $this;
        }

        public function getEmail(): string {
            return $this->email;
        }

        public function setEmail(string $email): self {
            $this->email = $email;
            return $this;
        }

        public function getSenhaHash(): string {
            return $this->senhaHash;
        }

        public function setSenhaHash(string $senhaHash): self {
            $this->senhaHash = $senhaHash;
            return $this;
        }

        public function getDataCadastro(): string {
            return $this->dataCadastro;
        }

        public function setDataCadastro(string $dataCadastro): self {
            $this->dataCadastro = $dataCadastro;
            return $this;
        }

        public function getDataAtualizacao(): string {
            return $this->dataAtualizacao;
        }

        public function setDataAtualizacao(string $dataAtualizacao): self {
            $this->dataAtualizacao = $dataAtualizacao;
            return $this;
        }

        public function isAtivo(): bool {
            return $this->ativo;
        }

        public function setAtivo(bool $ativo): self {
            $this->ativo = $ativo;
            return $this;
        }

    }