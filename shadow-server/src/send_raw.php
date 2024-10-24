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
    $channel = $_POST['c'];

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

    $sql=$db->prepare("INSERT INTO MSG (F,T,M) VALUES(?,?,?)");
    $sql->bindParam(1, $_POST['f'], SQLITE3_TEXT);
    $sql->bindParam(2, $_POST['t'], SQLITE3_TEXT);
    $sql->bindParam(3, $_POST['m'], SQLITE3_TEXT);
    
    if(!$sql->execute()){
        $res->success = FALSE;
        $res->err = $db->lastErrorMsg();
        return;
    }

    $res->id = $db->lastInsertRowID();

    $db->close();
}

main();
header('Content-Type: application/json');
echo json_encode($res);
?> 