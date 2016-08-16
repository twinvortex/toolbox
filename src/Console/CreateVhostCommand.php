<?php

namespace Vortex\Console;

use Symfony\Component\Process\Process;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
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
        ->addArgument('ip', InputArgument::OPTIONAL, 'The default ip')
        ->addOption('--with-host', null, InputOption::VALUE_NONE, 'If this option is set we add host to /etc/hosts')
        ->addOption('--with-restart', null, InputOption::VALUE_NONE, 'Restart the server');
  }

  protected function execute(InputInterface $input, OutputInterface $output) {
    $this->input = $input;
    $this->output = $output;

    $domain = $input->getArgument('domain');
    $path = $input->getArgument('path');
    $server = $input->getArgument('server');
    $ip = $input->getArgument('ip');
    if(empty($ip) && $server == 'apache')
      $ip = '*';

    if(empty($ip) && $server == 'nginx')
      $ip = '';
    else
      $ip = $ip.':';

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
   * Adding a virtual host files adding domain to /etc/hosts and restarting server
   * @param string The path where the vhost files are
   * @param string The domain name eg: local.dev
   * @param string The type of server: apache / nginx
   * @param string The path where the files are
   * @param string The ip used in vhost
   */
  private function addHost($serverPath, $domain, $server, $path, $ip = '*') {

    $src = array();
    $rpl = array();

    $this->output->writeln("Create vhost for $server!");

    if(!file_exists($serverPath.'/'.$domain.'.conf')) {
      $src[] = '{domain}';
      $rpl[] = $domain;
      $src[] = '{path}';
      $rpl[] = $path;
      $src[] = '{ip_address}';
      $rpl[] = $ip;
      $src[] = '{port}';
      if($server == 'apache')
        $rpl[] = APACHE_PORT;
      else if($server == 'nginx')
        $rpl[] = NGINX_PORT;
      else
        $rpl[] = 80;

      if($server == 'apache')
        $serverData = str_replace($src, $rpl, file_get_contents(APACHE_TEMPLATE_FILE));
      elseif($server == 'nginx')
        $serverData = str_replace($src, $rpl, file_get_contents(NGNIX_TEMPLATE_FILE));
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

    if($this->input->getOption('with-restart')) {
      if($server == 'apache') {
        $process = new Process("service apache2 restart");
        $process->run();
        if(!$process->isSuccessful())
          throw new \RuntimeException($process->getErrorOutput());
        else
          $this->output->writeln('Apache server restarted!');
      } else if($server == 'nginx') {
          $process = new Process("service nginx restart");
          $process->run();
          if(!$process->isSuccessful())
            throw new \RuntimeException($process->getErrorOutput());
          else
            $this->output->writeln('Nginx server restarted!');
      } else {
        $this->output->writeln('No action taken!');
      }
    }
  }

}
