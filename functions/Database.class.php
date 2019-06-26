<?php

class Database{

    // Attributes for MySQL connection
    private $host = "localhost";
    private $username = "iste330t14";
    private $password = "student";
    private $dbname = "iste330t14";
    private $lockEnable = False;
    private $pdo;

    /* A default constructor that will implement the PDO connection
     */
    public function __construct()
    {
        $dsn =  "mysql:host=$this->host;dbname=$this->dbname";
        //Connect
        $this->pdo = new \PDO($dsn,$this->username,$this->password);
        //Show all pdo errors
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_EXCEPTION);

        //Ensure use prepare
        $this->pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES,false);
        return $this->pdo;
    }

    /**
     * This function will return the PDO connection
     * @return PDO connection
     */
    function get_connection(){
        return $this->pdo;
    }

    /**
     * This function will start the transaction
     */
    private function beginTransaction()
    {
        if(!$this->lockEnable)
        {
            $this->lockEnable = True;
            $this->pdo->beginTransaction();
        }
    }

    /**
     * This function will execute the query
     * @param $sql - the prepared SQL statement
     * @param $bindings - the binds variables
     * @return boolean
     */
    public function query($sql,$bindings=array())
    {
        $this->beginTransaction();

        if(count($bindings)>=1)
        {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($bindings);

            //success?
            if($stmt->rowCount() > 0)
            {
                return $stmt;
            }
            return false;
        }

        //No parameter binding
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        //success?
        if($stmt->rowCount() > 0)
        {
            return $stmt;
        }

        return false;
    }


    /**
     * This function will commit a transaction
     * @return boolean
     */
    public function commit()
    {
        if($this->lockEnable)
        {
            $this->lockEnable = false;
           return $this->pdo->commit();
        }
    }

    /**
     * This function will rollback a transaction
     * @return boolean
     */
    public function rollback(){

        if($this->lockEnable)
        {
            $this->lockEnable = false;
            return $this->pdo->rollBack();
        }
    }

}   // End of the database


?>
