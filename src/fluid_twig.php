<?php
require __DIR__ . '/../vendor/autoload.php';

$maxCount = 20000;

$testConfig = [
    'fluid' => [
        [
            'template' => 'Index.html',
            'cache' => true
        ],
        [
            'template' => 'Index.html',
            'cache' => false
        ],
        [
            'template' => 'Advanced.html',
            'cache' => true
        ]
    ],
    'twig' => [
        [
            'template' => 'index.html',
            'cache' => true
        ],
        [
            'template' => 'index.html',
            'cache' => false
        ],
        [
            'template' => 'advanced.html',
            'cache' => true
        ]
    ],
];

for($i=0;$i<10;$i++) {
    $list[] = new MyFoo('Test1', 'Test1'.$i, 'Num1'.$i);
}

foreach ($testConfig as $engineKey =>  $engine) {
    foreach ($engine as $engineConf) {


        PHP_Timer::start();
        echo "starting ".$engineConf['template']." rendering with ".$engineKey." (".$maxCount." times, cache ".(int)$engineConf['cache'].")" . PHP_EOL;
        $f = '';

        if ($engineKey == 'fluid') {
            $fluidView = initFluid($engineConf['template'], $engineConf['cache']);
        } elseif ($engineKey == 'twig') {
            $twigTemplate = initTwig($engineConf['template'], $engineConf['cache']);
        }

        for($i=0;$i < $maxCount;$i++) {
            if ($engineKey == 'fluid') {
                $f .= renderBasicFluidVar($fluidView).PHP_EOL;
            } elseif ($engineKey == 'twig') {
                $f .= renderTwig($twigTemplate).PHP_EOL;
            }

        }

        $time = PHP_Timer::stop();
        echo PHP_EOL;
        echo "finished after:" . PHP_EOL;
        echo  PHP_Timer::secondsToTimeString($time);
        echo PHP_EOL . '------------------------------------' . PHP_EOL;
    }
}

// set up paths object with arrays of paths with files

function initFluid($template, $cached) {
    $FLUID_CACHE_DIRECTORY = !isset($FLUID_CACHE_DIRECTORY) ? __DIR__ . '/../cache/' : $FLUID_CACHE_DIRECTORY;
    // pass the constructed TemplatePaths instance to the View
    $view = new \TYPO3Fluid\Fluid\View\TemplateView();
    $view->getTemplatePaths()->setTemplateRootPaths(array('src/Templates/Fluid/Templates/'));
    $view->getTemplatePaths()->setLayoutRootPaths(array('src/Templates/Fluid/Layouts'));
    $view->getTemplatePaths()->setPartialRootPaths(array('src/Templates/Fluid/Partials'));
    $view->getTemplatePaths()->setTemplatePathAndFilename('src/Templates/Fluid/Templates/'. $template);

    if ($cached)
        $view->setCache(new \TYPO3Fluid\Fluid\Core\Cache\SimpleFileCache($FLUID_CACHE_DIRECTORY));
    return $view;
}

function renderBasicFluidVar(\TYPO3Fluid\Fluid\View\TemplateView $view) {

    global $list;

    $view->assign('foobar', 'MyStuff');
    $view->assign('list', $list);
    return $view->render();
}

/**
 * @param $templateFile
 * @param $cache
 * @return Twig_TemplateWrapper
 */
function initTwig($templateFile, $cache) {
    $opt = [];
    if ($cache) {
        $opt = array(
            'cache' => __DIR__ . '/../cache/twig',
        );
    }
    $loader = new Twig_Loader_Filesystem('src/Templates/Twig');
    $twig = new Twig_Environment($loader, $opt);
    return $twig->load($templateFile);
}

function renderTwig(Twig_TemplateWrapper $template) {
    global $list;

    return $template->render(['foobar' => 'MyStuff', 'list' => $list]);
}

class MyFoo {
    private $name;
    private $title;
    private $num;

    public function __construct($name, $title, $num)
    {
        $this->name = $name;
        $this->title = $title;
        $this->num = $num;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getNum()
    {
        return $this->num;
    }

    /**
     * @param mixed $num
     */
    public function setNum($num)
    {
        $this->num = $num;
    }


}