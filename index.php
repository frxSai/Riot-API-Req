<?php
require __DIR__ . '/vendor/autoload.php';

use Cowsayphp\Farm;

// Redirect to src/login.php
header('Location: /login.php');
exit();

// This code will not execute after the redirect