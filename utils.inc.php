<?php
require_once('config.inc.php');

function match_private_ip_range($ip) { return (empty($ip)?false:(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE) ? false : true )); }
function match_reserved_ip_range($ip) { return (empty($ip)?false:(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE) ? false : true)); }
function match_hostname($hostname) { return (match_ipv4($hostname) || match_ipv6($hostname)?false:(preg_match('/^(_?[a-z\d](-*[_a-z\d])*)(\.(_?[a-z\d](-*[_a-z\d])*))*$/i', $hostname) && preg_match('/^.{1,253}$/', $hostname) && preg_match('/^[^\.]{1,63}(\.[^\.]{1,63})*$/', $hostname))); }
function is_valid_ip_address($ip_address) { return match_ipv6($ip_address, true) || match_ipv4($ip_address, true); }
function is_valid_ip_prefix($ip_address) { return match_ipv6($ip_address, false) || match_ipv4($ip_address, false); }
function is_valid_destination($destination) { return is_valid_ip_address($destination) || match_hostname($destination); }
function quote($string) { return sprintf('"%s"', $string); }
function IP() { return (empty($_SERVER["HTTP_X_FORWARDED_FOR"])?@$_SERVER["REMOTE_ADDR"]:$_SERVER["HTTP_X_FORWARDED_FOR"]); }

function match_ipv6($ip, $ip_only = true) {
  global $config;
  if (empty($ip)) return false;
  if (strrpos($ip, '/') && !$ip_only)  {
    $ip_and_mask = explode('/', $ip, 2);
    if (!$config['misc']['allow_private_ip'] && match_private_ip_range($ip_and_mask[0])) return false;
    $min_prefix_length = @$config['minimum_prefix_length']['ipv6'];
    $prefix_length = intval($ip_and_mask[1]);
    if (($min_prefix_length > 0) && ($prefix_length < $min_prefix_length)) return false;
    return filter_var($ip_and_mask[0], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) && filter_var($ip_and_mask[1], FILTER_VALIDATE_INT);
  } else {
    if (!$config['misc']['allow_private_ip'] && match_private_ip_range($ip)) return false;
    return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
  }
}

function match_ipv4($ip, $ip_only = true) {
  global $config;
  if (empty($ip)) return false;
  if (strrpos($ip, '/') && !$ip_only)  {
    $ip_and_mask = explode('/', $ip, 2);
    if (!$config['misc']['allow_private_ip'] && match_private_ip_range($ip_and_mask[0])) return false;
    if (!$config['misc']['allow_reserved_ip'] && match_reserved_ip_range($ip_and_mask[0])) return false;
    $min_prefix_length = @$config['minimum_prefix_length']['ipv4'];
    $prefix_length = intval($ip_and_mask[1]);
    if (($min_prefix_length > 0) && ($prefix_length < $min_prefix_length)) return false;
    return filter_var($ip_and_mask[0], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) && filter_var($ip_and_mask[1], FILTER_VALIDATE_INT);
  } else {
    if (!$config['misc']['allow_private_ip'] && match_private_ip_range($ip)) return false;
    if (!$config['misc']['allow_reserved_ip'] && match_reserved_ip_range($ip)) return false;
    return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
  }
}

function match_as($as) {
  global $config;
  $options_wide_range = array( 'options' => array('min_range' => 1, 'max_range' => 4294967294) );
  $options_16bit_private = array( 'options' => array( 'min_range' => 64512, 'max_range' => 65534) );
  $options_32bit_private = array( 'options' => array('min_range' => 4200000000, 'max_range' => 4294967294) );
  if (empty($as)) return false;
  if (!filter_var($as, FILTER_VALIDATE_INT, $options_wide_range)) return false;
  if (filter_var($as, FILTER_VALIDATE_INT, $options_16bit_private)) return isset($config['misc']['allow_private_asn']) && $config['misc']['allow_private_asn'] == true;
  if (filter_var($as, FILTER_VALIDATE_INT, $options_32bit_private)) return isset($config['misc']['allow_private_asn']) && $config['misc']['allow_private_asn'] == true;
  return true;
}

function match_aspath_regexp($aspath_regexp) {
  global $config;
  if (empty($aspath_regexp)) return false;
  if (strpos($aspath_regexp, ';') !== false) return false;
  if (strpos($aspath_regexp, '"') !== false) return false;
  if (strpos($aspath_regexp, '\'') !== false) return false;
  foreach ($config['filters']['aspath_regexp'] as $invalid_aspath_regexp) if ($invalid_aspath_regexp === $aspath_regexp) return false;
  return true;
}

function hostname_to_ip_address($hostname, $config = null) {
  $record_types = DNS_AAAA + DNS_A;
  $dns_record = dns_get_record($hostname, $record_types);
  if (!$dns_record) return false;
  $records_nb = count($dns_record);
  if ($records_nb == 1) {
    if ($dns_record[0]['type'] == 'AAAA') return $dns_record[0]['ipv6'];
      else if ($dns_record[0]['type'] == 'A') return $dns_record[0]['ip'];
        else return false;
  }
  if ($records_nb > 1) {
    foreach ($dns_record as $record) if ($record['type'] == 'AAAA') return $record['ipv6'];
    foreach ($dns_record as $record) if ($record['type'] == 'A') return $record['ip'];
    return false;
  }
}

function log_to_file($log) {
  global $config;
  file_put_contents($config['logs']['file'], $log."\n", FILE_APPEND | LOCK_EX);
}
