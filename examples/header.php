<?php
error_reporting(E_ALL);
// Return to the caller script when runs by CLI
if ($helper->isCli()) {
    return;
}
?>
<html>
<head>
    <title></title>
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
