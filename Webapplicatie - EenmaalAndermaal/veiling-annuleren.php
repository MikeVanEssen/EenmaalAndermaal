<?php

require_once 'includes/header.php';

Session::delete('veilingData');

header('Location: accountpagina.php');

?>