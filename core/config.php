<?php
// core/config.php

return [
    'host'     => $_ENV['DB_HOST'] ?? 'localhost',
    'dbname'   => $_ENV['DB_NAME'] ?? 'u746374185_icensus',
    'user'     => $_ENV['DB_USER'] ?? 'u746374185_icensus',
    'password' => $_ENV['DB_PASS'] ?? '',
    'charset'  => 'utf8mb4'
];