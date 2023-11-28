<?php
include 'service/auth.php';

session_start();

logout();

header('Location: login.php');