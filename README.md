# toolbox  
The toolbox that every linux web developer should have  

What it does?  
It can create virtual host files for apache and nginx  
It can add host to /etc/hosts  

Config.  
Ports and vhost paths can be changed in config.php for both apache and nginx.

Example command:  
`sudo php toolbox vhost:add --with-host --with-restart local3.dev /var/www/local.dev apache`
