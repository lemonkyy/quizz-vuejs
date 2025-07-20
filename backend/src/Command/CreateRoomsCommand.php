<?php

namespace App\Command;

use App\Entity\Room;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:create-rooms',
    description: 'Creates a public room for each user.',
)]
class CreateRoomsCommand extends Command
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $users = $this->userRepository->findAll();

        if (empty($users)) {
            $io->warning('No users found. Please create some users first.');
            return Command::SUCCESS;
        }

        $io->progressStart(count($users));

        foreach ($users as $user) {
            $room = new Room();
            $room->setOwner($user);
            $room->setIsPublic(true);
            $room->setCode($this->generateUniqueRoomCode());

            $this->entityManager->persist($room);
            $io->progressAdvance();
        }

        $this->entityManager->flush();

        $io->progressFinish();
        $io->success(sprintf('Successfully created %d rooms.', count($users)));

        return Command::SUCCESS;
    }

    private function generateUniqueRoomCode(): string
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = '';
        $codeLength = 6;
        $roomRepository = $this->entityManager->getRepository(Room::class);

        do {
            $code = '';
            for ($i = 0; $i < $codeLength; $i++) {
                $code .= $characters[random_int(0, strlen($characters) - 1)];
            }
        } while ($roomRepository->findOneBy(['code' => $code]));

        return $code;
    }
}
