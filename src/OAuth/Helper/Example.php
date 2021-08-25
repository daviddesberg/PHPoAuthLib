<?php

namespace OAuth\Helper;

use Symfony\Component\Finder\Finder;

/**
 * Helper class to be used in sample code.
 */
class Example
{
    /**
     * @var Finder
     */
    private $finder;

    private $title;

    public function __construct()
    {
        $this->finder = new Finder();
    }

    public function isCli(): bool
    {
        return PHP_SAPI === 'cli';
    }

    public function getFinder(): Finder
    {
        $this->finder->in(__DIR__ . '/../../../examples/provider/');

        return $this->finder;
    }

    public function getHeader(): string
    {
        $title = $this->title;

        return <<<HTML
<html>
<head>
    <title>$title</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="stylesheet" href="/bootstrap/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="/bootstrap/css/font-awesome.min.css"/>
    <link rel="stylesheet" href="/bootstrap/css/phpspreadsheet.css"/>
    <script src="/bootstrap/js/jquery.min.js"></script>
    <script src="/bootstrap/js/bootstrap.min.js"></script>
</head>
<body>
    <div class="container">
        <div class="navbar navbar-default" role="navigation">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="/">PHPoAuthLib</a>
                </div>
                <div class="navbar-collapse collapse">
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="https://github.com/Lusitanian/PHPoAuthLib"><i class="fa fa-github fa-lg" title="GitHub"></i>&nbsp;</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <h1>$title</h1>
HTML;
    }

    public function getFooter()
    {
        return <<<HTML
    </body>
    </html>
HTML;
    }

    public function getForm(): string
    {
        return  <<<HTML
            <form>
              <div class="form-group">
                <label for="exampleInputEmail1">key</label>
                <input class="form-control" name="key" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="key">
              </div>
              <div class="form-group">
                <label for="exampleInputPassword1">secret</label>
                <input name="secret" class="form-control" id="exampleInputPassword1" placeholder="secret">
              </div>
              <button type="submit" class="btn btn-primary">Submit</button>
            </form>
HTML;
    }

    public function getContent(): string
    {
        $response = $this->getHeader();
        $response .= $this->getForm();
        $response .= $this->getFooter();

        return $response;
    }

    public function isShowLink(): bool
    {
        return true;
    }

    public function getCurrentUrl(): string
    {
        return  'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?oauth=redirect&key=' . urldecode($_GET['key']) . '&secret=' . urldecode($_GET['secret']);
    }

    public function getErrorMessage($exception): void
    {
        echo '<div class="alert alert-danger">' . $exception->getMessage() . '</div>';
        echo '<pre>';
        print_r($exception);
        echo '</pre>';
    }

    public function setTitle(string $title): void
    {
        $this->title = 'PHPoAuthLib - ' . $title;
    }
}
