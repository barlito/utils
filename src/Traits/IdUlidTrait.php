<?php

declare(strict_types=1);

namespace Barlito\Utils\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UlidGenerator;
use Symfony\Bridge\Doctrine\Types\UlidType;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Validator\Constraints as Assert;

trait IdUlidTrait
{
    /**
     * Typed Ulid on purpose: with a `?string` property against the UlidType
     * column, Doctrine keeps the ORIGINAL value as a Ulid object while the
     * property holds a string — the changeset then flags `id` as changed on
     * EVERY hydrated entity, and any flush() rewrites every loaded row
     * (`UPDATE ... SET id = <same>, updated_at = <now>`).
     */
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(name: 'id', type: UlidType::NAME, unique: true)]
    #[ORM\CustomIdGenerator(class: UlidGenerator::class)]
    #[Assert\Ulid]
    #[Groups(['default'])]
    private ?Ulid $id = null;

    public function getId(): ?string
    {
        return $this->id?->toBase32();
    }

    public function setId(string $id): self
    {
        $this->id = Ulid::fromString($id);

        return $this;
    }
}
