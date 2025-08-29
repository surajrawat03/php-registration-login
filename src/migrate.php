<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\class\schemaManager;

$schema = new schemaManager();
$schema->migrate();
