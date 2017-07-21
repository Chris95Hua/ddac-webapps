<?php
require_once ('/../modal/core/setup.php');

$user->logout();

session_destroy();

header('Location: /TCS/'.Config::get('home'));
exit();