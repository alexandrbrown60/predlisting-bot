<?php
header("Content-type: text/html; charset=utf-8");
//ошибки
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require 'constants.php';
require 'classes/DatabaseConnection.php';
require 'classes/DatabaseManager.php';

$database = new DatabaseManager('alexanb0_listing');
$database->deleteNotRelevant();