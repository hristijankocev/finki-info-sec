<?php
include 'service/authentication.php';

session_start();

logout();

header('Location: login.php');