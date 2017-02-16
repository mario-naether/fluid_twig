<?php
require __DIR__ . '/../vendor/autoload.php';

$maxCount = 100000;

$testConfig = [
    'fluid' => [
        [
            'template' => 'Index.html',
            'cache' => true
        ],
        [
            'template' => 'Index.html',
            'cache' => false
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
        ]
    ],
];

foreach ($testConfig as $engineKey =>  $engine) {
    foreach ($engine as $engineConf) {


        PHP_Timer::start();
        echo "starting basic variable rendering with ".$engineKey." (".$maxCount." times, cache ".(int)$engineConf['cache'].")" . PHP_EOL;
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

    $view->assign('foobar', 'MyStuff');
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
    return $template->render(['foobar' => 'MyStuff']);
}