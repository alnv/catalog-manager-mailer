<?php

namespace Alnv\CatalogManagerMailerBundle\NotificationCenter;

use Terminal42\NotificationCenterBundle\NotificationType\NotificationTypeInterface;
use Terminal42\NotificationCenterBundle\Token\Definition\AnythingTokenDefinition;
use Terminal42\NotificationCenterBundle\Token\Definition\Factory\TokenDefinitionFactoryInterface;

class MailerDefault implements NotificationTypeInterface
{

    public const NAME = 'DEFAULT_MAILER';

    public function __construct(private TokenDefinitionFactoryInterface $factory)
    {
        //
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getTokenDefinitions(): array
    {
        return [
            $this->factory->create(AnythingTokenDefinition::class, 'recipient', 'mailer.recipient'),
            $this->factory->create(AnythingTokenDefinition::class, 'admin_email', 'mailer.admin_email'),
            $this->factory->create(AnythingTokenDefinition::class, 'raw_*', 'mailer.raw_*'),
            $this->factory->create(AnythingTokenDefinition::class, 'clean_*', 'mailer.clean_*'),
            $this->factory->create(AnythingTokenDefinition::class, 'post_raw_*', 'mailer.post_raw_*'),
            $this->factory->create(AnythingTokenDefinition::class, 'post_old_raw_*', 'mailer.post_old_raw_*'),
            $this->factory->create(AnythingTokenDefinition::class, 'post_old_clean_*', 'mailer.post_old_clean_*'),
            $this->factory->create(AnythingTokenDefinition::class, 'post_type', 'mailer.post_type'),
            $this->factory->create(AnythingTokenDefinition::class, 'reminder_attachment', 'mailer.reminder_attachment')
        ];
    }
}