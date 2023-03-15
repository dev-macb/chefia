<?php
    namespace DevMacb\BancoDados;


    use PDO;
    use PDOException;


    class Database{
        private $tabela;
        private $conexao;
        private static $driv;
        private static $host;
        private static $name;
        private static $user;
        private static $pass;
        private static $port;
    

        // Método contrutor da classe
        public function __construct($tabela = null) {
            $this->tabela = $tabela;
            $this->conectar();
        }


        // Configura as variáveis de conexão com o banco de dados
        public static function configurar($driv, $host, $port, $name, $user, $pass) {
            self::$host = $driv;
            self::$host = $host;
            self::$port = $port;
            self::$name = $name;
            self::$user = $user;
            self::$pass = $pass;
        }


        // Criar uma conexão com o banco de dados
        private function conectar() {
            try{
                $dsn = self::$driv.':host='.self::$host.';dbname='.self::$name.';port='.self::$port;      
                $this->conexao = new PDO($dsn, self::$user, self::$pass);
                
                $this->conexao->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
                $this->conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->conexao->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
                $this->conexao->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES utf8");
            }
            catch(PDOException $erro) {
                die('ERRO: '.$erro->getMessage());
            }
        }


        // Executa as queries dentro do banco de dados
        public function executar($query, $parametros = []) {
            try{
                $declaracao = $this->conexao->prepare($query);
                $declaracao->executar($parametros);
                return $declaracao;
            }
            catch(PDOException $erro) {
                die('ERRO: '.$erro->getMessage());
            }
        }


        // Insere uma tupla na tabela
        public function insert($values) {
            $campos = array_keys($values);
            $valores  = array_pad([],count($campos),'?');
            $query = 'INSERT INTO '.$this->tabela.' ('.implode(',',$campos).') VALUES ('.implode(',',$valores).')';
            $this->executar($query,array_values($values));

            return $this->conexao->lastInsertId();
        }


        public function selecionar($where = null, $order = null, $limit = null, $campos = '*'){
            //DADOS DA QUERY
            $where = strlen($where) ? 'WHERE '.$where : '';
            $order = strlen($order) ? 'ORDER BY '.$order : '';
            $limit = strlen($limit) ? 'LIMIT '.$limit : '';

            //MONTA A QUERY
            $query = 'SELECT '.$campos.' FROM '.$this->tabela.' '.$where.' '.$order.' '.$limit;

            //EXECUTA A QUERY
            return $this->executar($query);
        }


        public function atualizar($where,$values){
            //DADOS DA QUERY
            $campos = array_keys($values);

            //MONTA A QUERY
            $query = 'UPDATE '.$this->tabela.' SET '.implode('=?,',$campos).'=? WHERE '.$where;

            //EXECUTAR A QUERY
            $this->executar($query,array_values($values));

            //RETORNA SUCESSO
            return true;
        }


        public function deletar($where) {
            $query = 'DELETE FROM '.$this->tabela.' WHERE '.$where;
            $this->executar($query);

            return true;
        }

    }
?>