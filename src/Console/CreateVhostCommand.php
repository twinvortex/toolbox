<?php

namespace Vortex\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateVhostCommand extends Command {

 protected function configure() {
    $this->setName('vhost:add')
        ->setDescription('Add a virtual host for apache or nginx')
        ->addArgument('domain', InputArgument::REQUIRED, 'Enter the domain name.')
        ->addArgument('path', InputArgument::REQUIRED, 'The path of your server files')
        ->addArgument('server', InputArgument::REQUIRED, 'The type of server')
        ->addOption('--with-host', null, InputOption::VALUE_NONE, 'If this option is set we add host to /etc/hosts');
  }

  protected function execute(InputInterface $input, OutputInterface $output) {
    $domain = $input->getArgument('domain');
    $path = $input->getArgument('path');
    $server = $input->getArgument('server');

    if(isset($path)) {
      if(!is_dir($path)) {
        mkdir($path, 0755, true);
      }
    }

    if($server == 'apache') {
      $output->writeln("Create vhost for apache!");
      if(!file_exists(APACHE_SITES_PATH.'/'.$domain.'.conf')) {
        $src[] = '{domain}';
        $rpl[] = $domain;
        $src[] = '{path}';
        $rpl[] = $path;

        $apacheData = str_replace($src, $rpl, file_get_contents(APACHE_TEMPLATE_FILE));
        if(file_put_contents(APACHE_SITES_PATH.'/'.$domain.'.conf', $apacheData)) {
          $output->writeln("Hostname added to apache: ".APACHE_SITES_PATH.'/'.$domain.'.conf');
        } else {
          $output->writeln("Could not write to file: ".APACHE_SITES_PATH.'/'.$domain.'.conf');
        }

      } else {
        $output->writeln('Config file already exists.');
      }
      if($input->getOption('with-host')) {
          $output->writeln('Option to add to domain to hosts file enabled!');
          if(file_put_contents(HOSTS_PATH, '127.0.0.1 '.$domain, FILE_APPEND)) {
            $output->writeln('Added domain to hosts.');
          }
      }
    } else if ($server == 'nginx') {
      $output->writeln("Create vhost for nginx");
    } else {
      $output->writeln('Server not recognized');
    }
  }

}
