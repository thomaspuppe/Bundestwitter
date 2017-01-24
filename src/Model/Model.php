<?php
namespace BT\Model;

class Model
{
    protected $databaseService;


    public function __construct($mode = null, $params = null)
    {
        $this->databaseService = \BT\Service\DatabaseService::getInstance();

        if ($mode == 'fromMysqlResultRow') {
            $this->fillFromMysqlResultRow($params);
            return $this;
        }

        if ($mode == 'fromDatabaseField') {
            return $this->fetchFromDatabase($params);
        }


    }


    // Magic Getter for $this->property. Wird überschrieben, wenn echte Getter existieren.
    public function __get($property)
    {

        if (method_exists($this, 'get' . ucfirst($property))) {
            $methodName = 'get' . ucfirst($property);
            return $this->$methodName();
        }

        if (property_exists($this, $property)) {
            return $this->$property;
        }
        return null;
    }


    protected function fillFromMysqlResultRow($mysqlResultRow)
    {
        foreach ($mysqlResultRow as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }


    protected function fetchFromDatabase($params)
    {
        $dbResult = $this->databaseService->runQuery('SELECT * FROM ' . $this->databaseTableName . ' WHERE ' . $params['field'] . ' = "' . $params['value'] . '" LIMIT 1');

        if (mysqli_num_rows($dbResult)==1) {
            $this->fillFromMysqlResultRow(mysqli_fetch_object($dbResult));
            return $this;
        } else {
            return false;
        }

    }


}
