<?php
// src/Command/TestPersistQuizCommand.php
namespace App\Command;

use App\Entity\Quizzes;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:test-persist-quiz',
    description: 'Test direct pour vérifier persist Quizzes'
)]
class TestPersistQuizCommand extends Command
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $quiz = new Quizzes();
        $quiz->setContentJson(json_encode(['hello' => 'world']));

        $this->entityManager->persist($quiz);
        $this->entityManager->flush();

        $output->writeln('Inserted quiz with id: '.$quiz->getId());
        return Command::SUCCESS;
    }
}
