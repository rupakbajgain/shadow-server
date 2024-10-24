<?php
$res = new StdClass;
$res->success = TRUE;

function sanitize($name) {
    return str_replace(array_merge(
        array_map('chr', range(0, 31)),
        array('<', '>', ':', '"', '/', '\\', '|', '?', '*')
    ), '', $name);
}

$channel = $_GET['c'];

if(!isset($channel)){
    $res->success = FALSE;
    $res->err = "Channel name required";
    return;
}


if(!unlink('db/'.$channel.'.db')){
    $res->success = FALSE;
    $res->err="Could not delete channel";
};

header('Content-Type: application/json');
echo json_encode($res);
?>