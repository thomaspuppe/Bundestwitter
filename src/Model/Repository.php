<?php
namespace BT\Model;

use \BT\Service\DatabaseService;

class Repository
{
    protected $databaseService;
    protected $databaseTableName;


    public function __construct()
    {
        $this->databaseService = DatabaseService::getInstance();
    }

    public function findAll()
    {
        $dbResult = $this->databaseService->runQuery('SELECT * FROM ' . $this->databaseTableName);
        return $this->convertMysqlResultToModelArray($dbResult);
    }

    public function countAll($column = 'id')
    {
        $dbResult = $this->databaseService->runQuery('SELECT COUNT(' . $column . ') AS number FROM ' . $this->databaseTableName);
        $mysqlRow = mysqli_fetch_object($dbResult);
        return $mysqlRow->number;

    }

    public function findOneBy($whereArray = null)
    {

        $query = 'SELECT * FROM ' . $this->databaseTableName;

        if (is_array($whereArray)) {
            $queryWhere = '';
            foreach ($whereArray as $field => $value) {
                if ($queryWhere == '') {
                    $queryWhere .= ' WHERE ' . $field . ' = "' . $value . '"';
                } else {
                    $queryWhere .= ' AND ' . $field . ' = "' . $value . '"';
                }
            }
            $query .= $queryWhere;
        }

        $query .= ' LIMIT 1';

        $dbResult = $this->databaseService->runQuery($query);

        if (mysqli_num_rows($dbResult) == 0) {
            return false;
        }

        return $this->convertMysqlResultToSingleModel($dbResult);
    }


    protected function convertMysqlResultToModelArray($mysqlResult)
    {
        $modelName = '\\BT\\Model\\' . ucfirst($this->databaseTableName) . 'Model';

        $modelArray = array();
        while ($mysqlRow = mysqli_fetch_object($mysqlResult)) {
            $newModel = new $modelName('fromMysqlResultRow', $mysqlRow);
            array_push($modelArray, $newModel);
        }
        return $modelArray;
    }


    protected function convertMysqlResultToArray($mysqlResult)
    {
        $returnArray = array();
        while ($mysqlRow = mysqli_fetch_array($mysqlResult)) {
            array_push($returnArray, $mysqlRow);
        }
        return $returnArray;
    }


    protected function convertMysqlResultToSingleModel($mysqlResult)
    {
        $modelName = '\\BT\\Model\\' . ucfirst($this->databaseTableName) . 'Model';
        $mysqlRow = mysqli_fetch_object($mysqlResult);
        $newModel = new $modelName('fromMysqlResultRow', $mysqlRow);
        return $newModel;
    }
}
