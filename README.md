### CREATE config/database.php

copy & paste below in dataase.php
```
<?php

$host = 'YOUR HOST';
$username = 'YOUR USERNAME';
$password = 'YOUR PASSWORD';
$dbname = 'YOUR DATABASE NAME';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
