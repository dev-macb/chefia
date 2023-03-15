<?php
    namespace DevMacb\BancoDados;


    class Pagination{
        private $limite;
        private $resultados;
        private $paginas;
        private $pagina_atual;


        public function __construct($resultados, $pagina_atual = 1, $limite = 10){
            $this->limite       = $limite;
            $this->resultados   = $resultados;
            $this->pagina_atual = (is_numeric($pagina_atual) and $pagina_atual > 0) ? $pagina_atual : 1;
            $this->calculate();
        }



        private function calculate(){
            //CALCULA O TOTAL DE PÁGINAS
            $this->paginas = $this->resultados > 0 ? ceil($this->resultados / $this->limite) : 1;

            //VERIFICA SE A PÁGINA ATUAL NÃO EXCEDE O NÚMERO DE PÁGINAS
            $this->pagina_atual = $this->pagina_atual <= $this->paginas ? $this->pagina_atual : $this->paginas;
        }

        public function getlimite(){
            $offset = ($this->limite * ($this->pagina_atual - 1));
            return $offset.','.$this->limite;
        }

        public function getpaginas(){
            //NÃO RETORNA PÁGINAS
            if($this->paginas == 1) return [];

            //PÁGINAS
            $paginas = [];
            for($i = 1; $i <= $this->paginas; $i++) {
                $paginas[] = [
                    'page'    => $i,
                    'current' => $i == $this->pagina_atual
                ];
            }
            return $paginas;
        }

    }
?>