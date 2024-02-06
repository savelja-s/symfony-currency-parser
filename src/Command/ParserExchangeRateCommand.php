<?php

namespace App\Command;

use App\Enum\BankEnum;
use App\Enum\CurrencyEnum;
use App\Service\NotifierService;
use App\Service\ParserService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

#[AsCommand(
    name: 'parser:exchange-rate',
    description: 'Parser exchange rate from privat24, monobank',
)]
class ParserExchangeRateCommand extends Command
{
    public function __construct(protected ParserService $parserService, protected NotifierService $notifierService)
    {
        parent::__construct();
    }

    public function getDescription(): string
    {
        return 'Parser exchange rate ('.implode(',', CurrencyEnum::names()).') from privat24, monobank';
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'banks',
                'b',
                InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
                'Banks that need to be parsed.',
                [BankEnum::PRIVAT_BANK, BankEnum::MONOBANK]
            )
            ->addOption(
                'currencies',
                'c',
                InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
                'Currencies that need to be parsed.',
                [CurrencyEnum::EUR, CurrencyEnum::USD]
            );
    }

    /**
     * @throws \Exception
     * @throws TransportExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $banks = $input->getOption('banks');
        $currencies = $input->getOption('currencies');
        $changes = $this->parserService->getChanges($banks, $currencies);
        if (!$changes) {
            $io->note('There are no changes in exchange rates.');
            return self::SUCCESS;
        }
        $this->notifierService->sendExchangeRateChanges($changes,'Sent message with changes in Exchange Rate.');
        $io->success('Sent message with changes in Exchange Rate.');
        return self::SUCCESS;
    }
}
