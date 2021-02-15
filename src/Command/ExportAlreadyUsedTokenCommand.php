<?php

namespace App\Command;

use App\Entity\TokenNumber;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;
use DateTime;

class ExportAlreadyUsedTokenCommand extends Command
{
    protected static $defaultName = 'cititest:exportToken';

    private $_em;
    private $_tokenRepo;
    public function __construct( string $name = null, EntityManagerInterface $manager)
    {
		parent::__construct($name);
        $this->_em = $manager;
        $this->_tokenRepo = $manager->getRepository(TokenNumber::class);
    }
    protected function configure()
    {
        $this
            ->setDescription('export token used last mounth');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $date = new DateTime('last month');
        $tokens = $this->_tokenRepo->getTokenByUpdate($date);
        $gzFile = "Tokens_" . $date->format("Y-D-M h:m:s") . ".gz";
        $c = -1;
        $len = count($tokens);
        $fp = gzopen($gzFile, "w9");
        gzwrite($fp, "[\n");
        while (++$c < $len)
        {
            gzwrite($fp, json_encode($tokens[$c]->getSerializeToken()));
            if ($c + 1 < $len)
                gzwrite($fp, ",\n");
            $this->_em->remove($tokens[$c]);
        }
        gzwrite($fp, "]");
        gzclose($fp);
        return 0;
    }

}
