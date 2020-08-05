<?php


namespace JulienIts\EmailsQueueBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

/**
 * A console command that process the emails queue
 *
 * To use this command, open a terminal window, enter into your project directory
 * and execute the following:
 *
 *     $ php bin/console jits:queue:process --limit 30
 *
 *
 * @author Julien Gustin
 * Usage exemple : php bin/console jits:queue:process --limit 30
 */
class EmailsCommand extends Command
{
    protected static $defaultName = 'jits:queue:process';

    // Services
    protected $avService;

    // Command options
    private $_limit;

    public function __construct(\JulienIts\EmailsQueueBundle\Services\EmailsQueueService $emailsQueueService)
    {
        $this->emailsQueueService = $emailsQueueService;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName(EmailsCommand::$defaultName)
            ->setDescription('Process the email queue')
            ->setHelp('This command can be used to process the email queue')
            ->addOption('limit', 'l', InputOption::VALUE_REQUIRED, 'Limit')
        ;
    }

    /**
     * This method is executed after initialize(). It contains the logic
     * to execute to complete this command task.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->_limit = $input->getOption('limit');

        $this->emailsQueueService->processQueue($this->_limit);

        $output->writeLn('Queue processed');
        return 0;

    }
}
