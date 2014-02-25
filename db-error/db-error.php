<?php 

header('HTTP/1.1 503 Service Temporarily Unavailable');
header('Status: 503 Service Temporarily Unavailable');
header('Retry-After: 3600'); // 1 hour = 3600 seconds
mail("VOTREMAIL_ICI", "Database Bug", "Le site rencontre des soucis techniques (MySQL).", "From: Quick-Tutoriel");

?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir='ltr'>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Quick-Tutoriel est indisponible</title>
<style type="text/css">
html{background:#f9f9f9}
body{background:#fff; color:#333; font-family:sans-serif; margin:2em auto; padding:1em 2em 2em; -webkit-border-radius:3px; border-radius:3px; border:1px solid #dfdfdf; max-width:750px; text-align:center;}
#error-page{margin-top:50px}
#error-page p{font-size:14px; line-height:1.5; margin:25px 0 20px}
#error-page code{font-family:Consolas,Monaco,monospace}
a{color:#21759B; text-decoration:none}
a:hover{color:#D54E21}
</style>
</head>
<body id="error-page">
<h1>Capitaine, le navire coule !</h1>
<p>Quick-Tutoriel connaît actuellement quelques difficultés avec sa base de données. S'il vous plaît revenez plus tard..</p>
</body>

</html>
