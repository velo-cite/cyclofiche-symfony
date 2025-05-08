<?php

namespace App\EventListener;

use App\Event\OrganisationUserAddedEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Mailer\MailerInterface;

final class OrganisationUserCreatedListener
{
    public function __construct(private MailerInterface $mailer)
    {

    }

    #[AsEventListener(event: OrganisationUserAddedEvent::class)]
    public function onOrganisationUserCreated(OrganisationUserAddedEvent $event): void
    {
        $this->mailer->send(
            (new TemplatedEmail())
            ->from('contact@velo-cite.org')
            ->to($event->getOrganisationUser()->getEmail())
            ->subject('Création de compte sur les Cyclofiches')
            ->htmlTemplate('email/organisationUserCreated.html.twig')
            ->context([
                'organisationUser' => $event->getOrganisationUser(),
            ])
        );
    }
}
