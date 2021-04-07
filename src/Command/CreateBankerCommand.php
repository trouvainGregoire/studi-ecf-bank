<?php

namespace App\Command;

use App\Entity\Banker;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class CreateBankerCommand extends Command
{
    protected static $defaultName = 'app:create-banker';
    protected static $defaultDescription = 'Add a short description for your command';
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    /**
     * @var EncoderFactoryInterface
     */
    private EncoderFactoryInterface $encoderFactory;

    public function __construct(EntityManagerInterface $entityManager, EncoderFactoryInterface $encoderFactory)
    {
        $this->entityManager = $entityManager;
        $this->encoderFactory = $encoderFactory;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Create a new banker.')
            ->addArgument('email', InputArgument::REQUIRED, 'Banker email')
            ->addArgument('password', InputArgument::REQUIRED, 'Banker password')
            ->setHelp('This command allows you to create a banker.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$output instanceof ConsoleOutputInterface) {
            throw new LogicException('This command accepts only an instance of "ConsoleOutputInterface".');
        }

        $em = $this->entityManager;

        $em->getConnection()->getConfiguration()->setSQLLogger(null);

        $banker = new Banker();

        $encoder = $this->encoderFactory->getEncoder($banker);

        $banker->setEmail($input->getArgument('email'))
            ->setPassword($encoder->encodePassword($input->getArgument('password'), $banker->getSalt()));

        $em->persist($banker);
        $em->flush();


        $output->writeln([
            'Banker Creator',
            '============',
            '',
        ]);

        $output->writeln('Banker successfully generated!');


        return Command::SUCCESS;
    }
}
