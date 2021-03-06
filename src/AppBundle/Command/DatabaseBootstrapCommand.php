<?php

namespace AppBundle\Command;

use AppBundle\Entity\Message;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * Class DatabaseBootstrapCommand.
 */
class DatabaseBootstrapCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('database:bootstrap')
            ->setDescription('Bootstraps the testing database.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        echo $this->dropSchema();

        echo $this->createSchema();

        $output->writeln([
            ' Creating sample data...',
            '',
        ]);

        $em = $this->getContainer()->get('doctrine')->getEntityManager();

        $alice = new User;
        $alice->setUsername('alice');
        $alice->setPassword('password');

        $bob = new User;
        $bob->setUsername('bob');
        $bob->setPassword('password');

        $foo = new Message;
        $foo->setTel('07123456789');
        $foo->setContent('foo');
        $foo->setAuthor($alice);
        $foo->setStatus(Message::STATUS_QUEUED);

        $bar = new Message;
        $bar->setTel('07012345678');
        $bar->setContent('bar');
        $bar->setAuthor($bob);
        $bar->setStatus(Message::STATUS_QUEUED);

        $em->persist($alice);
        $em->persist($bob);
        $em->persist($foo);
        $em->persist($bar);
        $em->flush();

        $output->writeln([
            ' [OK] Sample data created successfully!',
        ]);
    }

    protected function dropSchema()
    {
        $process = new Process('php vendor/bin/doctrine orm:schema-tool:drop --force');
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process->getOutput();
    }

    protected function createSchema()
    {
        $process = new Process('php vendor/bin/doctrine orm:schema-tool:create');
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process->getOutput();
    }
}
