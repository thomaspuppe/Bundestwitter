<?php
namespace BT\Model;

class Repository
{
    protected $databaseService;
    protected $databaseTableName;


    public function __construct()
    {
        $this->databaseService = \BT\Service\DatabaseService::getInstance();
        #$this->databaseCacheService = \BT\Service\DatabaseCacheService::getInstance();
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
