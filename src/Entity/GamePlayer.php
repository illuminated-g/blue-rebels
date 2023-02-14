<?php

namespace App\Entity;

use App\Repository\GamePlayerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GamePlayerRepository::class)]
class GamePlayer implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = "";

    #[ORM\ManyToOne(inversedBy: 'game_players')]
    private ?SessionUser $sessionUser = null;

    #[ORM\ManyToOne(inversedBy: 'players')]
    private ?Game $game = null;

    #[ORM\OneToOne(mappedBy: 'player', cascade: ['persist', 'remove'])]
    private ?GamePlayerRole $role = null;

    #[ORM\Column(nullable: true)]
    private ?bool $vote = null;

    public function jsonSerialize(): mixed {
        //Note, session user ID is never sent to clients, that should remain secret
        // on the server.
        $res = [
            'id' => $this->id,
            'name' => $this->name
        ];

        return $res;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSessionUser(): ?SessionUser
    {
        return $this->sessionUser;
    }

    public function setSessionUser(?SessionUser $sessionUser): self
    {
        $this->sessionUser = $sessionUser;

        return $this;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): self
    {
        $this->game = $game;

        return $this;
    }

    public function getRole(): ?GamePlayerRole
    {
        return $this->role;
    }

    public function setRole(?GamePlayerRole $role): self
    {
        // unset the owning side of the relation if necessary
        if ($role === null && $this->role !== null) {
            $this->role->setPlayer(null);
        }

        // set the owning side of the relation if necessary
        if ($role !== null && $role->getPlayer() !== $this) {
            $role->setPlayer($this);
        }

        $this->role = $role;

        return $this;
    }

    public function getVote(): ?bool
    {
        return $this->vote;
    }

    public function setVote(?bool $vote): self
    {
        $this->vote = $vote;

        return $this;
    }
}
