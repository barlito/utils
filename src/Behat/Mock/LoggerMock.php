<?php

declare(strict_types=1);

namespace Barlito\Utils\Behat\Mock;

use Psr\Log\LoggerInterface;
use Stringable;

/**
 * @phpstan-type LoggedMessage array{message: string | Stringable, context: array<mixed>, level: mixed}
 *
 * @SuppressWarnings("PHPMD.TooManyPublicMethods")
 */
class LoggerMock implements LoggerInterface
{
    /** @var list<LoggedMessage> */
    private static array $loggedMessages = [];

    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function error(string | \Stringable $message, array $context = []): void
    {
        $this->addLoggedMessage($message, $context, 'error');
        $this->logger->error($message, $context);
    }

    /**
     * @return list<LoggedMessage>
     */
    public function getLoggedMessages(): array
    {
        return self::$loggedMessages;
    }

    /**
     * @return LoggedMessage|null
     */
    public function getLoggedMessage(string $message): ?array
    {
        foreach (self::$loggedMessages as $loggedMessage) {
            if ($message === (string) $loggedMessage['message']) {
                return $loggedMessage;
            }
        }

        return null;
    }

    /**
     * @return LoggedMessage|null
     */
    public function containsLoggedMessage(string $message): ?array
    {
        foreach (self::$loggedMessages as $loggedMessage) {
            if (str_contains((string) $loggedMessage['message'], $message)) {
                return $loggedMessage;
            }
        }

        return null;
    }

    public function emergency(string | \Stringable $message, array $context = []): void
    {
        $this->addLoggedMessage($message, $context, 'emergency');
        $this->logger->emergency($message, $context);
    }

    public function alert(string | \Stringable $message, array $context = []): void
    {
        $this->addLoggedMessage($message, $context, 'alert');
        $this->logger->alert($message, $context);
    }

    public function critical(string | \Stringable $message, array $context = []): void
    {
        $this->addLoggedMessage($message, $context, 'critical');
        $this->logger->critical($message, $context);
    }

    public function warning(string | \Stringable $message, array $context = []): void
    {
        $this->addLoggedMessage($message, $context, 'warning');
        $this->logger->warning($message, $context);
    }

    public function notice(string | \Stringable $message, array $context = []): void
    {
        $this->addLoggedMessage($message, $context, 'notice');
        $this->logger->notice($message, $context);
    }

    public function info(string | \Stringable $message, array $context = []): void
    {
        $this->addLoggedMessage($message, $context, 'info');
        $this->logger->info($message, $context);
    }

    public function debug(string | \Stringable $message, array $context = []): void
    {
        $this->addLoggedMessage($message, $context, 'debug');
        $this->logger->debug($message, $context);
    }

    public function log($level, string | \Stringable $message, array $context = []): void
    {
        $this->addLoggedMessage($message, $context, $level);
        $this->logger->log($level, $message, $context);
    }

    public function reset(): void
    {
        self::$loggedMessages = [];
    }

    /**
     * @param array<mixed> $context
     */
    private function addLoggedMessage(string | \Stringable $message, array $context, mixed $level): void
    {
        self::$loggedMessages[] = [
            'message' => $message,
            'context' => $context,
            'level' => $level,
        ];
    }
}
