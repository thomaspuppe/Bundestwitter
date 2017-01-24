<?php
// define the current App, and the root folder
define('APP', basename(__DIR__));
define('ROOT', __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);

$rootPathinfo = pathinfo($_SERVER['PHP_SELF']);
define('BASE', 'http://' . $_SERVER['HTTP_HOST'] . $rootPathinfo['dirname'] . '/');

// require the configuration file
require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . APP . '.php');

// TODO: solve this more elegant!
if ((ENVIRONMENT=='DEV' || isset($_COOKIE['btadmin'])) && strpos($_SERVER['REQUEST_URI'], '/api')===false && substr($_SERVER['REQUEST_URI'], 0, 6) != '/tweet') {
    $GLOBALS['PROFILING'] = array();
}

if (isset($GLOBALS['PROFILING'])) {
    $GLOBALS['PROFILING'][] = array('START', microtime(true));
}

// require the "Kernel"
require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Kernel.php');


$routerService = new \BT\Service\RouterService();
// Routes are defined inside the RouterService Class.
$routerService->execute();


// TODO: Move to Controller or Service!
if (isset($GLOBALS['PROFILING'])) {
    $GLOBALS['PROFILING'][] = array('END', microtime(true));

    #echo "<!-- ";
    echo '<div id="debugPanel" style="background: #FBFF98; font-size: 0.8em; left: 0; position:absolute; top: 0; z-index:9999;"><pre>';
    echo '<a href="#" onClick="document.getElementById(\'debugPanel\').style.display = \'none\'; return false;">REMOVE</a>';

    $firstRowTime = null;
    $latestRowTime = null;
    foreach ($GLOBALS['PROFILING'] as $profileRow) {
        $currentRowOperation = $profileRow[0];
        $currentRowTime = $profileRow[1];

        if ($firstRowTime==null) {
            $firstRowTime = $currentRowTime;
            $latestRowTime = $currentRowTime;
        }

        echo "\n" . number_format(($currentRowTime-$latestRowTime), 5) . "s | " . number_format(($currentRowTime-$firstRowTime), 5) . "s | " . $currentRowOperation;
        $latestRowTime = $currentRowTime;
    }

    echo "\n\n" . $GLOBALS['QUERY_COUNTER'] . " Queries performed.";

    $databaseService = \BT\Service\DatabaseService::getInstance();
    $profilingResult = mysqli_query($databaseService->getConnection(), 'SHOW PROFILES');
    if ($profilingResult) {
        while ($profilingRow = mysqli_fetch_array($profilingResult)) {
            $currentQuery = $profilingRow['Query'];
            $cleanCurrentQuery = str_replace("\n", "", htmlspecialchars($currentQuery));
            echo "\n" . number_format(($profilingRow['Duration']), 5) . "s | " . '<span title="' . $cleanCurrentQuery . '">' . substr($cleanCurrentQuery, 0, 32) . '</span>';
        }
    }

    #echo "\n -->";
    echo '</pre></div>';
}
