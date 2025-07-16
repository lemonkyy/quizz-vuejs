<?php

namespace App\Command;

use App\Service\ParseQuizService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:parse-quiz',
    description: 'Parse un quiz existant en DB et enregistre les questions liées.'
)]
class ParseQuizCommand extends Command
{
    public function __construct(
        private ParseQuizService $parseQuizService
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('quizId', InputArgument::REQUIRED, 'ID du quiz à parser');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $quizId = (int) $input->getArgument('quizId');

        $this->parseQuizService->parseAndPersistQuestions($quizId);

        $output->writeln("<info>Parsing and persisting done for quiz ID: $quizId</info>");

        return Command::SUCCESS;
    }
}
