<?php
// core/config.php

// This file now relies on environment variables loaded by Dotenv in core/init.php.
// It acts as a bridge to get the credentials securely.

return [
    'host'     => $_ENV['DB_HOST'] ?? 'localhost',
    'dbname'   => $_ENV['DB_NAME'] ?? 'u746374185_icensus',
    'user'     => $_ENV['DB_USER'] ?? 'u746374185_icensus',
    'password' => $_ENV['DB_PASS'] ?? '',
    'charset'  => 'utf8mb4'
];