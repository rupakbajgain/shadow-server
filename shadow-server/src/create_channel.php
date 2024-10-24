<?php
$res = new StdClass;
$res->success = TRUE;

function sanitize($name) {
    return str_replace(array_merge(
        array_map('chr', range(0, 31)),
        array('<', '>', ':', '"', '/', '\\', '|', '?', '*')
    ), '', $name);
}

function main(){
    global $res;
    $channel = $_GET['c'];

    if(!isset($channel)){
        $res->success = FALSE;
        $res->err = "Channel name required";
        return;
    }

    $db = new SQLite3('db/'.sanitize($channel).'.db');
    if(!$db) {
        $res->success = FALSE;
        $res->err = $db->lastErrorMsg();
        return;
    };

    $sql =<<<EOF
    CREATE TABLE MSG
    (ID INTEGER PRIMARY KEY AUTOINCREMENT,
    F       TEXT,
    T         TEXT,
    M        TEXT);
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
