<?php

namespace App\EventListener;

use App\Event\Admin\ModeratorCreatedEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Mailer\MailerInterface;

final readonly class ModeratorCreatedListener
{
    public function __construct(
        private MailerInterface $mailer,
    )
    {
    }

    #[AsEventListener(event: ModeratorCreatedEvent::class)]
    public function onModeratorCreated(ModeratorCreatedEvent $event): void
    {
        $this->mailer->send(
            (new TemplatedEmail())
            ->from('contact@velo-cite.org')
            ->to($event->getModerator()->getEmail())
            ->subject('Création de compte modérateur sur les Cyclofiches')
            ->htmlTemplate('email/moderatorCreated.html.twig')
            ->context([
                'organisationUser' => $event->getModerator(),
            ])
        );

    }
}
