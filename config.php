<?php

// The default paths for nginx and apache.
// These can be changed according to your install
define("APACHE_SITES_PATH", "/etc/apache2/sites-enabled");
define("NGINX_SITES_PATH", "/etc/nginx/sites-enabled");
define("APACHE_PORT", 80);
define("NGINX_PORT", 81);

// Default template config files for apache, nginx and bind
define("APACHE_TEMPLATE_FILE", __DIR__ . '/src/Console/tmp/apache.tmp.conf');
define("NGNIX_TEMPLATE_FILE", __DIR__ . '/src/Console/tmp/nginx.tmp.conf');
define("BIND_TEMPLATE_FILE", __DIR__ . '/src/Console/tmp/bind.tmp.conf');

define("HOSTS_PATH", "/etc/hosts");
