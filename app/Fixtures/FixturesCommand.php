<?php

namespace Presidos\Fixtures;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Jan Marek
 */
class FixturesCommand extends Command
{

	/**
	 * @var Fixtures
	 */
	private $fixtures;

	public function __construct(Fixtures $fixtures)
	{
		parent::__construct('presidos:fixtures');
		$this->fixtures = $fixtures;
	}

	protected function configure()
	{
		$this
			->setDescription('Fixtures import');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$output->writeln('Importing fixtures...');
		$this->fixtures->install();

		$output->writeln('OK');
	}

} 