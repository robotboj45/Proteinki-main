<?php

session_start();

if(isset($_SESSION['user_id']))
{
	unset($_SESSION['user_id']);
    $_SESSION['user_group'] = 'user';

}

header("Location: ../index.php");
die;
