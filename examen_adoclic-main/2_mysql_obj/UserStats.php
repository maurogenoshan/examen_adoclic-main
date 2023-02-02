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
        $where = $this->get_where_conditions_conditionally();
        return "SELECT 
        CONCAT($this->relation_table.first_name ,' ',$this->relation_table.last_name) AS 'full_name',
        DATE( $this->table_name.date) AS date_stats,
        SUM($this->table_name.views) AS total_views,
        SUM($this->table_name.clicks) AS total_clicks,
        SUM($this->table_name.conversions) AS total_conversions,
        SUM($this->table_name.conversions) / SUM($this->table_name.clicks)*100 as cf,
        MAX(DATE($this->table_name.date)) as last_date,
        $this->relation_table.status 
        FROM $this->table_name 
        INNER JOIN $this->relation_table ON $this->table_name.user_id = $this->relation_table.id 
        WHERE $where
        GROUP BY $this->table_name.user_id  
        ORDER BY $this->table_name.date ASC";
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
