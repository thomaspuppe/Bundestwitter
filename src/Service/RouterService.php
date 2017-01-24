<?php
namespace BT\Service;

/**
 * Igniter Router Class
 *
 * This it the Igniter URL Router, the layer of a web application between the
 * URL and the function executed to perform a request. The router determines
 * which function to execute for a given URL.
 *
 * <code>
 * $router = new \Igniter\Router;
 *
 * // Adding a basic route
 * $router->route( '/login', 'login_function' );
 *
 * // Adding a route with a named alphanumeric capture, using the <:var_name> syntax
 * $router->route( '/user/view/<:username>', 'view_username' );
 *
 * // Adding a route with a named numeric capture, using the <#var_name> syntax
 * $router->route( '/user/view/<#user_id>', array( 'UserClass', 'view_user' ) );
 *
 * // Adding a route with a wildcard capture (Including directory separtors), using
 * // the <*var_name> syntax
 * $router->route( '/browse/<*categories>', 'category_browse' );
 *
 * // Adding a wildcard capture (Excludes directory separators), using the
 * // <!var_name> syntax
 * $router->route( '/browse/<!category>', 'browse_category' );
 *
 * // Adding a custom regex capture using the <:var_name|regex> syntax
 * $router->route( '/lookup/zipcode/<:zipcode|[0-9]{5}>', 'zipcode_func' );
 *
 * // Specifying priorities
 * $router->route( '/users/all', 'view_users', 1 ); // Executes first
 * $router->route( '/users/<:status>', 'view_users_by_status', 100 ); // Executes after
 *
 * // Specifying a default callback function if no other route is matched
 * $router->default_route( 'page_404' );
 *
 * // Run the router
 * $router->execute();
 * </code>
 *
 * @since 2.0.0
 */

class RouterService
{

    /**
     * Contains the callback function to execute, retrieved during run()
     *
     * @var String|Array The callback function to execute during dispatch()
     * @since 2.0.1
     * @access protected
     */
    protected $callback = null;

    /**
     * Contains the callback function to execute if none of the given routes can
     * be matched to the current URL.
     *
     * @var String|Array The callback function to execute as a fallback option
     * @since 2.0.0
     * @access protected
     */
    protected $default_route = null;

    /**
     * Contains the last route executed, used when chaining methods calls in
     * the route() function (Such as for put(), post(), and delete()).
     *
     * @var Pointer A pointer to the last route added
     * @since 2.0.0
     * @access protected
     */
    protected $last_route = null;

    /**
     * An array containing the parameters to pass to the callback function,
     * retrieved during run()
     *
     * @var Array An array containing the list of routing rules
     * @since 2.0.1
     * @access protected
     */
    protected $params = array();

    /**
     * An array containing the list of routing rules and their callback
     * functions, as well as their priority and any additional paramters.
     *
     * @var Array An array containing the list of routing rules
     * @since 2.0.0
     * @access protected
     */
    protected $routes = array();

    /**
     * An array containing the list of routing rules before they are parsed
     * into their regex equivalents, used for debugging and test cases
     *
     * @var Array An array containing the list of unaltered routing rules
     * @since 2.0.1
     * @access protected
     */
    protected $routes_original = array();

    /**
     * Whether or not to display errors for things like malformed routes or
     * conflicting routes.
     *
     * @var Boolean Whether or not to display errors
     * @since 2.0.0
     * @access protected
     */
    protected $show_errors = true;

    /**
     * A sanitized version of the URL, excluding the domain and base component
     *
     * @var String A clean URL
     * @since 2.0.0
     * @access protected
     */
    protected $url_clean = '';

    /**
     * The dirty URL, direct from $_SERVER['REQUEST_URI']
     *
     * @var String The unsanitized URL (Full URL)
     * @since 2.0.0
     * @access protected
     */
    protected $url_dirty = '';

    /**
     * Initializes the router by getting the URL and cleaning it
     *
     * @since 2.0.0
     * @access protected
     */
    public function __construct($url = null)
    {
        if ($url == null) {
            // Get the current URL, differents depending on platform/server software
            if (isset($_SERVER['REQUEST_URL']) && !empty($_SERVER['REQUEST_URL'])) {
                $url = $_SERVER['REQUEST_URL'];
            } else {
                $url = $_SERVER['REQUEST_URI'];
            }
        }

        // Store the dirty version of the URL
        $this->url_dirty = $url;

        // Clean the URL, removing the protocol, domain, and base directory if there is one
        $this->url_clean = $this->__getCleanUrl($this->url_dirty);

        $this->defineRoutes();

        return $this;
    }

