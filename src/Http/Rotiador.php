<?php 
    namespace Suprema\Http;

    
    use Closure;
    use Exception; 
    use ReflectionFunction;
    use Suprema\Http\Queue;    


    class Rotiador {
        private $requisicao;
        private $url     = '';
        private $rotas   = [];
        private $prefixo = '';


        public function __construct($url) {
            $this->requisicao = new Request();
            $this->url        = $url; 
            $this->definirPrefixo(); 
        }


        private function definirPrefixo() {
            $analise_url = parse_url($this->url);
            $this->prefixo = $analise_url['path'] ?? '';
        }


        private function obterRotaBase() {
            $rota = $this->requisicao->obterUri();
            $nova_base = strlen($this->prefixo) ? explode($this->prefixo, $rota) : [$rota];
            return end($nova_base);
        }
 

        private function obterParametrosRota() {
            $rota = $this->obterRotaBase();
            $metodoHttp = $this->requisicao->obterMetodoHttp();

            foreach($this->rotas as $padrao_rota => $metodos) {
                if (preg_match($padrao_rota, $rota, $acertos)) {
                    if (isset($metodos[$metodoHttp])) {
                        unset($acertos[0]);
                        $chaves = $metodos[$metodoHttp]['variaveis'];
                        $metodos[$metodoHttp]['variaveis'] = array_combine($chaves, $acertos);
                        $metodos[$metodoHttp]['variaveis']['requisicao'] = $this->requisicao;
                        return $metodos[$metodoHttp];
                    } 
                    throw new Exception('Método não permitido', 405);
                }
            }
            throw new Exception('Url não encontrada', 404);
        }


        private function adicionarRota($metodo, $rota, $parametros = []) {
            foreach ($parametros as $chave => $valor) {
                if ($valor instanceof Closure) {
                    $parametros['controller'] = $valor;
                    unset($parametros[$chave]);
                    continue;
                }
            }

            $parametros['middlewares'] = $parametros['middlewares'] ?? [];

            $parametros['variaveis'] = [];
            $padrao_variavel = '/{(.*?)}/';
            if (preg_match_all($padrao_variavel, $rota, $acertos)) {
                $rota = preg_replace($padrao_variavel, '(.*?)', $rota);
                $parametros['variaveis'] = $acertos[1];
            }
            $padrao_rota = '/^'.str_replace('/', '\/', $rota).'$/';
            $this->rotas[$padrao_rota][$metodo] = $parametros;
        }


        public function get($rota, $parametros = []) {
            return $this->adicionarRota('GET', $rota, $parametros);
        }

        public function post($rota, $parametros = []) {
            return $this->adicionarRota('POST', $rota, $parametros);
        }

        public function rodar() {
            try {
                $rota = $this->obterParametrosRota();
                if(!isset($rota['controller'])) {
                    throw new Exception('A url não pode ser processada', 500);
                }

                $argumentos = [];
                $reflexao = new ReflectionFunction($rota['controller']);
                foreach($reflexao->getParameters() as $parametro) {
                    $nome = $parametro->getName();
                    $argumentos[$nome] = $rota['variaveis'][$nome] ?? '';
                }

                return (new Queue($rota['middlewares'], $rota['controller'], $argumentos))->proximo($this->requisicao);
            }
            catch(Exception $erro) {
                return new Response($erro->getCode(), $erro->getMessage());
            }
        }
    }
?>
