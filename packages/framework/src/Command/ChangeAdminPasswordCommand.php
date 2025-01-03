<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Command;

use Exception;
use Shopsys\FrameworkBundle\Model\Administrator\AdministratorFacade;
use Shopsys\FrameworkBundle\Model\Administrator\AdministratorPasswordFacade;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'shopsys:administrator:change-password',
    description: 'Set new password for administrator.',
)]
class ChangeAdminPasswordCommand extends Command
{
    private const ARG_USERNAME = 'username';

    /**
     * @param \Shopsys\FrameworkBundle\Model\Administrator\AdministratorFacade $administratorFacade
     * @param \Shopsys\FrameworkBundle\Model\Administrator\AdministratorPasswordFacade $administratorPasswordFacade
     */
    public function __construct(
        private readonly AdministratorFacade $administratorFacade,
        private readonly AdministratorPasswordFacade $administratorPasswordFacade,
    ) {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->addArgument(self::ARG_USERNAME, InputArgument::REQUIRED, 'Existing administrator username');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $adminUsername = $input->getArgument(self::ARG_USERNAME);
        $password = $this->askRepeatedlyForNewPassword($input, $io);

        $administrator = $this->administratorFacade->getByUserName($adminUsername);
        $this->administratorPasswordFacade->setPassword($administrator, $password);

        $output->writeln(sprintf('Password for administrator "%s" was successfully changed', $adminUsername));

        return Command::SUCCESS;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Style\SymfonyStyle $io
     * @return string
     */
    private function askRepeatedlyForNewPassword(InputInterface $input, SymfonyStyle $io)
    {
        $question = new Question('Enter new password');
        $question->setHidden(true);
        $question->setHiddenFallback(false);
        $question->setValidator(function ($password) use ($io) {
            if ($password === null) {
                throw new Exception('The password cannot be empty');
            }

            $repeatQuestion = new Question('Repeat the password');
            $repeatQuestion->setHidden(true);
            $repeatQuestion->setHiddenFallback(false);
            $repeatQuestion->setValidator(function ($repeatedPassword) use ($password) {
                if ($repeatedPassword !== $password) {
                    throw new Exception('Passwords do not match');
                }

                return $repeatedPassword;
            });
            $repeatQuestion->setMaxAttempts(1);

            return $io->askQuestion($repeatQuestion);
        });
        $question->setMaxAttempts(3);

        $password = $io->askQuestion($question);

        // Workaround for QuestionHelper that does not run validation in non-interactive mode
        // See: https://github.com/symfony/symfony/issues/23211
        if (!$input->isInteractive() && $password === null) {
            throw new Exception('The password cannot be empty. Please run this command in interactive mode.');
        }

        return $password;
    }
}
