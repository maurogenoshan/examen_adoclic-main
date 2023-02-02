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

    private function get_string_to_date($string)
    {
        return date('Y-m-d', strtotime($string));
    }

    public function get_stats()
    {
        $query = $this->connection->prepare("SELECT users.id,DATE(user_stats.date) as date_stats, user_stats.views,user_stats.clicks,user_stats.conversions, users.status FROM user_stats INNER JOIN users ON user_stats.user_id = users.id WHERE DATE(user_stats.date) > :dateFrom AND DATE(user_stats.date) < :dateTo AND users.status = 'active' AND user_stats.clicks > :totalClicks ORDER BY date ASC");

        $query->bindParam(':dateFrom', $this->get_string_to_date($this->dateFrom), PDO::PARAM_STR, 10);
        $query->bindParam(':dateTo', $this->get_string_to_date($this->dateTo), PDO::PARAM_STR, 10);
        $query->bindParam(':totalClicks', $this->totalClicks, PDO::PARAM_INT, 10);

        $query->execute();

        $user_stats = $query->fetch(PDO::FETCH_ASSOC);

        if (!$user_stats)  return [];
        return $user_stats;
    }
}
