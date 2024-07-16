<?php
// it was originally on DB
$config=<<<EOT
{
    "output": {
        "show_command": true
    },
    "filters": {
        "output": [],
        "aspath_regexp": [
            ".",
            ".*",
            ".[,]*",
            ".[0-9,0-9]*",
            ".[0-9,0-9]+"
        ]
    },
    "logs": {
        "file": "\/tmp\/looking-glass.log",
        "format": "[%D] [client: %R] %H > %C",
        "auth_debug": false
    },
    "misc": {
        "allow_private_asn": false,
        "allow_private_ip": false,
        "allow_reserved_ip": false,
        "minimum_prefix_length": {
            "ipv6": 0,
            "ipv4": 0
        }
    },
    "tools": {
        "ping_options": "-A -c 10",
        "ping_source_option": "-I",
        "traceroute4": "traceroute -4",
        "traceroute6": "traceroute -6",
        "traceroute_options": "-A -q1 -N32 -w1 -m15",
        "traceroute_source_option": "-s"
    },
    "doc": {
        "bgp_summary": {
            "command": "bgp summary",
            "description": "Show BGP summary information.",
            "parameter": "No parameter needed, so it's ignored."
        },
        "bgp": {
            "command": "bgp route IP_ADDRESS",
            "description": "Show the best routes to a given destination.",
            "parameter": "The parameter must be a valid destination. Destination means an IPv4\/IPv6 address or a subnet. Masks are also accepted as part of a valid IPv4\/IPv6 address.<br \/>RFC1918 addresses, IPv6 starting with FD or FC, and IPv4 reserved ranges (0.0.0.0\/8, 169.254.0.0\/16, 192.0.2.0\/24 and 224.0.0.0\/4) may be refused.<br \/>Please note that some routers always need a mask to be given when looking for an IPv6 address.<br \/><br \/>Example of valid arguments:<br \/><ul><li>8.8.8.8<\/li><li>8.8.4.0\/24<\/li><li>2001:db8:1337::42<\/li><li>2001:db8::\/32<\/li>"
        },
        "bgp_terse": {
            "command": "bgp terse route IP_ADDRESS",
            "description": "Show the best routes to a given destination (terse mode).",
            "parameter": "The parameter must be a valid destination. Destination means an IPv4\/IPv6 address or a subnet. Masks are also accepted as part of a valid IPv4\/IPv6 address.<br \/>RFC1918 addresses, IPv6 starting with FD or FC, and IPv4 reserved ranges (0.0.0.0\/8, 169.254.0.0\/16, 192.0.2.0\/24 and 224.0.0.0\/4) may be refused.<br \/>Please note that some routers always need a mask to be given when looking for an IPv6 address.<br \/><br \/>Example of valid arguments:<br \/><ul><li>8.8.8.8<\/li><li>8.8.4.0\/24<\/li><li>2001:db8:1337::42<\/li><li>2001:db8::\/32<\/li>"
        },
        "bgp_detail": {
            "command": "bgp detail route IP_ADDRESS",
            "description": "Show the best routes to a given destination (detail mode).",
            "parameter": "The parameter must be a valid destination. Destination means an IPv4\/IPv6 address or a subnet. Masks are also accepted as part of a valid IPv4\/IPv6 address.<br \/>RFC1918 addresses, IPv6 starting with FD or FC, and IPv4 reserved ranges (0.0.0.0\/8, 169.254.0.0\/16, 192.0.2.0\/24 and 224.0.0.0\/4) may be refused.<br \/>Please note that some routers always need a mask to be given when looking for an IPv6 address.<br \/><br \/>Example of valid arguments:<br \/><ul><li>8.8.8.8<\/li><li>8.8.4.0\/24<\/li><li>2001:db8:1337::42<\/li><li>2001:db8::\/32<\/li>"
        },
        "as-path-regex": {
            "command": "bgp route as-path-regex AS_PATH_REGEX",
            "description": "Show the routes matching the given AS path regular expression.",
            "parameter": "The parameter must be a valid AS path regular expression and must not contain any \" characters (the input will be automatically quoted if needed).<br \/>Please note that these expressions can change depending on the router and its software.<br \/>OpenBGPD does not support regular expressions, but will search for the submitted AS number anywhere in the AS path.<br \/><br \/>Here are some examples:<ul><li><strong>Juniper<\/strong> - ^AS1 AS2 .*$<\/li><li><strong>Cisco<\/strong> - ^AS1_AS2_<\/li><li><strong>BIRD<\/strong> - AS1 AS2 AS3 &hellip; ASZ<\/li><li><strong>OpenBGPD<\/strong> - AS1<\/li><\/ul><br \/>You may find some help with the following link:<br \/><ul><li><a href=\"http:\/\/www.juniper.net\/techpubs\/en_US\/junos13.3\/topics\/reference\/command-summary\/show-route-aspath-regex.html\" title=\"Juniper Documentation\">Juniper Documentation<\/a><\/li><li><a href=\"http:\/\/www.cisco.com\/c\/en\/us\/support\/docs\/ip\/border-gateway-protocol-bgp\/26634-bgp-toc.html#asregexp\" title=\"Cisco Documentation\">Cisco Documentation<\/a><\/li><li><a href=\"http:\/\/bird.network.cz\/?get_doc&f=bird-5.html\" title=\"BIRD Documentation\">BIRD Documentation<\/a> (search for bgpmask)<\/li><\/ul>"
        },
        "as": {
            "command": "bgp route as-path-number ASN",
            "description": "Show the routes received from a given neighboring AS number.",
            "parameter": "The parameter must be a valid 16-bit or 32-bit autonomous system number.<br \/>Be careful, 32-bit ASN are not handled by old routers or old router softwares.<br \/>Unless specified, private ASN will be considered as invalid.<br \/><br \/>Example of valid argument:<br \/><ul><li>15169<\/li><li>29467<\/li><\/ul>"
        },
        "ping": {
            "command": "icmp ping IP_ADDRESS|HOSTNAME",
            "description": "Send pings to the given destination.",
            "parameter": "The parameter must be an IPv4\/IPv6 address (without mask) or a hostname.<br \/>RFC1918 addresses, IPv6 starting with FD or FC, and IPv4 reserved ranges (0.0.0.0\/8, 169.254.0.0\/16, 192.0.2.0\/24 and 224.0.0.0\/4) may be refused.<br \/><br \/>Example of valid arguments:<br \/><ul><li>8.8.8.8<\/li><li>2001:db8:1337::42<\/li><li>example.com<\/li><\/ul>"
        },
        "icmp_traceroute": {
            "command": "icmp traceroute IP_ADDRESS|HOSTNAME",
            "description": "Display the path to a given destination.",
            "parameter": "The parameter must be an IPv4\/IPv6 address (without mask) or a hostname.<br \/>RFC1918 addresses, IPv6 starting with FD or FC, and IPv4 reserved ranges (0.0.0.0\/8, 169.254.0.0\/16, 192.0.2.0\/24 and 224.0.0.0\/4) may be refused.<br \/><br \/>Example of valid arguments:<br \/><ul><li>8.8.8.8<\/li><li>2001:db8:1337::42<\/li><li>example.com<\/li><\/ul>"
        },
        "udp_traceroute": {
            "command": "udp traceroute IP_ADDRESS|HOSTNAME",
            "description": "Display the path to a given destination.",
            "parameter": "The parameter must be an IPv4\/IPv6 address (without mask) or a hostname.<br \/>RFC1918 addresses, IPv6 starting with FD or FC, and IPv4 reserved ranges (0.0.0.0\/8, 169.254.0.0\/16, 192.0.2.0\/24 and 224.0.0.0\/4) may be refused.<br \/><br \/>Example of valid arguments:<br \/><ul><li>8.8.8.8<\/li><li>2001:db8:1337::42<\/li><li>example.com<\/li><\/ul>"
        },
        "rpki_validate": {
            "command": "rpki check PREFIX AS",
            "description": "Show what PREFIXes with what ASorigins are impacted by the RFC 6811 Origin Validation procedure.",
            "parameter": "The parameter must be couple IPv4\/IPv6 ASnumbenr.<br \/><br \/>Example of valid arguments:<br \/><ul><li>45.67.115.0\/24 41327<\/li><li>2a09:3e40::\/29 41327<\/li><\/ul>"
        }
    },
    "routers": {
        "router1": {
            "source-interface-id": "lo0",
            "user": "lookingglass",
            "pass": "E59fcefff986980f6d67529d57b48819b3f12247!",
            "auth": "ssh-password",
            "type": "juniper",
            "host": "185.157.228.251",
            "desc": "MX204 - Opeb Hub Med - Carini (PA)",
            "probes": []
        },
        "router2": {
            "source-interface-id": "lo0",
            "user": "lookingglass",
            "pass": "E59fcefff986980f6d67529d57b48819b3f12247!",
            "auth": "ssh-password",
            "type": "juniper",
            "host": "185.157.228.254",
            "desc": "MX204 - Cineca NaMeX #1 - Roma",
            "probes": []
        },
        "router3": {
            "source-interface-id": "lo0",
            "user": "lookingglass",
            "pass": "E59fcefff986980f6d67529d57b48819b3f12247!",
            "auth": "ssh-password",
            "type": "juniper",
            "host": "185.157.228.247",
            "desc": "MX204 - DATA4 - Cornaredo (MI)",
            "probes": []
        },
        "router4": {
            "source-interface-id": "lo0",
            "user": "lookingglass",
            "pass": "E59fcefff986980f6d67529d57b48819b3f12247!",
            "auth": "ssh-password",
            "type": "juniper",
            "host": "185.157.228.253",
            "desc": "MX960 - MIX - Milano",
            "probes": []
        },
        "router5": {
            "source-interface-id": "lo0",
            "user": "lookingglass",
            "pass": "E59fcefff986980f6d67529d57b48819b3f12247!",
            "auth": "ssh-password",
            "type": "juniper",
            "host": "185.157.228.246",
            "desc": "MX204 - Irideos - Avalon Campus - Milano",
            "probes": []
        },
        "router6": {
            "source-interface-id": "lo0",
            "user": "lookingglass",
            "pass": "E59fcefff986980f6d67529d57b48819b3f12247!",
            "auth": "ssh-password",
            "type": "juniper",
            "host": "185.157.228.248",
            "desc": "MX204 - Applico - Perugia",
            "probes": []
        },
        "router7": {
            "source-interface-id": "lo0",
            "user": "lookingglass",
            "pass": "E59fcefff986980f6d67529d57b48819b3f12247!",
            "auth": "ssh-password",
            "type": "juniper",
            "host": "185.157.228.252",
            "desc": "MX480 - British Telecom - Palermo",
            "probes": []
        },
        "router8": {
            "source-interface-id": "lo0",
            "user": "lookingglass",
            "pass": "E59fcefff986980f6d67529d57b48819b3f12247!",
            "auth": "ssh-password",
            "type": "juniper",
            "host": "185.157.228.245",
            "desc": "MX204 - Interxion - Amsterdam",
            "probes": []
        },
        "router9": {
            "source-interface-id": "lo0",
            "user": "lookingglass",
            "pass": "E59fcefff986980f6d67529d57b48819b3f12247!",
            "auth": "ssh-password",
            "type": "juniper",
            "host": "185.157.228.244",
            "desc": "MX204 - Interxion - Frankfurt",
            "probes": []
        },
        "router10": {
            "source-interface-id": "lo0",
            "user": "lookingglass",
            "pass": "E59fcefff986980f6d67529d57b48819b3f12247!",
            "auth": "ssh-password",
            "type": "juniper",
            "host": "185.157.228.236",
            "desc": "MX204 - Top-IX - Torino",
            "probes": []
        },
        "router11": {
            "source-interface-id": "lo0",
            "user": "lookingglass",
            "pass": "E59fcefff986980f6d67529d57b48819b3f12247!",
            "auth": "ssh-password",
            "type": "juniper",
            "host": "185.157.228.227",
            "desc": "MX204 - VSIX - Padova",
            "probes": []
        },
        "router12": {
            "source-interface-id": "lo0",
            "user": "lookingglass",
            "pass": "E59fcefff986980f6d67529d57b48819b3f12247!",
            "auth": "ssh-password",
            "type": "juniper",
            "host": "185.157.228.228",
            "desc": "MX204 - Cineca NaMeX #2 - Roma",
            "probes": []
        },
        "router13": {
            "source-interface-id": "lo0",
            "user": "lookingglass",
            "pass": "E59fcefff986980f6d67529d57b48819b3f12247!",
            "auth": "ssh-password",
            "type": "juniper",
            "host": "185.157.228.230",
            "desc": "MX204 - Cloud Europe - Roma",
            "probes": []
        },
        "router14": {
            "source-interface-id": "lo0",
            "user": "lookingglass",
            "pass": "E59fcefff986980f6d67529d57b48819b3f12247!",
            "auth": "ssh-password",
            "type": "juniper",
            "host": "185.157.228.231",
            "desc": "MX204 - Cloud Europe 2 - Roma",
            "probes": []
        },
        "router15": {
            "source-interface-id": "lo0",
            "user": "lookingglass",
            "pass": "E59fcefff986980f6d67529d57b48819b3f12247!",
            "auth": "ssh-password",
            "type": "juniper",
            "host": "185.157.228.255",
            "desc": "MX204 - Pop Catania - Catania",
            "probes": []
        },
        "router16": {
            "source-interface-id": "lo0",
            "user": "lookingglass",
            "pass": "E59fcefff986980f6d67529d57b48819b3f12247!",
            "auth": "ssh-password",
            "type": "juniper",
            "host": "185.157.228.226",
            "desc": "MX204 - MIX - Milano",
            "probes": []
        },
        "router17": {
            "source-interface-id": "lo0",
            "user": "lookingglass",
            "pass": "E59fcefff986980f6d67529d57b48819b3f12247!",
            "auth": "ssh-password",
            "type": "juniper",
            "host": "185.157.228.221",
            "desc": "MX204 - BT - Palermo",
            "probes": []
        },
        "router18": {
            "source-interface-id": "lo0",
            "user": "lookingglass",
            "pass": "E59fcefff986980f6d67529d57b48819b3f12247!",
            "auth": "ssh-password",
            "type": "juniper",
            "host": "185.157.228.224",
            "desc": "MX204 - Equinix - Frankfurt",
            "probes": []
        }
    }
}
EOT;
$config=json_decode($config,true);
