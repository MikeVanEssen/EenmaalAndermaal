<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/EenmaalAndermaal/core/init.php';

$search = escape($_POST['search-field']);

header('Location: ../veilingoverzicht.php?search=' . $search);
//exit();