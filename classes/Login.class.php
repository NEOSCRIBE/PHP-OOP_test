<?php
/**
 * @author neo-scribe@ukr.net
 * @link scribe-soft.at.ua
 * 
 * Construct options is:
 * $login - entered login at web form
 * $pass - entered password at web form
 * $l_name - name of login field at database
 * $p_name - name of password field at database
 * $user_db - name of table where stored login and password
 */
class Login {
    private   $login = '';
    private    $pass = '';
    private  $l_name = '';
    private  $p_name = '';
    private $user_db = '';
    private $granted = false;
    
    public function __construct($login, $pass, $l_name, $p_name, $user_db) {
        $this->login = $login;
        $this->pass = $pass;
        $this->l_name = $l_name;
        $this->p_name = $p_name;
        $this->user_db = $user_db;
    }

    /**
     * Function to check if password correct
     * @param object of SafeMySQL class
     * @return TRUE|FALSE if correct or not 
     */
    public function check(SafeMySQL $db) {
        if (!function_exists('password_verify')){
            return false;
        }
        $this->granted = false;
        $some_psw = $db->getRow('SELECT id, ?n FROM ?n WHERE ?n=?s', $this->p_name, $this->user_db, $this->l_name, $this->login);
        if (password_verify($this->pass, $some_psw[$this->p_name])){        
            $this->granted = true;
            if ($this->accessGranted($db, $some_psw['id'])) {
                return true;
            }           
        }
        return false;
    }
    
    /**
     *  Function to create a session 
     */
    private function accessGranted(SafeMySQL $db, $id){
        if ($this->granted === true){
            session_start();
            $_SESSION['granted'] = (bool)true;
            $_SESSION['id'] = (int)$id;
            $sql = 'INSERT INTO ?n SET ?u';
            $data = array(
                              'user_id' => $id,
                              'user_ip' => $_SERVER['REMOTE_ADDR'],
                          'php_session' => session_id(),
                          );
            $db->query($sql, 'user_session', $data);
            $sql = 'UPDATE ?n SET online=1, session=(SELECT MAX(id)id FROM ?n WHERE user_id=?i and php_session=?s) WHERE id=?i';
            $db->query($sql, 'user', 'user_session', $id, session_id(), $id);
            return true;    
        }
        return false;
    }
}
