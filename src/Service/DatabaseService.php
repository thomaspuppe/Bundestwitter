<?php
namespace BT\Service;

$GLOBALS['QUERY_COUNTER'] = 0;

class DatabaseService
{

    public static $isSingleton = TRUE;
    private static $instance;
    private static $params;
    private static $dbConnection = null;

#****f* library/databaseHandler/__construct()
# FUNCTION
# leerer Konstruktor, weil Singleton.
#***
    private function __construct()
    {
        // SINGLETON OBJECT
    }

#****f* library/databaseHandler/__clone()
# FUNCTION
# leere Clone-Funktion, weil Singleton.
#***
    private function __clone()
    {
        // SINGLETON OBJECT
    }

#****f* library/databaseHandler/getInstance()
# FUNCTION
# Singleton-Konstruktor. Erzeugt eine Instanz und gibt diese bei jedem weiteren
# Aufruf zurueck.
#
# Stellt au�erdem die Verbindung zur Datenbank her
#
# SYNOPSIS
# $myErrorHandler = errorHandler::getInstance();
#
# RESULT
# [errorHandler Object] Gibt die einzige Instanz des errorHandler zurueck.
#***
    public static function getInstance()
    {

        if (!isset(self::$instance)) {
            $class = __CLASS__;
            self::$instance = new $class;
            self::$params = $GLOBALS['CONFIG']['DATABASE'];

            self::connect();
        }

        return self::$instance;
    }

#****f* library/databaseHandler/connect()
# FUNCTION
# Stellt die Verbindung zur Datenbank her.
#
# SYNOPSIS
# self::connect(); (private in databaseHandler)
#***
    private static function connect()
    {

        // TODO: Exception handling für gute Meldung in der Anwendung
        self::$dbConnection = mysqli_connect(self::$params['host'], self::$params['user'], self::$params['password']);
        mysqli_select_db(self::$dbConnection, self::$params['name']);

        if (!self::$dbConnection) {
            //TODO:error_log
            die('DB: Connect @' . self::$params['host'] . '|' . mysqli_error(self::$dbConnection));
        }

        mysqli_query(self::$dbConnection, "SET NAMES 'utf8'");
        mysqli_query(self::$dbConnection, "SET CHARACTER SET 'utf8'");


        if (!isset($GLOBALS['isJsonResponse']) && isset($GLOBALS['PROFILING'])) {
            mysqli_query(self::$dbConnection, 'SET profiling_history_size=100;');
            mysqli_query(self::$dbConnection, 'set profiling=1');
        }

    }

#****f* library/errorHandler/runQuery()
# FUNCTION
# F�hrt ungepr�ft(!) eine Datenbankabfrage durch
#
# SYNOPSIS
# runQuery($query))
#***
    public function runQuery($query)
    {
        $GLOBALS['QUERY_COUNTER']++;

        // TODO: SECURITY: Aufruf, Sicherheitscheck und Fehlerbehandlung in databaseHandler kapseln.


        // $query = mysql_real_escape_string ($query, self::$dbConnection);

        $result = mysqli_query(self::$dbConnection, $query);

        if (!$result) {

            $requestUri = '';
            if (isset($_SERVER['REQUEST_URI'])) {
                $requestUri = $_SERVER['REQUEST_URI'];
            }

            $errorMessage = 'Error@DatabaseService (' . $requestUri . ') ' . $query . ' | ' . mysqli_error(self::$dbConnection);

            if (ENV=='DEV' || isset($_COOKIE['btadmin'])) {
                die($errorMessage);
            } else {
                // Send Error to Monitoring System
                if (isset($GLOBALS['ERRORHANDLER'])) {
                    $GLOBALS['ERRORHANDLER']->captureMessage($errorMessage);
                }

                die('database error');
            }

        }

        return $result;
    }


    public function getConnection()
    {
        return self::$dbConnection;
    }

}
