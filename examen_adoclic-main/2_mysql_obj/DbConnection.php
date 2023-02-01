<?php
class DbConnection implements ConfigDbInterface
{

    protected $connection;

    public function __construct()
    {
        if (isset($this->connection)) return false;

        $this->set_connection();
    }


    private function set_connection()
    {
        try {

            $this->connection = new PDO("mysql:host=" . self::DB_HOST . ";dbname=" . self::DB_NAME, self::DB_USER, self::DB_PASSWORD, [PDO::ATTR_ERRMODE => self::ATTR_ERRMODE]);
        } catch (PDOException $e) {
            die("Failed to connect with MySQL: " . $e->getMessage());
        }
    }

    public function get_connection()
    {
        return $this->connection;
    }
}
