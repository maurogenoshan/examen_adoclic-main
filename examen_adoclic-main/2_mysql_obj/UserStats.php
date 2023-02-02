<?php
class UserStats extends DbConnection
{

    private $dateFrom, $dateTo, $totalClicks, $table_name, $relation_table;

    public function __construct(string $dateFrom, string $dateTo, int $totalClicks = null)
    {

        $this->dateFrom =  $this->get_string_to_date($dateFrom);
        $this->dateTo =  $this->get_string_to_date($dateTo);
        $this->totalClicks = (int) $totalClicks;
        $this->connection =  (new DbConnection())->get_connection();
        $this->table_name = 'user_stats';
        $this->relation_table = 'users';
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

    private function get_query()
    {
        return "SELECT 
        $this->relation_table.id,DATE( $this->table_name.date) as date_stats, $this->table_name.views,user_stats.clicks,$this->table_name.conversions,$this->relation_table.status 
        FROM $this->table_name 
        INNER JOIN $this->relation_table ON $this->table_name.user_id = $this->relation_table.id 
        WHERE " . $this->get_where_conditions_conditionally() .
            " ORDER BY $this->table_name.date ASC";
    }
    private function get_where_conditions_conditionally()
    {
        $where = " users.status = 'active'";
        $where .= ($this->is_range_of_dates()) ? " AND DATE( $this->table_name.date) > :dateFrom AND DATE( $this->table_name.date) < :dateTo" : '';
        $where .= ($this->is_clicks_not_empty()) ? " AND  $this->table_name.clicks > :totalClicks" : '';

        return $where;
    }

    private function get_params($query)
    {
        if ($this->is_range_of_dates()) {
            $query->bindParam(':dateFrom', $this->dateFrom, PDO::PARAM_STR);
            $query->bindParam(':dateTo', $this->dateTo, PDO::PARAM_STR);
        }
        if ($this->is_clicks_not_empty()) {
            $query->bindParam(':totalClicks', $this->totalClicks, PDO::PARAM_INT);
        }
    }

    public function get_stats()
    {
        $sql = $this->get_query();


        $query = $this->connection->prepare($sql);

        $this->get_params($query);


        $query->execute();

        $user_stats = $query->fetchaLL(PDO::FETCH_ASSOC);
        if (!$user_stats)  return [];
        return $user_stats;
    }
}
