<?php
require __DIR__ . '/vendor/autoload.php';

use Cowsayphp\Farm;

// Redirect to src/login.php
header('Location: src/login.php');
exit();

// This code will not execute after the redirect
