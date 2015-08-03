<?php
class User {
    private $id;
    private $login;
    private $db;
    public $info = array();
    
    public function __construct(SafeMySQL $db, $id){
        if (is_int($id) && $id > 0) {
            $this->id = $id;
            $this->db = $db;
            
        }    
    }
    
    public function GetInfo(){
        if (isset($this->id)) {            
            $this->info = $this->db->getRow('SELECT a.*, (CASE WHEN b.union IS NULL THEN 0 ELSE b.union END)\'union\' FROM user a LEFT JOIN user_clan b ON a.clan=b.id WHERE a.id=?i', $this->id);           
            return true;             
        }
        return false;
    }
    
    public function SetOnline($online){
        if (is_bool($online)) {
            if ($online) {
                $this->db->query('UPDATE user SET online=?i WHERE id=?i', '1',  $this->id);   
            } else {
                $this->db->query('UPDATE user SET online=?i WHERE id=?i', '0',  $this->id);
            }
                            
        }           
    }
    
    public function CheckOnline(){
        if (isset($this->id) && !empty($this->id)) {          
            if ($this->db->getOne('SELECT online FROM user WHERE id=?i', $this->id) === '1'){
                return true;
            }                     
        }
        return false;
    }
    
    public function SetUpdate(){
        $this->db->query('UPDATE user_session SET time_update=?s WHERE id=(SELECT session FROM user WHERE id=?i)', date(DATE_ATOM), $this->id);
    }        
}
