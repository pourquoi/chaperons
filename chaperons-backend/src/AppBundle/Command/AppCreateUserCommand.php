<?php

namespace AppBundle\Command;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

class AppCreateUserCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:create-user')
            ->setDescription('Create a new user')
            ->addArgument('username', InputArgument::REQUIRED)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $question = new Question('password: ');
        $question->setHidden(true);
        $question->setHiddenFallback(false);
        $password = $helper->ask($input, $output, $question);

        $question = new Question('confirm password: ');
        $question->setHidden(true);
        $question->setHiddenFallback(false);
        $password2 = $helper->ask($input, $output, $question);

        if( $password != $password2 ) {
            $output->writeln('passwords does not match');
            exit(1);
        }

        $username = $input->getArgument('username');

        /** @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine')->getManager();

        $user = new User();
        $user->setApiKey(User::generateApiKey(7));
        $user->setUsername($username);

        /** @var UserPasswordEncoder $encoder */
        $encoder = $this->getContainer()->get('security.password_encoder');
        $encoded = $encoder->encodePassword($user, $password);
        $user->setPassword($encoded);

        $em->persist($user);
        $em->flush();

        $output->writeln(sprintf('User %s created', $username));
    }

}
