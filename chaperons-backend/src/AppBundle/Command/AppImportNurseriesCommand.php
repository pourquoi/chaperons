<?php

namespace AppBundle\Command;

use App\NurseryParser;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AppImportNurseriesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:import-nurseries')
            ->setDescription('Import nurseries from csv')
            ->addArgument('path', InputArgument::REQUIRED, 'path to the csv')
            ->addOption('strict', null, InputOption::VALUE_NONE, 'will remove database nurseries not in the csv.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $input->getArgument('path');
        $strict = $input->getOption('strict');

        /** @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine')->getManager();

        $parser = new NurseryParser($em);
        $n = $parser->importCsv($path, $strict);
        $em->flush();


        $output->writeln(sprintf('%d imported',$n));
    }

}
