<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET');
header('Access-Control-Max-Age: 300');

require_once('config.inc.php');
require_once('utils.inc.php');
require_once('router.class.php');

$ip = IP();

if (!empty($_POST['dontlook'])) { log_to_file('Spam detected from '.$ip.'.'); die('Spam detected'); }
if (isset($_POST['doc']) && !empty($_POST['doc'])) {
  $query = htmlspecialchars($_POST['doc']);
  print(json_encode($config['doc'][$query]));
}

if (isset($_POST['query']) && !empty($_POST['query']) &&
    isset($_POST['routers']) && !empty($_POST['routers']) /*&&
    isset($_POST['parameter']) && !empty($_POST['parameter'])*/) {
  $query = trim($_POST['query']);
  $hostname = trim($_POST['routers']);
  $parameter = trim($_POST['parameter']);

  // Check if query is disabled
  if (!isset($config['doc'][$query]['command'])) {
    $error = 'This query has been disabled in the configuration.';
    print(json_encode(array('error' => $error)));
    return;
  }

  $router = Router::instance($hostname, $ip);

  try { $output = $router->send_command($query, $parameter); } catch (Exception $e) { $error = $e->getMessage(); }

  print(json_encode((isset($output)?['result' => $output]:['error' => $error])));
}
