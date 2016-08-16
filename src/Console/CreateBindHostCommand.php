<?php

namespace Vortex\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class CreateBindHostCommand extends Command {
	private $input;
	private $output;

	protected function configure() {
		$this->setName('bind:add')
			->addArgument('domain', InputArgument::REQUIRED, 'Enter the domain name.')
			->addArgument('ip', InputArgument::REQUIRED, 'Enter the ip.')
			->setDescription('Add a domain name!');
	}

	protected function exec(InputInterface $input, OutputInterface $output) {
		$this->input = $input;
		$this->output = $output;
	}
}
