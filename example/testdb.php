<?php
declare(strict_types=1);

require './vendor/autoload.php';
require './src/QueryBuilderWrapper.php';

use Basttyy\ReactphpOrm\QueryBuilderWrapper;
use Basttyy\ReactphpOrm\QueryBuilder;
use React\MySQL\Exception as QueryException;
use Illuminate\Support\Collection;
use React\MySQL\Factory;
use React\MySQL\QueryResult;
use React\Promise\PromiseInterface;

$factory = new Factory();

$connection = (new QueryBuilderWrapper($factory))->createLazyConnection('root:123456789@localhost/react-database');

$type = $argv[1] ? $argv[1] : 'query';

switch ($type) {
    case 'query':
        runQuery($connection);
        break;
    case 'runget':
        runGet($connection);
        break;
    case 'insert':
        runInsert($connection);
        break;
    default:
        runQuery($connection);
}

function runQuery(PromiseInterface|QueryBuilder $connection)
{
    $connection->from('users')->where('status', 'active')->query()->then(
        function (QueryResult $command) {
            print_r($command->resultRows);
            echo count($command->resultRows) . ' row(s) in set' . PHP_EOL;
        },
        function (Exception $error) {
            echo 'Error: ' . $error->getMessage() . PHP_EOL;
        }
    );
}

function runGet(PromiseInterface|QueryBuilder $connection)
{
    $connection->from('users')->where('status', 'active')->get()->then(
        function(Collection $data) {
            print_r($data->all());
            echo $data->count() . ' row(s) in set' . PHP_EOL;
        },
        function (Exception $error) {
            echo 'Error: ' . $error->getMessage() . PHP_EOL;
        }
    );
}

function runInsert(PromiseInterface|QueryBuilder $connection)
{
    $values = [
        'username' => 'basttyy',
        'firstname' => 'abdulbasit',
        'lastname' => 'mamman',
        'email' => 'basttyy@mail.com'
    ];
    $connection->from('users')->insert($values)->then(
        function (bool $status) {
            echo "inserted successfully ".PHP_EOL;
        },
        function (Exception $ex) {
            echo $ex->getMessage().PHP_EOL;
        }
    );
}

$connection->quit();