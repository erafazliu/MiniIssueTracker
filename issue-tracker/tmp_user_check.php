<?php

$db = new SQLite3(__DIR__.'/database/database.sqlite');
$res = $db->query('SELECT id,name,email,password FROM users');
while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
    echo $row['id'].'|'.$row['name'].'|'.$row['email'].'|'.$row['password'].PHP_EOL;
}
