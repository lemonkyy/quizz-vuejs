<?php

namespace App\Command;

use App\Entity\Room;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:delete-all-rooms',
    description: 'Deletes all rooms from the database.',
)]
class DeleteAllRoomsCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $roomRepository = $this->entityManager->getRepository(Room::class);
        $rooms = $roomRepository->findAll();

        if (empty($rooms)) {
            $io->info('No rooms found to delete.');
            return Command::SUCCESS;
        }

        if (!$io->confirm(sprintf('Are you sure you want to delete %d rooms?', count($rooms)), false)) {
            $io->info('Operation cancelled.');
            return Command::SUCCESS;
        }

        $io->progressStart(count($rooms));

        foreach ($rooms as $room) {
            $this->entityManager->remove($room);
            $io->progressAdvance();
        }

        $this->entityManager->flush();

        $io->progressFinish();
        $io->success(sprintf('Successfully deleted %d rooms.', count($rooms)));

        return Command::SUCCESS;
    }
}
