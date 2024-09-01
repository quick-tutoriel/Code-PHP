<?php
class dbConfig {
    protected $serverName;
    protected $userName;
    protected $password;
    protected $dbName;
    function dbConfig() {
        $this -> serverName = 'localhost';
	$this -> userName = 'admin_ftp';
        $this -> password = 'Foxmulder87@';
        $this -> dbName = 'PUREFTPD_BDD';
    }
}
?>
