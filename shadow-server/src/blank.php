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

    $db->close();
}

main();
echo json_encode($res);
?> 