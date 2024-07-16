<!DOCTYPE html>
<?php
  require_once('init.inc.php');
?>
<html lang="en">
<head>
    <title>Looking Glass - Fiber Telecom</title>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="//cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css" rel="stylesheet" />
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
    <link href="style.css" rel="stylesheet" />
    <script src="script.js?1598550099"></script>
</head>
<body>
    <div class="bg-light">
        <div class="content w-100 p-2">
            <div class="bg-dark p-2 justify-content-md-center asn-database text-center">
                <?= $visitor; ?>
            </div>
        </div>
        <form role="form" action="run.php" method="post">
            <div class="content w-100 px-2">
                <div class="container_1200">
                    <div class="row">
                        <div class="col-12 text-center">
                            <div class="loading">
                                <div class="progress">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
                                </div>
                            </div>
                            <div class="alert alert-danger alert-dismissable" id="error">
                                <button type="button" class="close" aria-hidden="true">&times;</button>
                                <strong>Error!</strong>&nbsp;<span id="error-text"></span>
                            </div>
                        </div>
                        <div class="col-12 content row" id="command_options">
                            <input type="text" class="d-none" name="dontlook" placeholder="Don\'t look at me!" />
                            <div class="col-12 col-sm-6 col-md-4">
                                <label for="routers" class="text-nowrap">Router to use</label>
                                <div class="input-group">
                                    <select size="1" class="form-control custom-select" name="routers" id="routers"><?= $routers; ?></select>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-3">
                                <label for="query" class="text-nowrap">Command to issue</label>
                                <select size="1" class="form-control custom-select" name="query" id="query"><?= $commands; ?></select>
                            </div>
                            <div class="col-12 col-sm-6 col-md-3">
                                <label for="input-param" class="text-nowrap">Parameter</label>
                                <div class="input-group">
                                    <input class="form-control" name="parameter" id="input-param" autofocus />
                                    <div class="input-group-append"><button type="button" class="btn btn-primary text-nowrap" data-toggle="modal" data-target="#help"><i class="fas fa-question"></i> Help</button></div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-2">
                                <label for="send">&nbsp;</label>
                                <div class="input-group"><button id="send" type="submit" class="btn btn-success text-nowrap">Query <i class="fas fa-play"></i></button></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="result">
                                <div id="output"><pre class="pre-scrollable"></pre></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div id="help" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Help</h5><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
                                        <div class="modal-body">
                                            <h4>Command <span class="badge badge-dark"><small id="command-reminder"></small></span></h4>
                                            <p id="description-help"></p>
                                            <h4>Parameter</h4>
                                            <p id="parameter-help"></p>
                                        </div>
                                        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </form>
        </div>
</body>
</html>
