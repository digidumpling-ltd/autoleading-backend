<?php
$sock = fsockopen('127.0.0.1', 1025, $err, $errstr, 2);
echo $sock ? 'SMTP 127.0.0.1:1025 reachable' . PHP_EOL : 'FAILED: ' . $errstr . PHP_EOL;

$sock2 = fsockopen('mailpit', 1025, $err2, $errstr2, 2);
echo $sock2 ? 'SMTP mailpit:1025 reachable' . PHP_EOL : 'mailpit host FAILED: ' . $errstr2 . PHP_EOL;
