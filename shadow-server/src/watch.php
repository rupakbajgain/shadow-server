<!DOCTYPE html>
<html>
<head>
    <title>Control Panel(Watch)</title>
    <style>
table, th, td {
  border: 1px solid black;
}
    </style>
</head>
<body>
    <h2>Control Panel(Watch)</h2>
    <table width=100%>
    <tr>
        <th>ID</th>
        <th>From</th>
        <th>To</th>
        <th>Message</th>
        <th>Actions</th>
    </tr>
    <?php
function sanitize($name) {
    return str_replace(array_merge(
        array_map('chr', range(0, 31)),
        array('<', '>', ':', '"', '/', '\\', '|', '?', '*')
    ), '', $name);
}

    $channel = $_GET['c'];
    if(isset($channel)){
        $db = new SQLite3('db/'.sanitize($channel).'.db');
        $ret = $db->query("SELECT * from MSG");
        while($row = $ret->fetchArray(SQLITE3_ASSOC) ) {
            echo "<tr>";
            echo "<td>".$row['ID']."</td>";
            echo "<td>".$row['F']."</td>";
            echo "<td>".$row['T']."</td>";
            echo "<td>".$row['M']."</td>";
            echo "<td><a href='delete.php?c=$channel&i=".$row['ID']."'>Delete</a></td>";
            echo "</tr>";
        }
    }
    ?>
    </table>
</body>
</html>