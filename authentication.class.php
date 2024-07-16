<?php

################################################################################
### Authentication

abstract class Authentication {
  protected $config;
  protected $debug;

  protected abstract function check_config();
  public abstract function connect();
  public abstract function disconnect();
  public abstract function send_command($command);

  public function __destruct() { $this->disconnect(); }
  public function __construct($config, $debug) {
    $this->config = $config;
    $this->debug = $debug;
    $this->check_config();
  }

  public static final function instance($config, $debug) {
    switch ($config['auth']) {
      case 'ssh-password':
      case 'ssh-key':
          $ret = new SSH($config, $debug);
        break;
      case 'telnet':
          $ret = new Telnet($config, $debug);
        break;
      default:
          print('Unknown authentication mechanism "'.$config['auth'].'"."');
          $ret = null;
    }
    return $ret;
  }
}


################################################################################
### Telnet

final class Telnet extends Authentication {
  private $port;

  public function __construct($config, $debug) {
    parent::__construct($config, $debug);
    $this->port = isset($this->config['port']) ? (int) $this->config['port'] : 23;
  }

  protected function check_config() {
    if (!isset($this->config['user']) || !isset($this->config['pass'])) throw new Exception('Router authentication configuration incomplete.');
  }

  public function connect() {
    $this->connection = fsockopen($this->config['host'], $this->port, $errno, $errstr, $this->config['timeout']);
    if (!$this->connection) throw new Exception('Cannot connect to router (code '.$errno.'['.$errstr.']).');
    fputs($this->connection, $this->config['user']."\r\n");
    fputs($this->connection, $this->config['pass']."\r\n");
  }

  public function send_command($command) {
    $this->connect();
    fputs($this->connection, $command."\r\n");
    $data = '';
    while(substr($data, -1) != '#' && substr($data, -1) != '>') {
      $data .= fread($this->connection, 4096);
      $data = rtrim($data, " ");
    }
    $this->disconnect();
    return $data;
  }

  public function disconnect() {
    if ($this->connection) {
      fclose($this->connection);
      $this->connection = null;
    }
  }
}


##########################################################################
### ssh

require_once('libs/ClassLoader.php');

$loader = new \Composer\Autoload\ClassLoader();
$loader->addPsr4('phpseclib\\', 'libs/phpseclib-2.0.23');
$loader->register();

use phpseclib\Crypt\RSA;
use phpseclib\Net\SSH2;

require_once('authentication.class.php');
require_once('utils.inc.php');

final class SSH extends Authentication {
  private $port;
  public function __construct($config, $debug) {
    parent::__construct($config, $debug);
    $this->port = isset($this->config['port']) ? (int) $this->config['port'] : 22;
    if ($this->debug) define('NET_SSH2_LOGGING', 2);// NET_SSH2_LOG_COMPLEX
  }

  protected function check_config() {
    if ($this->config['auth'] == 'ssh-password') {
      if (!isset($this->config['user']) || !isset($this->config['pass'])) throw new Exception('Router authentication configuration incomplete.');
    }
    if ($this->config['auth'] == 'ssh-key') {
      if (!isset($this->config['user']) || !isset($this->config['private_key'])) throw new Exception('Router authentication configuration incomplete.');
      if (isset($this->config['private_key']) && !is_readable($this->config['private_key'])) throw new Exception('SSH key for authentication is not readable.');
    }
  }

  public function connect() {
    $this->connection = new SSH2($this->config['host'], $this->port);
    $this->connection->setTimeout($this->config['timeout']);
    $success = false;
    if ($this->config['auth'] == 'ssh-password') {
      $success = $this->connection->login($this->config['user'], $this->config['pass']);
    } else if ($this->config['auth'] == 'ssh-key') {
      $key = new RSA();
      if (isset($this->config['pass'])) $key->setPassword($this->config['pass']);
      $key->loadKey(file_get_contents($this->config['private_key']));
      $success = $this->connection->login($this->config['user'], $key);
    } else {
      throw new Exception('Unknown type of connection.');
    }
    if ($this->debug) log_to_file($this->connection->getLog());
    if (!$success) throw new Exception('Cannot connect to router.');
  }

  public function send_command($command) {
    $this->connect();
    $data = $this->connection->exec($command);
    if ($this->debug) log_to_file($this->connection->getLog());
    $this->disconnect();
    return $data;
  }

  public function disconnect() {
    if (($this->connection != null) && $this->connection->isConnected()) {
      $this->connection->disconnect();
      if ($this->debug) log_to_file($this->connection->getLog());
      $this->connection = null;
    }
  }
}
