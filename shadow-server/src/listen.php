<?php
// params
// c = channel
// i = clientid , '' for server(empty)
// l = last received id

ob_implicit_flush(true);
#Get rid of output buffer entirely
while (ob_get_level()) {ob_end_flush();}

ignore_user_abort(true);
header('Content-Type: application/json');
echo '{"p":"';

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
    $db->busyTimeout(200);//required in this file

    $is_server = isset($_GET['i'])?$_GET['i']=='':TRUE;
    $id = $is_server?'':$_GET['i'];//empty means it is server
    $lid = isset($_GET['l'])?$_GET['l']:0;

    if(isset($from)){
        $from = $_GET['f'];
        $sql=$db->prepare("SELECT * FROM MSG WHERE (T=? OR T='*') AND ID > ? AND F = ?");
        $sql->bindParam(3, $from, SQLITE3_TEXT);
        //echo "Prepaired";
    }else
        $sql=$db->prepare("SELECT * FROM MSG WHERE (T=? OR T='*') AND ID > ?");
    $sql->bindParam(1, $id, SQLITE3_TEXT);
    $sql->bindParam(2, $lid, SQLITE3_INTEGER);
    //echo $sql->sql();

    $arr=array();
    $lrow=array();//rows to delete

    while(true){
        echo "0";
        if(connection_aborted())exit;
        //
        $ret = $sql->execute();
        while($row = $ret->fetchArray(SQLITE3_ASSOC) ) {
            if($is_server || $row['T']!='*')
                array_push($lrow, $row['ID']);
            if($row['F']==$id)continue;
            $row['M']=json_decode($row['M']);//we assume all messages are json from now
            array_push($arr, $row);
        }
        $ret->finalize();//close
        if(count($arr)==0){
            //sleep( 1 );
            usleep(200000);
            continue;
        }

        $ret = $db->exec("DELETE from MSG where ID in (".implode(',',$lrow).")");
        if(!$ret){
            $res->success = FALSE;
            $res->err = $db->lastErrorMsg();
            return;
        }

        $res->ret=$arr;
        $db->close();
        return;
    }
}

main();
echo '", ';
$json = json_encode($res);
echo substr($json, 1);
?>
