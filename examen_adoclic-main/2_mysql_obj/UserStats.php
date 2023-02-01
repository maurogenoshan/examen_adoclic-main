<?php
class UserStats extends DbConnection
{

    private $dateFrom, $dateTo, $totalClicks;

    public function __construct(string $dateFrom, string $dateTo, int $totalClicks = null)
    {

        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->totalClicks = $totalClicks;

        $this->connection =  (new DbConnection())->get_connection();
    }

    public function get_stats()
    {
        $statement = $this->connection->prepare("SELECT * FROM user_stats WHERE DATE('date','Y-M-D') BETWEEN :dateFrom AND :dateTo");

        $statement->execute(
            array(
                ":dateFrom" => date("Y-M-D", strtotime($this->dateFrom)), PDO::PARAM_STR,
                ":dateTo" => date("Y-M-D", strtotime($this->dateFrom)), PDO::PARAM_STR
            )
        );


        $publisher = $statement->fetch(PDO::FETCH_ASSOC);
        var_dump($publisher);
        if ($publisher) {
            echo $publisher['userId'] . '.' . $publisher['views'];
        } else {
            echo "The publisher with id $this->dateTo was not found.";
        }
    }
}
