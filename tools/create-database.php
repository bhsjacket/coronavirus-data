<?php

require_once('../database.php');

$sql = "
CREATE DATABASE coronavirus;
USE coronavirus;
CREATE TABLE zip (
    zip INT(5) NOT NULL PRIMARY KEY,
    city VARCHAR(100) NOT NULL,
    income INT(11) NOT NULL,
    tests INT(11) NOT NULL,
    positive INT(11) NULL,
    percent_positive FLOAT NULL,
    population INT(11) NOT NULL,
    tests_per_mille FLOAT NOT NULL,
    cases INT(11) NOT NULL
);";
if( $database->query($sql) === true ) {
    echo 'Database configured successfully.';
} else {
    echo 'Error configuring database: ' . $database->error;
}