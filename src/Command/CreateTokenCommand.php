<?php

namespace App\Command;

use App\Entity\TokenNumber;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;

class CreateTokenCommand extends Command
{
    protected static $defaultName = 'createToken';
    protected $manager = null;
    protected $batch = 500;
    protected $count = 0;
    protected $badToken = [];
    protected $tokenRepo = null;
    public function __construct(string $name = null, EntityManagerInterface $manager)
    {
        parent::__construct($name);
        $this->manager = $manager;
        $this->tokenRepo = $manager->getRepository(TokenNumber::class);
    }

    protected function configure()
    {
        $this
            ->setDescription('createtion of token at 8 number')
            ->addArgument('arg1', InputArgument::REQUIRED, 'number of token generated');;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $len = (int) $input->getArgument('arg1');
        $c = -1;
        $flagCheck = false;
        while (++$c < $len)
        {
            while ($flagCheck === false)
            {
                $str = $this->generateToken();
                if ($this->checkToken($str) === true)
                    $flagCheck = true;
                if ($flagCheck === true)
                    $this->createToken($str);
            }
            $flagCheck = false;
        }
        return (0);
    }

    private function generateToken()
    {
        $c = -1;
        $str = "";
        while(++$c < 8)
        {
            if ($c === 0)
                $num = rand(1, 9);
            else
                $num = rand(0, 9);
            $str .= (string) $num;
        }
        return $str;
    }

    private function checkToken($token)
    {
        if (in_array((int) $token, $this->badToken))
            return false;
        else if ($this->tokenRepo->findOneBy(["number" => (int) $token]) instanceof TokenNumber)
        {
            $this->badToken[] = (int) $token;
            return false;
        }
        else if (strlen($token) !== 8)
            return false;
        return true;
    }

    private function createToken($num)
    {
        $token = new TokenNumber();
        $token->setNumber((int) $num);
        $this->manager->persist($token);
        $this->manager->flush();
        echo("token create: " . $token->getNumber() . "\n");
        $this->count++;
        if ($this->count === $this->batch)
        {
            $this->count = 0;
            $this->manager->clear();
        }
    }
}