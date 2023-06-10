<?php
/**
 * Validator class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\DatabaseManager;

use PDO;
use PDOException;

class Database
{
    /**
     * Host de conexão com o banco de dados
     * @var string
     */
    private static $host;

    /**
     * Nome do banco de dados
     * @var string
     */
    private static $name;

    /**
     * Usuário do banco
     * @var string
     */
    private static $user;

    /**
     * Senha de acesso ao banco de dados
     * @var string
     */
    private static $pass;

    /**
     * Porta de acesso ao banco
     * @var integer
     */
    private static $port;

    /**
     * Nome da tabela a ser manipulada
     * @var string
     */
    private $table;

    /**
     * Instancia de conexão com o banco de dados
     * @var PDO
     */
    private $connection;

    /**
     * Método responsável por configurar a classe
     * @param  string  $host
     * @param  string  $name
     * @param  string  $user
     * @param  string  $pass
     * @param  integer $port
     */
    public static function config($host, $name, $user, $pass, $port = 3306)
    {
        self::$host = $host;
        self::$name = $name;
        self::$user = $user;
        self::$pass = $pass;
        self::$port = $port;
    }

    /**
     * Define a tabela e instancia e conexão
     * @param string $table
     */
    public function __construct($table = null)
    {
        $this->table = $table;
        $this->setConnection();
    }

    /**
     * Método responsável por criar uma conexão com o banco de dados
     */
    private function setConnection()
    {
        try {
            $this->connection = new PDO('mysql:host=' . self::$host . ';dbname=' . self::$name . ';port=' . self::$port, self::$user, self::$pass);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die('ERROR: ' . $e->getMessage());
        }
    }

    /**
     * Método responsável por executar queries dentro do banco de dados
     * @param  string $query
     * @param  array  $params
     * @return \PDOStatement
     */
    public function execute($query, $params = [])
    {
        try {
            $statement = $this->connection->prepare($query);
            $statement->execute($params);
            return $statement;
        } catch (PDOException $e) {
            die('Error executing query: ' . $e->getMessage());
        }
    }

    /**
     * Método responsável por inserir dados no banco
     * @param  array $values [ field => value ]
     * @return integer ID inserido
     */
    public function insert($values)
    {
        //DADOS DA QUERY
        $fields = array_keys($values);
        $binds = array_pad([], count($fields), '?');

        //MONTA A QUERY
        $query = 'INSERT INTO ' . $this->table . ' (' . implode(',', $fields) . ') VALUES (' . implode(',', $binds) . ')';

        //EXECUTA O INSERT
        $this->execute($query, array_values($values));

        //RETORNA O ID INSERIDO
        return $this->connection->lastInsertId();
    }

    /**
     * Método responsável por inserir várias linhas de dados no banco
     * @param  array $data [ array( array( field => value, ...), ... )]
     */
    public function insertMany($data)
    {
        // Criamos uma consulta com múltiplos placeholders para as inserções
        $fields = array_keys($data[0]);
        $placeholders = implode(',', array_fill(0, count($data), '(' . implode(',', array_fill(0, count($fields), '?')) . ')'));

        // Criamos um array plano com todos os valores a serem inseridos
        $values = array_reduce($data, function ($carry, $item) {
            return array_merge($carry, array_values($item));
        }, []);

        // Montamos a consulta SQL
        $sql = sprintf('INSERT INTO %s (%s) VALUES %s', $this->table, implode(',', $fields), $placeholders);

        // Executamos a consulta
        $this->execute($sql, $values);
    }

    /**
     * Método responsável por executar uma consulta no banco
     * @param  string $where
     * @param  string $order
     * @param  string $limit
     * @param  string $fields
     * @return \PDOStatement
     */
    public function select($where = null, $order = null, $limit = null, $fields = '*')
    {
        // DADOS DA QUERY
        if ($where === null) {
            $where = [];
        }

        $wheres = [];
        $values = [];
        foreach ($where as $key => $value) {
            if ($key === 'date BETWEEN' && is_array($value) && count($value) === 2) {
                $wheres[] = $key . ' ? AND ?';
                $values[] = $value[0];
                $values[] = $value[1];
            } elseif ($key === 'date_end <' && is_numeric($value)) {
                $wheres[] = 'date_end < FROM_UNIXTIME(?)';
                $values[] = $value;
            } elseif (is_array($value)) {
                // Handle OR conditions
                $orConditions = [];
                foreach ($value as $orValue) {
                    $orConditions[] = $key . ' = ?';
                    $values[] = $orValue;
                }
                $wheres[] = '(' . implode(' OR ', $orConditions) . ')';
            } elseif (strpos($key, 'LIKE') !== false) {
                $wheres[] = $key . ' ?';
                $values[] = "%{$value}%";
            } else {
                // Handle operators other than =
                $split = explode(' ', $key);
                if (count($split) == 2) {
                    [$key, $operator] = $split;
                    $wheres[] = $key . ' ' . $operator . ' ?';
                } else {
                    $wheres[] = $key . ' = ?';
                }
                $values[] = $value;
            }
        }

        $whereString = implode(' AND ', $wheres);
        if (strlen($whereString)) {
            $whereString = ' WHERE ' . $whereString;
        }

        $order = $order && strlen($order) ? ' ORDER BY ' . $order : '';
        $limit = $limit && strlen($limit) ? ' LIMIT ' . $limit : '';

        if (is_array($fields)) {
            $fieldString = implode(', ', $fields);
        } else {
            $fieldString = $fields;
        }

        // MONTA A QUERY
        $query = 'SELECT ' . $fieldString . ' FROM ' . $this->table . $whereString . $order . $limit;

        // EXECUTA A QUERY
        return $this->execute($query, $values);
    }

    public function selectLike($where = null, $like = null, $order = null, $limit = null, $fields = '*')
    {
        // DADOS DA QUERY
        if ($where === null) {
            $where = [];
        }

        $wheres = [];
        $values = [];
        foreach ($where as $key => $value) {
            $wheres[] = $key . ' = ?';
            $values[] = $value;
        }

        if ($like !== null) {
            foreach ($like as $key => $value) {
                $wheres[] = $key . ' LIKE ?';
                $values[] = '%' . $value . '%';
            }
        }

        $whereString = implode(' AND ', $wheres);
        if (strlen($whereString)) {
            $whereString = ' WHERE ' . $whereString;
        }

        $order = $order && strlen($order) ? ' ORDER BY ' . $order : '';
        $limit = $limit && strlen($limit) ? ' LIMIT ' . $limit : '';

        if (is_array($fields)) {
            $fieldString = implode(', ', $fields);
        } else {
            $fieldString = $fields;
        }

        // MONTA A QUERY
        $query = 'SELECT ' . $fieldString . ' FROM ' . $this->table . $whereString . $order . $limit;

        // EXECUTA A QUERY
        return $this->execute($query, $values);
    }


    /**
         * Método responsável por executar atualizações no banco de dados
         * @param  string $where
         * @param  array $values [ field => value ]
         * @return boolean
         */
        public function update($where, $values)
        {
            // DADOS DA QUERY
            $fields = array_keys($values);
            $params = array_values($values);

            // Tratar as condições WHERE
            if (is_array($where)) {
                $wheres = [];
                foreach ($where as $key => $value) {
                    $split = explode(' ', $key);
                    if (count($split) == 2) {
                        [$key, $operator] = $split;
                        $wheres[] = $key . ' ' . $operator . ' ?';
                    } else {
                        $wheres[] = $key . ' = ?';
                    }
                    $params[] = $value;
                }

                $whereString = implode(' AND ', $wheres);
                if (strlen($whereString)) {
                    $whereString = ' WHERE ' . $whereString;
                }
            } else {
                // For backward compatibility, if $where is a string
                $whereString = ' WHERE ' . $where;
            }

            // MONTA A QUERY
            $query = 'UPDATE ' . $this->table . ' SET ' . implode('=?, ', $fields) . '=?' . $whereString;

            // EXECUTA A QUERY
            $this->execute($query, $params);

            // RETORNA SUCESSO
            return true;
        }

    /**
     * Método responsável por excluir dados do banco
     * @param  string $where
     * @return boolean
     */
    public function delete($where)
    {
        $allowedTables = ['canary_items'];

        // Check if $where is an array and prepare it accordingly
        if (is_array($where)) {
            $keys = array_keys($where);
            $values = array_values($where);
            $placeholders = array_fill(0, count($keys), '= ?');

            $whereString = implode(' AND ', array_map(function ($key, $placeholder) {
                return $key . $placeholder;
            }, $keys, $placeholders));
        } else {
            // For backward compatibility, if $where is a string
            $whereString = $where;
            $values = [];
        }

        // Verificar se é uma tabela específica para truncamento
        $isTruncatableTable = in_array($this->table, $allowedTables, true);

        // MONTA A QUERY
        $query = $isTruncatableTable ? 'TRUNCATE TABLE ' . $this->table : 'DELETE FROM ' . $this->table . ' WHERE ' . $whereString;

        // EXECUTA A QUERY
        $this->execute($query, $values);

        // RETORNA SUCESSO
        return true;
    }

}
