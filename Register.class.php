<?php

/**
 * @author neo-scribe@ukr.net
 * @link scribe-soft.at.ua
 * 
 * Construct options is:
 * ['table'] = the name of users table
 * 
 */
class Register {
    private $args = array();
    private $checked = false;
    public $correct = false;

    /**
     * Function of creating Register object
     * ['table'] is the name of table in dababase
     * @param array with different arguments and one of them are ['table']. 
     */
    public function __construct(array $args) {
        if (is_array($args) && isset($args['table']) && count($args) >= 3) {
            $this->args = $args;
            $this->correct = true;
        } else {
            $this->correct = false;
        }
    }

    /**
     *  Function to create a user at base
     *  You must to place a datatype identifier like in SafeMySQL class but without a '?'
     *  Field "table" is required! And he must be the first in array!
     *  like this:
     *  $args = array ('table' => 'test',
     *                 'slogin' => 'new',
     *                 'spass' => 'mypass'
     *                  )
     *  'slogin' means 's' is a string, 'login' as a field name
     *  The SQL query become like this:
     *  'INSERT INTO test (login, pass) VALUES ('new','mypass')'
     * @param $db is a object of SafeMySQL class 
     * @return TRUE|FALSE if correct or not 
     */
    public function RegisterUser(SafeMySQL $db) {
        if ($this->Check($db)) {
            $second_args = array();
            $sql = "INSERT INTO {$this->args['table']} ";
            $comma = '';
            foreach ($this->args as $key => $value) {
                if ($key != 'table') {
                    $second_args[$key] = $value;
                    $names .= $comma . substr($key, 1, strlen($key));
                    $values .= $comma . '?' . substr($key, 0, 1);
                    $comma = ',';
                }
            }
            $sql .= '(' . $names . ') VALUES (' . $values . ')';
            $db->query($sql, $second_args);
            return true;
        }
        return false;
    }

    public function Check(SafeMySQL $db) {
        if ($this->correct) {
            $login_key = array_keys($this->args);
            $login = $this->args[$login_key[1]];
            $login_key = substr($login_key[1], 1, strlen($login_key[1]));
            $sql = "SELECT id FROM ?n WHERE ?n=?s";
            $id = $db->getOne($sql, $this->args['table'], $login_key, $login);
            if ($id) {
                return false;
            }
            return true;
        }
    }
}
