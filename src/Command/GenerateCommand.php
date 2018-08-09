<?php
declare(strict_types=1);

namespace Metamorph\Command;

use Metamorph\Context\TransformerType;
use Metamorph\Factory\MetamorphConfigFactory;
use Metamorph\Generator\TransformerGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Zend\Config\Factory;

final class GenerateCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('generate')
            ->setDescription('Generate transformers')
            ->setHelp('Generate transformers from configuration')
            ->addOption(
                'path',
                'p',
                InputOption::VALUE_REQUIRED,
                'The location of the config files',
                getcwd() . '/resources/metamorph/'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configPath = realpath($input->getOption('path')) . '/';
        $configData = Factory::fromFiles(glob($configPath . '*.*'));
        $config = (new MetamorphConfigFactory)($configData);
        $generator = new TransformerGenerator($config);

        $usages = $config['_usage'];
        foreach ($usages as $from => $uses) {
            foreach ($uses as $to => $types) {
                foreach ($types as $type) {
                    $transformerType = (new TransformerType())
                        ->setFrom($from)
                        ->setTo($to)
                        ->setType($type);
                    $generator->generateType($transformerType);
                    $message = "From $from to $to for $type written";
                    $output->writeln($message);
                }
            }
        }
    }
}
