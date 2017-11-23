<?php

namespace AppBundle\Command;

use AppBundle\Entity\Profile;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BasicGuestCodeCommand extends ContainerAwareCommand
{
    protected function configure() {
        $this
            ->setName('tml:guest_code:basic')
            ->setDescription('')
        ;
    }


    protected function execute(InputInterface $input, OutputInterface $output ) {
        ini_set('max_execution_time',0);
        $users = $this->getContainer()->get('fos_user.user_manager')->getAllByRole(Profile::PROFILE_BASIC);
        $guestCodeService = $this->getContainer()->get('tml.guest_code');
        foreach($users as $user) {
            $guestCodeService->generateCodeByUser($user);
        }
        ini_restore('max_execution_time');

    }
}