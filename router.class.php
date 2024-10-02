<?php
require_once('config.inc.php');
require_once('utils.inc.php');
require_once('juniper.router.php');
require_once('authentication.class.php');

abstract class Router {
  protected $global_config;
  protected $config;
  protected $id;
  protected $requester;

  public function __construct($global_config, $config, $id, $requester) {
    $this->id = $id;
    $this->config = $config;
    $this->requester = $requester;
    $this->global_config = $global_config;
    $this->config['timeout'] = 30;
  }

  private function sanitize_output($output) {
    $filtered = '';
    if (count($this->global_config['filters']['output']) < 1) return preg_replace('/(?:\n|\r\n|\r)$/D', '', $output);
    foreach (preg_split("/((\r?\n)|(\r\n?))/", $output) as $line) {
      $valid = true;
      foreach ($this->global_config['filters']['output'] as $filter) {
        if (is_array($filter)) $line = preg_replace($filter[0], $filter[1], $line);
        elseif (!$valid || (preg_match($filter, $line) === 1)) { $valid = false; break; }
      }
      if ($valid) $filtered .= $line."\n";
    }
    return preg_replace('/(?:\n|\r\n|\r)$/D', '', $filtered);
  }

  protected function format_output($command, $output)              { return sprintf('%s<pre class="pre-scrollable">%s</pre>',($this->global_config['output']['show_command']?sprintf('<p><kbd>%s</kdb></p>',$command):''),$output);  }
  protected function has_source_interface_id()                     { return isset($this->config['source-interface-id']); }
  protected function get_source_interface_id($ip_version = 'ipv6') { return ($this->has_source_interface_id()?(is_array($this->config['source-interface-id'])?$this->config['source-interface-id'][$ip_version]:$this->config['source-interface-id']):null); }
  protected function has_routing_table_name()                      { return isset($this->config['routing-table']); }
  protected function get_routing_table_name($ip_version = 'ipv6')  { return ($this->has_routing_table_name()?(is_array($this->config['routing-table'])?$this->config['routing-table'][$ip_version]:$this->config['routing-table']):null); }
  public function get_config()                                     { return $this->config; }

  private function commands($command, $parameter) {
    switch ($command) {
      case 'bgp':
          if (!is_valid_ip_prefix($parameter)) throw new Exception('The parameter is not a valid IP address/prefix.');
          $ret = $this->bgp($parameter);
        break;
      case 'bgp_terse':
          if (!is_valid_ip_prefix($parameter)) throw new Exception('The parameter is not a valid IP address/prefix.');
          $ret = $this->bgp_terse($parameter);
        break;
      case 'bgp_detail':
          if (!is_valid_ip_prefix($parameter)) throw new Exception('The parameter is not a valid IP address/prefix.');
          $ret = $this->bgp_detail($parameter);
        break;
      case 'bgp_summary':
          $ret = $this->bgp_summary();
        break;
      case 'as-path-regex':
          if (!match_aspath_regexp($parameter)) throw new Exception('The parameter is not an AS-Path regular expression.');
          $ret = $this->aspath_regexp($parameter);
        break;
      case 'as':
          if (!match_as($parameter)) throw new Exception('The parameter is not a valid AS number.');
          $ret = $this->aspath($parameter);
        break;
      case 'ping':
          if (!is_valid_destination($parameter)) throw new Exception('The parameter is not a valid IP address or a HOSTNAME.');
          $ret = $this->ping($parameter);
        break;
      case 'icmp_traceroute':
          if (!is_valid_destination($parameter)) throw new Exception('The parameter is not a valid IP address or a HOSTNAME.');
          $ret = $this->icmp_traceroute($parameter);
        break;
      case 'udp_traceroute':
          if (!is_valid_destination($parameter)) throw new Exception('The parameter is not a valid IP address or a HOSTNAME.');
          $ret = $this->udp_traceroute($parameter);
        break;
      default:
          $ret = null;
          throw new Exception('Command not supported.');
    }
    return $ret;
  }

  public function send_command($command, $parameter='') {
    switch ($command) {
      case 'rpki_validate':
          $data = ''; $commands = [];
          $selected = $command . ' ' . $parameter;
          $parameter = explode(' ',$parameter);
          if (substr(strtolower($parameter[1]),0,2)=='as') $parameter[1]=substr($parameter[1],2,9999);
          if (!is_valid_ip_prefix($parameter[0])) throw new Exception('The parameter is not a valid IP address/prefix.');
          if (!match_as($parameter[1])) throw new Exception('The parameter is not a valid AS number.');
          log_to_file(str_replace(['%D', '%R', '%H', '%C'], [date('Y-m-d H:i:s'), $this->requester, $this->config['host'], '[BEGIN] '.$selected], $this->global_config['logs']['format']));
          $data .= $this->format_output($selected, $this->sanitize_output($this->rpki_validate($parameter)));
          log_to_file(str_replace(['%D', '%R', '%H', '%C'], [date('Y-m-d H:i:s'), $this->requester, $this->config['host'], '[END] '.$selected], $this->global_config['logs']['format']));
        break;
      default:
        $data = ''; $commands = $this->commands($command, $parameter);
        file_put_contents('/tmp/test',print_r($commands,true)."\n---------------\n",FILE_APPEND);
        $outputs = [];
        $auth = Authentication::instance($this->config, $this->global_config['logs']['auth_debug']);
        foreach ($commands as $idx=>$selected) {
          log_to_file(str_replace(['%D', '%R', '%H', '%C'], [date('Y-m-d H:i:s'), $this->requester, $this->config['host'], '[BEGIN] '.$selected], $this->global_config['logs']['format']));
          $outputs[] = $this->sanitize_output($auth->send_command((string) $selected));
          //$data .= $this->format_output($selected, $this->sanitize_output($auth->send_command((string) $selected)));
          log_to_file(str_replace(['%D', '%R', '%H', '%C'], [date('Y-m-d H:i:s'), $this->requester, $this->config['host'], '[END] '.$selected], $this->global_config['logs']['format']));
        }
        $data .= $this->format_output(implode("\n",$commands), implode("\n",$outputs));
    }
    return $data;
  }

