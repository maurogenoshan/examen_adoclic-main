<?php
require_once('ConfigDbInterface.php');
require_once('DbConnection.php');
require_once('UserStats.php');

$user_stats = (new UserStats('2022-10-01', '2022-10-09', 1))->get_stats(1);
//SELECT users.id,DATE(user_stats.date) as date_stats, user_stats.views,user_stats.clicks,user_stats.conversions, users.status FROM user_stats INNER JOIN users ON user_stats.user_id = users.id WHERE DATE(user_stats.date) > "2022-10-01" AND DATE(user_stats.date) < "2022-10-03" AND users.status = 'active' AND user_stats.clicks > 500 ORDER BY date ASC;
var_dump($user_stats);
