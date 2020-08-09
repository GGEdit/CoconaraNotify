<?php

class PDOWrapper{

    private $hostName;
    private $databaseName;
    private $userName;
    private $passWord;
    public $instance;

    public function __construct($hostName, $databaseName, $userName, $passWord){
        $this->hostName = $hostName;
        $this->databaseName = $databaseName;
        $this->userName = $userName;
        $this->passWord = $passWord;
    }

    public function connect(){
        $dsn = 'mysql:host=' . $this->hostName . ';dbname=' . $this->databaseName . ';charset=utf8';
        try{
            $this->instance = new PDO(
                $dsn, 
                $this->userName, 
                $this->passWord,
                array(PDO::ATTR_EMULATE_PREPARES => false)
            );
            return true;
        } catch(PDOException $e){
            return false;
        }
    }

    public function exists($tableName, $key, $value, $type){
        $stmt = $this->instance->prepare("SELECT * FROM " . $tableName . " WHERE " . $key . ' = :' . $key);
        $stmt->bindParam(":" . $key, $value, $type);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!$row){
            return false;
        }
        return true;
    }

    public function insert($tableName, $key, $value, $type){
        $stmt = $this->instance->prepare("INSERT INTO " . $tableName . " (" . $key . ") VALUES (:" . $key .")");
        $stmt->bindParam(":" . $key, $value, $type);
        $stmt->execute();
    }
}

?>