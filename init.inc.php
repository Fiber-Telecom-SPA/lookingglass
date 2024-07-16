<?php
  require_once('config.inc.php');
  require_once('utils.inc.php');

  $commands = '';
  $routers  = '';
  $ip      = IP();
  $host    = @gethostbyaddr($ip);
  $visitor = sprintf('<i>Your IP address: %s</i>', htmlentities($ip)) . (($host) && ($host != $ip) ? sprintf(' (%s)', htmlentities($host)) : '');
  $selected = ' selected="selected"';
  foreach (array_keys($config['routers']) as $router) {
    $routers .= sprintf('<option value="%s" %s>%s</option>', $router, $selected, $config['routers'][$router]['desc']);
    $selected = '';
  }
  $selected = ' selected="selected"';
  foreach (array_keys($config['doc']) as $cmd) {
    if (isset($config['doc'][$cmd]['command'])) $commands .= sprintf('<option value="%s" %s>%s</option>', $cmd, $selected, $config['doc'][$cmd]['command']);
    $selected = '';
  }
