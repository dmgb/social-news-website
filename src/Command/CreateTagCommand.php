<?php declare(strict_types=1);

namespace App\Command;

use App\Entity\Tag;
use App\Repository\TagRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateTagCommand extends Command
{
    protected static $defaultName = 'snw:create-tag';

    public function __construct(
        private TagRepository $repository,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $initialQuestion = new Question('Enter a name of a new tag: ');
        $tagName = $this->getHelper('question')->ask($input, $output, $initialQuestion);
        while (null !== $this->repository->findOneBy(['name' => $tagName])) {
            $question = new Question('Tag with this name already exists, try another name: ');
            $tagName = $this->getHelper('question')->ask($input, $output, $question);
        }

        $tag = new Tag($tagName);
        $this->repository->save($tag);
        $io = new SymfonyStyle($input, $output);
        $io->success('Tag successfully created.');

        return Command::SUCCESS;
    }
}
