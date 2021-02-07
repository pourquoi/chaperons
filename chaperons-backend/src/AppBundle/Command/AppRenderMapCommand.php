<?php

namespace AppBundle\Command;

use AppBundle\Entity\Map;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AppRenderMapCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:render-map')
            ->setDescription('Render the map with phantomjs. Debug command.')
            ->addArgument('map_id', InputArgument::REQUIRED)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $map_id = $input->getArgument('map_id');

        $em = $this->getContainer()->get('doctrine')->getManager();
        /** @var Map $map */
        $map = $em->getRepository('AppBundle:Map')->find($map_id);

        if( !$map ) {
            $output->println(sprintf('map %d not found', $map_id));
        }

        $renderer = $this->getContainer()->get('app.renderer');
        $renderer->render($map);

        $em->flush();

        $output->writeln(sprintf('map rendered in web/maps/%s', $map->getCaptureFilename()));
    }

}
