<?php
$db = new SQLite3(__DIR__ . '/database/database.sqlite');
$res = $db->query('SELECT id,owner_id,name FROM projects');
$found = false;
while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
    $found = true;
    echo $row['id'] . '|' . $row['owner_id'] . '|' . $row['name'] . PHP_EOL;
}
if (! $found) {
    echo "NO PROJECTS\n";
}