    /**
     * Enables the display of errors such as malformed URL routing rules or
     * conflicting routing rules. Not recommended for production sites.
     *
     * @since 2.0.0
     * @access public
     */
    public function showErrors()
    {
        $this->show_errors = true;
    }

    /**
     * Disables the display of errors such as malformed URL routing rules or
     * conflicting routing rules. Not recommended for production sites.
     *
     * @since 2.0.0
     * @access public
     */
    public function hideErrors()
    {
        $this->show_errors = false;
    }

    /**
     * If the router cannot match the current URL to any of the given routes,
     * the function passed to this method will be executed instead. This would
     * be useful for displaying a 404 page for example.
     *
     * @since 2.0.0
     * @access public
     *
     * @param string|array $callback The function or class + function to execute if no other routes are matched
     */
    public function defaultRoute($callback)
    {
        $this->default_route = $callback;
    }

    /**
     * Tries to match one of the URL routes to the current URL, otherwise
     * execute the default function and return false.
     *
     * @since 2.0.1
     * @access public
     *
     * @return bool True if a route was matched, false if not
     */
    public function run()
    {
        // Whether or not we have matched the URL to a route
        $matched_route = false;

        // Sort the array by priority
        ksort($this->routes);

        // Loop through each priority level
        foreach ($this->routes as $priority => $routes) {
            // Loop through each route for this priority level
            foreach ($routes as $route => $callback) {
                // Does the routing rule match the current URL?
                if (preg_match($route, $this->url_clean, $matches)) {
                    // A routing rule was matched
                    $matched_route = true;

                    // Parameters to pass to the callback function
                    $params = array('routeUrl' => $this->url_clean);

                    // Get any named parameters from the route
                    foreach ($matches as $key => $match) {
                        if (is_string($key)) {
                            $params[$key] = $match;
                        }
                    }

                    // Store the parameters and callback function to execute later
                    $this->params = $params;
                    $this->callback = $callback;

                    // Return the callback and params, useful for unit testing
                    return array('callback' => $callback, 'params' => $params, 'route' => $route, 'original_route' => $this->routes_original[$priority][$route]);
                }
            }
        }

        // Was a match found or should we execute the default callback?
        if (!$matched_route && $this->default_route !== null) {
            // Store the parameters and callback function to execute later
            $this->params = array('routeUrl' => $this->url_clean);
            $this->callback = $this->default_route;

            return array('params' => $this->params, 'callback' => $this->default_route, 'route' => false, 'original_route' => false);
        }
    }

    /**
     * Calls the appropriate callback function and passes the given parameters
     * given by Router::run()
     *
     * @since 2.0.1
     * @access public
     *
     * @return boolean False if the callback cannot be executed, true otherwise
     */
    public function dispatch()
    {
        if ($this->callback == null || $this->params == null) {
            throw new \Exception('No callback or parameters found, please run $router->run() before $router->dispatch()');

            return false;
        }

        $controllerAndAction = explode('->', $this->callback);
        $controllerClass = '\\BT\\Controller\\' . $controllerAndAction[0];
        $actionMethod = $controllerAndAction[1];

        if (!class_exists($controllerClass)) {
            header("HTTP/1.0 404 Not Found");
            // TODO: 404 HTML_Seite ausliefern
            die('Controller class not found.');
            return false;
        }

        $ctrl = new $controllerClass();

        if (!method_exists($ctrl, $actionMethod)) {
            header("HTTP/1.0 404 Not Found");
            // TODO: 404 HTML_Seite ausliefern
            die('Controller Action method not found.');
            return false;
        }

        $ctrl->{$actionMethod}($this->params);

        return true;
    }

    /**
     * Runs the router matching engine and then calls the dispatcher
     *
     * @uses Router::run()
     * @uses Router::dispatch()
     *
     * @since 2.0.1
     * @access public
     */
    public function execute()
    {
        $this->run();
        $this->dispatch();
    }

