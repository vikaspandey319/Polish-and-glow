<?php
declare(strict_types=1);
function database(): PDO
{
    static $connection = null;
    if ($connection instanceof PDO) return $connection;
    $directory = __DIR__ . '/storage';
    if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) throw new RuntimeException('Unable to create the database directory.');
    $connection = new PDO('sqlite:' . $directory . '/enquiries.sqlite');
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $connection->exec('CREATE TABLE IF NOT EXISTS enquiries (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL, phone TEXT NOT NULL, interest TEXT NOT NULL, preferred_date TEXT, message TEXT, created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP)');
    return $connection;
}
