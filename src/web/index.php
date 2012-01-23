<html>
<head>
<title>Boxcat!</title>
</head>
<body>
<?php

echo "Testing the db...<br />\n";
$query = db_conn()->prepare('show tables');
$query->execute();
echo $query->fetch();

?>
</body>
</html>