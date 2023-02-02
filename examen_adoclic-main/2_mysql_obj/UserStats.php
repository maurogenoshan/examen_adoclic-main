<?php
class UserStats extends DbConnection
{

    private $dateFrom, $dateTo, $totalClicks;

    public function __construct(string $dateFrom, string $dateTo, int $totalClicks = null)
    {

        $this->dateFrom =  $this->get_string_to_date($dateFrom);
        $this->dateTo =  $this->get_string_to_date($dateTo);
        $this->totalClicks = (int) $totalClicks;

        $this->connection =  (new DbConnection())->get_connection();
    }

    private function get_string_to_date($string)
    {
        return date('Y-m-d', strtotime($string));
    }

    private function is_range_of_dates()
    {
        return isset($this->dateFrom) && !empty($this->dateFrom) && isset($this->dateTo) && !empty($this->dateTo);
    }

    private function is_clicks_not_empty()
    {
        return isset($this->totalClicks) && !empty($this->totalClicks);
    }
    private function get_where_conditions_conditionally()
    {
        $where = " users.status = 'active'";
        $where .= ($this->is_range_of_dates()) ? " AND DATE(user_stats.date) > :dateFrom AND DATE(user_stats.date) < :dateTo" : '';
        $where .= ($this->is_clicks_not_empty()) ? " AND user_stats.clicks > :totalClicks" : '';

        return $where;
    }

    public function get_stats()
    {
        $where = $this->get_where_conditions_conditionally();


        $query = $this->connection->prepare("SELECT users.id,DATE(user_stats.date) as date_stats, user_stats.views,user_stats.clicks,user_stats.conversions, users.status FROM user_stats INNER JOIN users ON user_stats.user_id = users.id WHERE $where ORDER BY date ASC");
        if ($this->is_range_of_dates()) {
            $query->bindParam(':dateFrom', $this->dateFrom, PDO::PARAM_STR);
            $query->bindParam(':dateTo', $this->dateTo, PDO::PARAM_STR);
        }
        if ($this->is_clicks_not_empty()) {
            $query->bindParam(':totalClicks', $this->totalClicks, PDO::PARAM_INT);
        }


        $query->execute();

        $user_stats = $query->fetchaLL(PDO::FETCH_ASSOC);
        if (!$user_stats)  return [];
        return $user_stats;
    }
}
