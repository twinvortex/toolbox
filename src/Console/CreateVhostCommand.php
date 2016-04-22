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

    if($server == 'apache') {
      $output->writeln("Create vhost for apache!");
      if($input->getOption('--with-host')) {
          $output->writeln('Option to add to domain to hosts file enabled!');
      }
    } else if ($server == 'nginx') {
      $output->writeln("Create vhost for nginx");
    } else {
      $output->writeln('Server not recognized');
    }
  }

}
