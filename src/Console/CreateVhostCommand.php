<?php

namespace Vortex\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateVhostCommand extends Command {

  private $input;
  private $output;

 protected function configure() {
    $this->setName('vhost:add')
        ->setDescription('Add a virtual host for apache or nginx')
        ->addArgument('domain', InputArgument::REQUIRED, 'Enter the domain name.')
        ->addArgument('path', InputArgument::REQUIRED, 'The path of your server files')
        ->addArgument('server', InputArgument::REQUIRED, 'The type of server')
        ->addOption('--with-host', null, InputOption::VALUE_NONE, 'If this option is set we add host to /etc/hosts');
  }

  protected function execute(InputInterface $input, OutputInterface $output) {
    $this->input = $input;
    $this->output = $output;

    $domain = $input->getArgument('domain');
    $path = $input->getArgument('path');
    $server = $input->getArgument('server');

    if(isset($path)) {
      if(!is_dir($path)) {
        mkdir($path, 0755, true);
      }
    }

    if($server == 'apache') {
      $this->addHost(APACHE_SITES_PATH, $domain, $server, $path);
    } else if ($server == 'nginx') {
      $this->addHost(NGINX_SITES_PATH, $domain, $server, $path);
    } else {
      $output->writeln('Server not recognized');
    }
  }

  /**
   * Create virtual host files for apache or nginx with the option to add a host
   * @param string $serverPath the path where the vhost files are located
   * @param string $domain     the domain name
   * @param string $server     the server type apache or nginx
   * @param string $output     output text
   */
  private function addHost($serverPath, $domain, $server, $path) {

    $src = array();
    $rpl = array();

    $this->output->writeln("Create vhost for $server!");

    if(!file_exists($serverPath.'/'.$domain.'.conf')) {
      $src[] = '{domain}';
      $rpl[] = $domain;
      $src[] = '{path}';
      $rpl[] = $path;

      if($server == 'apache')
        $serverData = str_replace($src, $rpl, file_get_contents(APACHE_TEMPLATE_FILE));
      elseif($server == 'nginx')
        $serverData = str_replace($src, $rpl, file_get_contents(NGINX_SITES_PATH));
      else
        return;

      if(file_put_contents($serverPath.'/'.$domain.'.conf', $serverData)) {
        $this->output->writeln("Hostname added to $server: ".$serverPath.'/'.$domain.'.conf');
      } else {
        $this->output->writeln("Could not write to file: ".$serverPath.'/'.$domain.'.conf');
      }

    } else {
      $this->output->writeln('Config file already exists.');
    }

    if($this->input->getOption('with-host')) {
        $this->output->writeln('Option to add domain to hosts file enabled!');
        if(file_put_contents(HOSTS_PATH, '127.0.0.1 '.$domain.PHP_EOL, FILE_APPEND)) {
          $this->output->writeln('Added domain to hosts.');
        } else {
          $this->output->writeln('Could not write to the hosts file, use sudo.');
        }
    }
  }

}
