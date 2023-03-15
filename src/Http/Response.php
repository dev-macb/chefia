<?php 
    namespace DevMacb\Http;

    
    class Response {
        private $headers = [];
        private $conteudo = [];
        private $codigo_http = 200;
        private $tipo_conteudo = 'text/html';
        

        public function __construct($codigo_http, $conteudo, $tipo_conteudo = 'text/html') {
            $this->conteudo    = $conteudo;
            $this->codigo_http = $codigo_http; 
            $this->definirTipoConteudo($tipo_conteudo);
        }


        public function definirTipoConteudo($tipo_conteudo) {
            $this->tipo_conteudo = $tipo_conteudo;
            $this->adicionarHeader('Content-Type', $tipo_conteudo);
        }


        public function adicionarHeader($chave, $valor) {
            $this->headers[$chave] = $valor;
        }


        public function enviarHeaders() {
            http_response_code($this->codigo_http);
            foreach ($this->headers as $chave => $valor) {
                header($chave.': '.$valor);
            }
        }

        
        public function enviarResponse() {
            $this->enviarHeaders();
            switch ($this->tipo_conteudo) {
                case 'text/html':
                    echo $this->conteudo;
                    exit;
            }
        }
    }
?>