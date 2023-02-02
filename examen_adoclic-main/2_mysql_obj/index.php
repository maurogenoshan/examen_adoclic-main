<?php
require_once('ConfigDbInterface.php');
require_once('DbConnection.php');
require_once('UserStats.php');

$user_stats = (new UserStats('2022-10-02', '2022-10-21', 600))->get_stats(1);

var_dump($user_stats);
