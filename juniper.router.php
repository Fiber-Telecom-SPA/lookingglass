<?php
require_once('router.class.php');
require_once('utils.inc.php');

final class Juniper extends Router {
  protected function bgp($parameter) {
    $cmd = sprintf('show route %s protocol bgp table%s%s%s', $parameter,(match_ipv6($parameter, false)?' inet6.0':''),(match_ipv4($parameter, false)?' inet.0':''),(@$this->config['bgp_detail']?' detail':''));
    return [$cmd];
  }

  protected function bgp_terse($parameter) {
    $cmd = sprintf('show route %s protocol bgp table%s%s%s terse', $parameter,(match_ipv6($parameter, false)?' inet6.0':''),(match_ipv4($parameter, false)?' inet.0':''),(@$this->config['bgp_detail']?' detail':''));
    return [$cmd];
  }

  protected function bgp_detail($parameter) {
    $cmd = sprintf('show route %s protocol bgp table%s%s%s detail', $parameter,(match_ipv6($parameter, false)?' inet6.0':''),(match_ipv4($parameter, false)?' inet.0':''),(@$this->config['bgp_detail']?' detail':''));
    return [$cmd];
  }

  protected function bgp_summary() {
    $cmd = 'show bgp summary | except "vrf|vpls|vpn"';
    return [$cmd];
  }

  protected function aspath_regexp($parameter) {
    $cmd6 = sprintf('show route aspath-regex %s protocol bgp table inet6.0', quote($parameter));
    $cmd4 = sprintf('show route aspath-regex %s protocol bgp table inet.0', quote($parameter));
    return [$cmd6, $cmd4];
  }

  protected function aspath_detail_regexp($parameter) {
    $cmd6 = sprintf('show route aspath-regex %s protocol bgp table inet6.0 detail', quote($parameter));
    $cmd4 = sprintf('show route aspath-regex %s protocol bgp table inet.0 detail', quote($parameter));
    return [$cmd6, $cmd4];
  }

  protected function aspath($parameter) {
    return $this->aspath_regexp(sprintf('^%s .*',$parameter));
  }

  protected function ping($parameter) {
    if (!is_valid_destination($parameter)) throw new Exception('The parameter is not an IP address or a hostname.');
    $cmd = sprintf('ping count 10 rapid %s%s no-resolve', $parameter, ($this->has_source_interface_id()?sprintf(' interface %s', $this->get_source_interface_id()):''));
    return [$cmd];
  }

  protected function icmp_traceroute($parameter) {
    if (!is_valid_destination($parameter)) throw new Exception('The parameter is not an IP address or a hostname.');
    $cmd = sprintf('traceroute monitor summary %s %s%s no-resolve',$parameter,(match_ipv6($parameter, false)?' inet6':''),(match_ipv4($parameter, false)?' inet':''));
    return [$cmd];
  }

  protected function udp_traceroute($parameter) {
    if (!is_valid_destination($parameter)) throw new Exception('The parameter is not an IP address or a hostname.');
    $cmd = sprintf('traceroute%s%s as-number-lookup wait 2 %s%s no-resolve',(match_ipv6($parameter, false)?' inet6':''),(match_ipv4($parameter, false)?' inet':''),$parameter,($this->has_source_interface_id()?sprintf(' interface %s', $this->get_source_interface_id()):''));
    return [$cmd];
  }
}
