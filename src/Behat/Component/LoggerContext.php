<?php

declare(strict_types=1);

namespace Barlito\Utils\Behat\Component;

use Barlito\Utils\Behat\Mock\LoggerMock;
use Behat\Behat\Context\Context;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class LoggerContext extends KernelTestCase implements Context
{
    public function __construct(
        private readonly LoggerMock $logger
    ) {
        parent::__construct('Logger Behat Context');
    }

    /**
     * @Then the logger logged the error with message :message
     */
    public function theLoggerLoggedTheErrorWithMessage(string $message): void
    {
        $this->assertNotNull(
            $this->logger->getLoggedMessage($message), "Error with message '" . $message . "' is not logged by the logger"
        );
    }

    /**
     * @Then the logger logged an error containing :message
     */
    public function theLoggerLoggedAnErrorContaining(string $message): void
    {
        $this->assertNotNull(
            $this->logger->containsLoggedMessage($message), "Error with message '" . $message . "' is not logged by the logger"
        );
    }

    /**
     * @BeforeScenario
     */
    public function flushLogger(): void
    {
        $this->logger->reset();
    }
}
