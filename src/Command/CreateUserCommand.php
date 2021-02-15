<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

class CreateUserCommand extends Command
{
	protected static $defaultName = 'CreateUserCommand';
	private $_manager;
	private $_encoder;

	public function __construct(string $name = null, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
	{
		parent::__construct($name);
		$this->_manager = $manager;
		$this->_encoder = $encoder;
	}

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
		$helper = $this->getHelper('question');
		$output->writeln('init roles in database');

		$user = new User();
		$mail = $helper->ask($input, $output, new Question('user email: '));
		$user->setEmail($mail);
		$user->setActivate(true);
		$user->setUserName($helper->ask($input, $output, new Question("user name: ")));
		$question = new Question('password: ');
		$user->setPassword(
			$this->_encoder->encodePassword(
				$user ,
				$helper->ask($input, $output, $question->setHidden(true))));
		$this->_manager->persist($user);
		$this->_manager->flush();
		return 0;
	}
}
