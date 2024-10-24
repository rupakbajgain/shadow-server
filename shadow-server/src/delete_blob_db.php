<?php
$res = new StdClass;
$res->success = TRUE;

if(!unlink('blob.db')){
    $res->success = FALSE;
};

header('Content-Type: application/json');
echo json_encode($res);
?>