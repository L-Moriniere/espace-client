<?php

namespace App\Command;

use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:check-connected-users',
    description: 'Récupérer les utilisateurs connectés au cours de la dernière heure',
)]
class CheckConnectedUsersCommand extends Command
{
    public function __construct(private UserRepository $userRepository)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $users = $this->userRepository->getUsersLastHour();
        $output->writeln(sprintf(
            '[%s] Utilisateurs connectés : %d',
            (new \DateTime())->format('Y-m-d H:i:s'),
            count($users)
        ));


        return Command::SUCCESS;
    }
}