  public function rpki_validate($parameter) {
    $url = sprintf("http://172.30.37.33:8323/api/v1/status");
    $status = json_decode(file_get_contents($url),false);
    $url = sprintf("http://172.30.37.33:8323/api/v1/validity/%s/%s",$parameter[1],$parameter[0]);
    $data = json_decode(file_get_contents($url),false);
    if (empty($data)) {
      $ret  = '<strong><big>NO RESULTS</big></strong>';
    } else {
      $res = (object)[];
      $res->asn = $data->validated_route->route->origin_asn;
      $res->prefix = $data->validated_route->route->prefix;
      $res->ts = strtotime(substr($status->lastUpdateDone,0,19).substr($status->lastUpdateDone,29));
      $res->state = sprintf("%s %s", $data->validated_route->validity->state, @$data->validated_route->validity->reason);
      $res->vrps = $data->validated_route->validity->VRPs;
      $res->description = $data->validated_route->validity->description;
      $ret  = sprintf('<strong><big><span style="cursor:help" title="%s">%s    %s    %s</span></big></strong>'.PHP_EOL, $res->description, $res->asn, $res->prefix, $res->state);
      $ret .= sprintf('Last Update  %s'.PHP_EOL.PHP_EOL, date('D d/m/Y H:i:s',$res->ts));
      if (!empty($res->vrps->matched)) {
        $ret .= sprintf('<strong>%s</strong>'.PHP_EOL, 'MATCHED');
        $ret .= '<table border="1" align="center">';
        $ret .= sprintf('<tr><th width="120">%s</th><th width="160">%s</th><th width="120">%s</th>'.PHP_EOL, 'AS', 'PREFIX','MAX LENGTH');
        foreach ($res->vrps->matched as $row) {
          $ret .= sprintf('<tr><td>%s</td><td>%s</td><td>%s</td>'.PHP_EOL, $row->asn, $row->prefix, $row->max_length);
        }
        $ret .= '</table>';
      }
      $ret .= PHP_EOL;
      if (!empty($res->vrps->unmatched_length)) {
        $ret .= sprintf('<strong>%s</strong>'.PHP_EOL, 'UNMATCHED LENGTH');
        $ret .= '<table border="1" align="center">';
        $ret .= sprintf('<tr><th width="120">%s</th><th width="160">%s</th><th width="120">%s</th>'.PHP_EOL, 'AS', 'PREFIX','MAX LENGTH');
        foreach ($res->vrps->unmatched_length as $row) {
          $ret .= sprintf('<tr><td>%s</td><td>%s</td><td>%s</td>'.PHP_EOL, $row->asn, $row->prefix, $row->max_length);
        }
        $ret .= '</table>';
      }
      $ret .= PHP_EOL;
      if (!empty($res->vrps->unmatched_as)) {
        $ret .= sprintf('<strong>%s</strong>'.PHP_EOL, 'UNMATCHED AS');
        $ret .= '<table border="1" align="center">';
        $ret .= sprintf('<tr><th width="120">%s</th><th width="160">%s</th><th width="120">%s</th>'.PHP_EOL, 'AS', 'PREFIX','MAX LENGTH');
        foreach ($res->vrps->unmatched_as as $row) {
          $ret .= sprintf('<tr><td>%s</td><td>%s</td><td>%s</td>'.PHP_EOL, $row->asn, $row->prefix, $row->max_length);
        }
        $ret .= '</table>';
      }
      $ret .= PHP_EOL;
    }
    unset($status,$data);
    return $ret;
  }

  public static final function instance($id, $requester) {
    global $config;
    switch (strtolower($config['routers'][$id]['type'])) {
      case 'juniper':
          $ret = new Juniper($config, $config['routers'][$id], $id, $requester);
        break;
      default:
          printf('Unknown router type "%s".',$config['routers'][$id]['type']);
          $ret = null;
    }
    return $ret;
  }

  protected abstract function bgp($parameter);
  protected abstract function bgp_terse($parameter);
  protected abstract function bgp_detail($parameter);
  protected abstract function bgp_summary();
  protected abstract function aspath_regexp($parameter);
  protected abstract function aspath($parameter);
  protected abstract function ping($parameter);
  protected abstract function icmp_traceroute($parameter);
  protected abstract function udp_traceroute($parameter);

}
