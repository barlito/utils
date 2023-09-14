<?php

declare(strict_types=1);

namespace Barlito\Utils\Behat\Component;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use JsonException;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\PropertyAccess\PropertyAccess;

final class EntityManagerContext extends TestCase implements Context
{
    public function __construct(protected EntityManagerInterface $entityManager, protected string $entityNamespace)
    {
        parent::__construct('EntityManager Behat Context');
    }

    /**
     * @Given a :entityClass entity found by :findByQueryString should match:
     */
    public function aEntityFoundByShouldMatch(string $entityClass, string $findByQueryString, TableNode $table): void
    {
        $findBy = $this->parseFindByQueryString($findByQueryString);
        $this->entityManager->clear();
        $entity = $this->getRepository($entityClass)->findOneBy($findBy);
        $this->valueShouldMatch($entity, $table);
    }

    /**
     * @Given a :entityClass entity found by :findByQueryString should not exist
     */
    public function aEntityFoundByShouldNotBeFound(string $entityClass, string $findByQueryString): void
    {
        $findBy = $this->parseFindByQueryString($findByQueryString);
        $this->entityManager->clear();
        $entity = $this->getRepository($entityClass)->findOneBy($findBy);
        $this->assertNull($entity, 'Entity found.');
    }

    private function valueShouldMatch(object $entity, TableNode $table): void
    {
        foreach ($table->getRowsHash() as $path => $expected) {
            $this->assertRow($path, $expected, $entity);
        }
    }

    private function assertRow(string $path, mixed $expected, mixed $entity): void
    {
        $assert = 'assertEquals';

        $actualValue = $this->getValueAtPath($entity, $path);

        $callable = [$this, $assert];
        if (!\is_callable($callable)) {
            return;
        }

        $callable($expected, $actualValue, sprintf(
            "The element '%s' value '%s' is not equal to expected '%s'",
            $path,
            $this->getAsString($actualValue),
            $this->getAsString($expected),
        ));
    }

    protected function parseFindByQueryString(string $findByQueryString): array
    {
        parse_str($findByQueryString, $findBy);

        foreach ($findBy as $key => $value) {
            $type = null;
            if (str_contains($key, ':')) {
                $parts = explode(':', $key);
                if (2 !== \count($parts)) {
                    throw new RuntimeException(
                        sprintf(
                            'Invalid type identifier given to look for an entity "%s"',
                            $key,
                        ),
                    );
                }

                unset($findBy[$key]);

                $key = $parts[0];
                $type = $parts[1];
            }

            $findBy[$key] = $this->handleQueryStringTypeHinting($value, $type);
        }

        return $findBy;
    }

    private function handleQueryStringTypeHinting(mixed $value, string $type = null): mixed
    {
        if ('null' === $value) {
            return null;
        }

        return match ($type) {
            'date' => new DateTime($value),
            default => $value,
        };
    }

    protected function getRepository(string $entityClass): ObjectRepository
    {
        return $this->entityManager->getRepository($this->entityNamespace . '\\' . $entityClass);
    }

    /**
     * @param mixed $input
     * @throws JsonException
     */
    private function getAsString($input): string
    {
        if ($input instanceof DateTimeInterface) {
            return $input->format(DATE_ATOM);
        }

        return \is_array($input) && false !== json_encode($input, JSON_THROW_ON_ERROR) ?
            json_encode($input, JSON_THROW_ON_ERROR) :
            (string) $input
        ;
    }

    /**
     * @param mixed $entity
     * @param string $path
     * @return mixed|null
     */
    private function getValueAtPath(mixed $entity, string $path): mixed
    {
        return PropertyAccess::createPropertyAccessorBuilder()
            ->enableExceptionOnInvalidIndex()
            ->getPropertyAccessor()
            ->getValue($entity, $path)
        ;
    }
}
