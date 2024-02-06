<?php


declare(strict_types=1);

namespace App\Service;


use App\Dto\ExchangeRateWithThresholdChange;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

readonly class NotifierService
{
    public function __construct(
        private MailerInterface $mailer,
        private ParameterBagInterface $parameterBag
    ) {
    }

    /**
     * @param  ExchangeRateWithThresholdChange[]  $changes
     * @param  string  $subject
     * @return void
     * @throws TransportExceptionInterface
     */
    public function sendExchangeRateChanges(array $changes, string $subject): void
    {
        $email = (new TemplatedEmail())
            ->from($this->parameterBag->get('MAILER_NO_REPLY_EMAIL'))
            ->to(new Address($this->parameterBag->get('EXCHANGE_RATE_MESSAGE_RECEIVER')))
            ->subject($subject)
            ->htmlTemplate('email/changes-exchange-rate.html.twig')
            ->context([
                'changes' => $changes,
            ]);
        $this->mailer->send($email);
    }
}