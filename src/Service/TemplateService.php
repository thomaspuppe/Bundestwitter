<?php
namespace BT\Service;

class TemplateService
{

    private $template;
    private $vars = array();

    public function __construct($template)
    {
        //TODO: check if the template exists. If not, ...?
        $this->template = str_replace('/', DIRECTORY_SEPARATOR, $template);
    }

    public function assign($varArray)
    {
        foreach ($varArray as $name => $value) {
            $this->vars[$name] = $value;
        }
    }

    public function render()
    {
        ob_start();
        $filename = ROOT . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR . 'Templates' . DIRECTORY_SEPARATOR . $this->template . '.php';
        if (!file_exists($filename)) {
            // TODO: handle error
            die('ERROR: Template "' . $this->template . '" does not exist.');
        }
        include_once($filename);
        return ob_get_clean();
    }


    public function getPartialPath($partial)
    {
        $filename = ROOT . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR . 'Partials' . DIRECTORY_SEPARATOR . $partial . '.php';
        return $filename;
    }


    public function renderTweet($statusId, $layout = 'small')
    {
        $filename = ROOT . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR . 'Partials' . DIRECTORY_SEPARATOR .'Tweet' . DIRECTORY_SEPARATOR . $layout . '.php';

        $tweetRepository = new \BT\Model\TweetRepository();
        $tweet = $tweetRepository->findOneBy(array('twitter_id' => $statusId));

        $this->vars['tweet'] = $tweet;

        include($filename);
    }


// im Controller werden via $view->assign($property, $value) Werte gesetzt.
// im Template werden via $this->property die Werte eingebunden.
    public function __get($property)
    {
        if (isset($this->vars[$property])) {
            return $this->vars[$property];
        }
        return null;
    }
}
