<?php 
    namespace DevMacb\Http;

    
    class Request {
        private $uri;
        private $metodo_http;
        private $headers = []; 
        private $dados_get = [];
        private $dados_post = [];
        

        public function __construct() {
            $this->dados_get   = $_GET ?? [];
            $this->dados_post  = $_POST ?? []; 
            $this->headers     = getallheaders(); 
            $this->uri         = $_SERVER['REQUEST_URI'] ?? '';
            $this->metodo_http = $_SERVER['REQUEST_METHOD'] ?? '';
        }


        public function obterDadosGet() {
            return $this->dados_get;
        }


        public function obterDadosPost() {
            return $this->dados_post;
        }


        public function obterHeaders() {
            return $this->headers;
        }


        public function obterUri() {
            return $this->uri;
        }

        
        public function obterMetodoHttp() {
            return $this->metodo_http;
        }
    }
?>