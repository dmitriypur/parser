<?php

$id = '10pJaTALJKq3NZynWiXuyvGNc-giVhaRKeflC9Nn32kE';
$gid = '0';

$csv = file_get_contents('https://docs.google.com/spreadsheets/d/1idtD_ufpqET_o5esO9w0M4jHzEhb59vZwASBnNBLDTQ/edit?usp=sharing');
$csv = explode("\r\n", $csv);
$table_arr = array_map('str_getcsv', $csv);

https://docs.google.com/spreadsheets/d/1idtD_ufpqET_o5esO9w0M4jHzEhb59vZwASBnNBLDTQ/edit#gid=1023260828