<?php

namespace Presidos\Fixtures;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
		$this->setDescription('Fixtures import');
		$this->addOption('test', 't', InputOption::VALUE_NONE, 'Import test data');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$this->fixtures->addFixtures();
		$output->writeln('Importing fixtures...');

		if ($input->getOption('test')) {
			$this->fixtures->addTestData();
			$output->writeln('Importing test data...');
		}

		$this->fixtures->execute();

		$output->writeln('OK');
	}

} 