<!DOCTYPE html>
<html>
<head>
    <title>Control Panel</title>
    <style>
table, th, td, form {
  border: 1px solid black;
}
    </style>
</head>
<body>
    <h2>Control Panel</h2>

    <h3>Channel</h3>
    <form method="GET" action="create_channel.php">
        <input name="c"/>
        <input type="submit" value="Create channel">
    </form><br/>
    <table width=100%>
    <tr>
        <th>Name</th>
        <th>Size</th>
        <th>Actions</th>
    </tr>
    <?php
    foreach (glob("db/*.db") as $filename) {
        $base = basename($filename,".db");
        echo "<tr>";
        echo "<td>$base</td>";
        echo "<td>".filesize($filename)."</td>";
        echo "<td><a href='watch.php?c=$base'>Watch</a> <a href='delete_channel.php?c=$base'>Delete</a></td>";
        echo "</tr>";
    };
    ?>
    </table>

    <h4>Post message:</h4>
    <!--
        could also post to send.php
        get(c)<-channel name
        post(json)<-{f:from,t:to,m:message}
    -->
    <form method="POST" action="send_raw.php">
    Channel:&emsp;<input name="c"><br/>
    From:&emsp;&emsp;<input name="f"><br/>
    To:&emsp;&emsp;&emsp;&emsp;<input name="t"><br/>
    Message:&emsp;<input name="m"><br/>
    <input type="submit" value="Post">
    </form>

    <h3>Blob</h3>
    <form method="POST" action="upload_blob.php" enctype="multipart/form-data">
        File: &emsp;<input name="f" type="file"/><br/>
        Path:&emsp;<input name="p"/><br/>
        <input type="submit" value="Upload">
    </form><br/>
    <table width=100%>
    <tr>
        <th>ID</th>
        <th>Type</th>
        <th>Size</th>
        <th>Path</th>
        <th>Actions</th>
    </tr>
    <?php
    $exists = file_exists('blob.db');
    if($exists){
    //echo "File found!";
    $db = new SQLite3('blob.db');
    $ret = $db->query("SELECT ID, T, P, LENGTH(C) AS S from B");
    while($row = $ret->fetchArray(SQLITE3_ASSOC) ) {
        echo "<tr>";
        echo "<td>".$row['ID']."</td>";
        echo "<td>".$row['T']."</td>";
        echo "<td>".$row['S']."</td>";
        echo "<td>".$row['P']."</td>";
        echo "<td><a href='get_blob.php?i=".$row['ID']."'>View</a> <a href='delete_blob.php?i=".$row['ID']."'>Delete</a><br/>".'
        <form method="POST" action="update_blob.php?i='.$row['ID'].'" enctype="multipart/form-data">
        <input name="f" type="file"/>
        <input type="submit" value="Update">
        </form>'."
        </td>";
        echo "</tr>";
    }
    $db->close();
    }
    ?>
    </table>
    <br/>
    <h3>
        <?php
        if ($exists) {
            echo "<a href='delete_blob_db.php'>Delete blob</a>";
        }else{
            echo "<a href='init_blob_db.php'>Init blob</a>";
        }
        ?>
    </h3>
    <h3>ID</h3>
    <a href="gen_id.php">Get ID</a>
</body>
</html>
