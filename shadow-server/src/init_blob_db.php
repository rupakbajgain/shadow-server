<?php
$res = new StdClass;
$res->success = TRUE;

function main(){
    global $res;

    $db = new SQLite3('blob.db');
    if(!$db) {
        $res->success = FALSE;
        $res->err = $db->lastErrorMsg();
        return;
    };

    $sql =<<<EOF
    CREATE TABLE B
    (ID INTEGER PRIMARY KEY AUTOINCREMENT,
    C       BLOB,
    T       TEXT,
    P       TEXT);
    EOF;

    $ret = $db->exec($sql);
    if(!$ret){
        $res->success = FALSE;
        $res->err = $db->lastErrorMsg();
        return;
    }

    $sql ="PRAGMA journal_mode=WAL;";

    $ret = $db->exec($sql);
    if(!$ret){
        $res->success = FALSE;
        $res->err = $db->lastErrorMsg();
        return;
    }

    $db->close();
}

main();
header('Content-Type: application/json');
echo json_encode($res);
?> 
