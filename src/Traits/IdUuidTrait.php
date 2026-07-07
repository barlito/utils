<?php

declare(strict_types=1);

namespace Barlito\Utils\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

trait IdUuidTrait
{
    /**
     * Typed Uuid on purpose: with a `?string` property against the UuidType
     * column, Doctrine keeps the ORIGINAL value as a Uuid object while the
     * property holds a string — the changeset then flags `id` as changed on
     * EVERY hydrated entity, and any flush() rewrites every loaded row
     * (`UPDATE ... SET id = <same>, updated_at = <now>`).
     */
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(name: 'id', type: UuidType::NAME, unique: true)]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Assert\Uuid]
    #[Groups(['default'])]
    private ?Uuid $id = null;

    public function getId(): ?string
    {
        return $this->id?->toRfc4122();
    }

    public function setId(string $id): self
    {
        $this->id = Uuid::fromString($id);

        return $this;
    }
}
