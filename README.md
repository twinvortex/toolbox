# toolbox  
The toolbox that every linux web developer should have  

What it does?  
It can create virtual host files for apache and nginx  
It can add host to /etc/hosts  

Install using `git clone https://github.com/twinvortex/toolbox.git`  
Install composer dependencies with `composer install`  

Config.  
Ports and vhost paths can be changed in config.php for both apache and nginx.

Example command:  
`sudo php toolbox vhost:add --with-host --with-restart local.test /var/www/local.test apache`

Example command with IP:  
`sudo php toolbox vhost:add --with-host --with-restart local3.test /var/www/local3.test apache "192.168.1.3"`
