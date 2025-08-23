<?php
    class CadastroUsuario implements JsonSerializable {
        
        public function __construct(
            private ?int $ID_usuario = null,
            private string $nome = '',
            private string $email = '',
            private string $senhaHash = '',
            private string $senha = '',
            private string $roleUsuario = 'usuario',
            private DateTime $dataCadastro = new DateTime(),
            private DateTime $dataAtualizacao = new DateTime(),
            private bool $ativo = true
        ) {}

        

        public function JsonSerialize(): array {
            return [
                'ID_usuario' => $this->ID_usuario,
                'nome' => $this->nome,
                'email' => $this->email,
                'senhaHash' => $this->senhaHash,
                'senha' => $this->senha,
                'roleUsuario' => $this->roleUsuario,
                'dataCadastro' => $this->dataCadastro,
                'dataAtualizacao' => $this->dataAtualizacao,
                'ativo' => $this->ativo
            ];
        }

        public function getId(): ?int {
            return $this->ID_usuario;
        }

        public function setId(?int $ID_usuario): self {
            $this->ID_usuario = $ID_usuario;
            return $this;
        }

        public function getNome(): string {
            return $this->nome;
        }

        public function setNome(string $nome): self { #ERRO TESTAR ROTA
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

        public function getSenha(): string {
            return $this->senha;
        }

        public function setSenha(string $senha): self {
            $this->senha = $senha;
            return $this;
        }

        public function getRoleUsuario(): string {
            return $this->roleUsuario;
        }

        public function setRoleUsuario(string $roleUsuario): self {
            $this->roleUsuario = $roleUsuario;
            return $this;
        }

        public function getDataCadastro(): DateTime {
            return $this->dataCadastro;
        }

        public function setDataCadastro(DateTime $dataCadastro): self {
            $this->dataCadastro = $dataCadastro;
            return $this;
        }

        public function getDataAtualizacao(): DateTime {
            return $this->dataAtualizacao;
        }

        public function setDataAtualizacao(DateTime $dataAtualizacao): self {
            $this->dataAtualizacao = $dataAtualizacao;
            return $this;
        }

        public function getAtivo(): bool {
            return $this->ativo;
        }

        public function setAtivo(bool $ativo): self {
            $this->ativo = $ativo;
            return $this;
        }

        public function getSenhaHash(): string {
            return $this->senhaHash; // Retorna a senha hash
        }
        public function setSenhaHash(string $senhaHash): self {
            $this->senhaHash = $senhaHash;
            return $this;
        }

    }