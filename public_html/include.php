<?php

// You gotta make a config file!
require("config.php");

// a failed connection or query throws, and php logs it
$Database = mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE);

// click's text is stored as utf8mb4, see migrations/utf8mb4.sql, but its
// queries still rely on mariadb's old permissive sql mode
mysqli_set_charset($Database, 'utf8mb4');
mysqli_query($Database, "SET SESSION sql_mode = ''");

function db_query($Query)
{
    global $Database;
    return mysqli_query($Database, $Query);
}

// escapes a value for a quoted string in a query
function db_escape($Value)
{
    global $Database;
    return mysqli_real_escape_string($Database, (string) $Value);
}

function replace_input($input)
{
    $output = stripslashes((string) $input);
    $output = filter_var($output, FILTER_SANITIZE_SPECIAL_CHARS);
    $output = trim($output, "/");

    return $output;
}

function replace_output($input)
{
    $output = str_ireplace('icanhazchat.com/pibdgaf', 'wetfish.net', $input);
    $output = str_ireplace('http://', 'https://', $input);

    return $output;
}

?>