    /**
     * Adds a new URL routing rule to the routing table, after converting any of
     * our special tokens into proper regular expressions.
     *
     * @since 2.0.0
     * @access public
     *
     * @param string $route The URL routing rule
     * @param string|array $callback The function or class + function to execute if this route is matched to the current URL
     * @param integer $priority The priority to match this route. Lower priorities are executed before higher priorities
     *
     * @return boolean True if the route was added, false if it was not (If a conflict occured)
     */
    public function route($route, $callback, $priority = 10)
    {

        // Keep the original routing rule for debugging/unit tests
        $original_route = $route;

        // Make sure the route ends in a / since all of the URLs will
        $route = rtrim($route, '/') . '/';

        // Custom capture, format: <:var_name|regex>
        $route = preg_replace('/\<\:(.*?)\|(.*?)\>/', '(?P<\1>\2)', $route);

        // Alphanumeric capture (0-9A-Za-z-_), format: <:var_name>
        $route = preg_replace('/\<\:(.*?)\>/', '(?P<\1>[A-Za-z0-9\-\_ÄÖÜäöüß]+)', $route);

        // Numeric capture (0-9), format: <#var_name>
        $route = preg_replace('/\<\#(.*?)\>/', '(?P<\1>[0-9]+)', $route);

        // Wildcard capture (Anything INCLUDING directory separators), format: <*var_name>
        $route = preg_replace('/\<\*(.*?)\>/', '(?P<\1>.+)', $route);

        // Wildcard capture (Anything EXCLUDING directory separators), format: <!var_name>
        $route = preg_replace('/\<\!(.*?)\>/', '(?P<\1>[^\/]+)', $route);

        // Add the regular expression syntax to make sure we do a full match or no match
        $route = '#^' . $route . '$#';

        // Does this URL routing rule already exist in the routing table?
        if (isset($this->routes[$priority][$route])) {
            // Trigger a new error and exception if errors are on
            if ($this->showErrors) {
                throw new \Exception('The URI "' . htmlspecialchars($route) . '" already exists in the router table');
            }

            return false;
        }

        // Add the route to our routing array
        $this->routes[$priority][$route] = $callback;
        $this->routes_original[$priority][$route] = $original_route;

        return true;
    }

    /**
     * Retrieves the part of the URL after the base (Calculated from the location
     * of the main application file, such as index.php), excluding the query
     * string. Adds a trailing slash.
     *
     * <code>
     * http://localhost/projects/test/users///view/1 would return the following,
     * assuming that /test/ was the base directory
     *
     * /users/view/1/
     * </code>
     *
     * @since 2.0.0
     * @access protected
     *
     * @param string $url The "dirty" url, not including the domain (path only)
     *
     * @return string The cleaned URL
     */
    protected function __getCleanUrl($url)
    {
        // The request url might be /project/index.php, this will remove the /project part
        $scriptDirName = dirname($_SERVER['SCRIPT_NAME']);
        if ($scriptDirName != '/') {
            $url = str_replace($scriptDirName, '', $url);
        }

        // Remove the query string if there is one
        $query_string = strpos($url, '?');

        if ($query_string !== false) {
            $url = substr($url, 0, $query_string);
        }

        // If the URL looks like http://localhost/index.php/path/to/folder remove /index.php
        if (substr($url, 1, strlen(basename($_SERVER['SCRIPT_NAME']))) == basename($_SERVER['SCRIPT_NAME'])) {
            $url = substr($url, strlen(basename($_SERVER['SCRIPT_NAME'])) + 1);
        }

        // Make sure the URI ends in a /
        $url = rtrim($url, '/') . '/';

        // Make sure the URI does not start with a / (because of Linux servers)
        $url = ltrim($url, '/');

        // Replace multiple slashes in a url, such as /my//dir/url
        $url = preg_replace('/\/+/', '/', $url);

        // make sure that Umlaute work
        $url = rawurldecode($url);

        // in case we are on the home page, set /
        if ($url == '') {
            $url = '/';
        }

        return $url;
    }


    private function defineRoutes()
    {
        $this->route('admin/party', 'AdminPartyController->indexAction');

        $this->route('admin/status/phpinfo', 'AdminStatusController->phpinfoAction');

        // Specifying a default callback function if no other route is matched
        $this->defaultRoute('PageController->error404Action');

        /*
        // Adding a basic route
        $router->route( '/login', 'login_function' );

        // Adding a route with a named alphanumeric capture, using the  syntax
        $router->route( '/user/view/<:username>', 'view_username' );

        // Adding a route with a named numeric capture, using the  syntax
        $router->route( '/user/view/<#user_id>', array( 'UserClass', 'view_user' ) );

        // Adding a route with a wildcard capture (Including directory separtors), using
        // the  syntax
        $router->route( '/browse/<*categories>', 'category_browse' );

        // Adding a wildcard capture (Excludes directory separators), using the
        //  syntax
        $router->route( '/browse/<!category>', 'browse_category' );

        // Adding a custom regex capture using the  syntax
        $router->route( '/lookup/zipcode/<:zipcode|[0-9]{5}>', 'zipcode_func' );

        // Specifying priorities
        $router->route( '/users/all', 'view_users', 1 ); // Executes first
        $router->route( '/users/<:status>', 'view_users_by_status', 100 ); // Executes after

        // Specifying a default callback function if no other route is matched
        $router->default_route( 'page_404' );
        */

    }
}
