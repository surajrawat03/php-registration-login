<?php

use App\class\databaseConnection;
use Doctrine\DBAL\Query\QueryBuilder;

function doctrine(): QueryBuilder {
    $conn = databaseConnection::getConnection();
    return $conn->createQueryBuilder();
}
