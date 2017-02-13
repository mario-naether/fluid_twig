<?php
require __DIR__ . '/../vendor/autoload.php';

$maxCount = 10000;

// set up paths object with arrays of paths with files

PHP_Timer::start();
echo "starting basic variable rendering with Fluid (".$maxCount." times, cache enable)" . PHP_EOL;
$f = '';
for($i=0;$i < $maxCount;$i++) {
    $f .= renderBasicFluidVar('Index.html', true).PHP_EOL;
}


$time = PHP_Timer::stop();
echo PHP_EOL.PHP_EOL;
echo "finished after:" . PHP_EOL;
echo  PHP_Timer::secondsToTimeString($time);
echo PHP_EOL.PHP_EOL;

PHP_Timer::start();
echo "starting basic variable rendering with Fluid (".$maxCount." times, cache disable)" . PHP_EOL;
$f = '';
for($i=0;$i < $maxCount;$i++) {
    $f .= renderBasicFluidVar('Index.html', false).PHP_EOL;
}


$time = PHP_Timer::stop();
echo PHP_EOL.PHP_EOL;
echo "finished after:" . PHP_EOL;
echo  PHP_Timer::secondsToTimeString($time);


function renderBasicFluidVar($template, $cached) {

    $FLUID_CACHE_DIRECTORY = !isset($FLUID_CACHE_DIRECTORY) ? __DIR__ . '/../cache/' : $FLUID_CACHE_DIRECTORY;
    // pass the constructed TemplatePaths instance to the View
    $view = new \TYPO3Fluid\Fluid\View\TemplateView();
    $view->getTemplatePaths()->setTemplateRootPaths(array('src/Templates/Fluid/Templates/'));
    $view->getTemplatePaths()->setLayoutRootPaths(array('src/Templates/Fluid/Layouts'));
    $view->getTemplatePaths()->setPartialRootPaths(array('src/Templates/Fluid/Partials'));
    $view->getTemplatePaths()->setTemplatePathAndFilename('src/Templates/Fluid/Templates/'. $template);

    if ($cached)
        $view->setCache(new \TYPO3Fluid\Fluid\Core\Cache\SimpleFileCache($FLUID_CACHE_DIRECTORY));

    $view->assign('foobar', 'MyStuff');
    return $view->render();
}