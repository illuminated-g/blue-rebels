<?php

namespace App\Entity;

use App\Repository\GamePlayerRoleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GamePlayerRoleRepository::class)]
class GamePlayerRole
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'role', cascade: ['persist', 'remove'])]
    private ?GamePlayer $player = null;

    #[ORM\Column(length: 255)]
    private ?string $role = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlayer(): ?GamePlayer
    {
        return $this->player;
    }

    public function setPlayer(?GamePlayer $player): self
    {
        $this->player = $player;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }
}
