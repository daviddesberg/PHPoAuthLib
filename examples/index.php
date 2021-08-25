<?php
include_once __DIR__ . '/bootstrap.php';
use OAuth\Helper\Example;

$helper = new Example();
$requirements = [
    'PHP 7.2.0' => version_compare(PHP_VERSION, '7.2.0', '>='),
    'PHP extension dom' => extension_loaded('dom'),
    'PHP extension curl' => extension_loaded('curl'),
    'PHP extension json' => extension_loaded('json'),
];
require_once 'header.php';

if (!$helper->isCli()) {
    ?>
    <div class="jumbotron">
        <p>Welcome to OAuthLib</p>
        <p>&nbsp;</p>
        <p>
            <a class="btn btn-lg btn-primary" href="https://github.com/Lusitanian/PHPoAuthLib" role="button">
                <i class="fa fa-github fa-lg" title="GitHub"></i> Fork us on Github!</a>
        </p>
    </div>
    <?php
    echo '<h3>Requirement check</h3>';
    echo '<ul>';
    foreach ($requirements as $label => $result) {
        $status = $result ? 'passed' : 'failed';
        echo "<li>{$label} ... <span class='{$status}'>{$status}</span></li>";
    }
    echo '</ul>'; ?>
    <h2>Select provider for check</h2>
    <ul>
        <?php
        /** @var SplFileInfo $files */
        foreach ($helper->getFinder() as $files) {
            $basename = $files->getBasename();
            echo '<li><a href="/provider/' . $basename . '">' . $basename . '</a></li>';
        } ?>
    </ul>

    <?php
} else {
            echo 'Requirement check:' . PHP_EOL;
            foreach ($requirements as $label => $result) {
                $status = $result ? '32m passed' : '31m failed';
                echo "{$label} ... \033[{$status}\033[0m" . PHP_EOL;
            }
        }
?>
