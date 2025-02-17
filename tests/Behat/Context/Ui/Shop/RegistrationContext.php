<?php

declare(strict_types=1);

namespace Tests\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Webmozart\Assert\Assert;

final readonly class RegistrationContext implements Context
{
    public function __construct(
        private NotificationCheckerInterface $notificationChecker,
        private UrlGeneratorInterface $urlGenerator,
        private RepositoryInterface $shopUserRepository
    ) {
    }

    /**
     * @Then I should have account verification link for :email displayed in flash message
     */
    public function iShouldHaveAccountVerificationLinkDisplayedInFlashMessage(string $email): void
    {
        /** @var ShopUserInterface $shopUser */
        $shopUser = $this->shopUserRepository->findOneBy(['username' => $email]);
        Assert::notNull($shopUser);

        $verificationLink = $this->urlGenerator->generate(
            'sylius_shop_user_verification',
            ['_locale' => 'en_US', 'token' => $shopUser->getEmailVerificationToken()]
        );

        $this->notificationChecker->checkNotification(
            sprintf(
                'For demo purposes you can visit https://127.0.0.1:8080%s to verify the account.',
                $verificationLink
            ),
            NotificationType::success()
        );
    }
}
