<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameRepository::class)]
class Game implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?GamePlayer $creator = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\OneToMany(mappedBy: 'game', targetEntity: GamePlayer::class)]
    private Collection $players;

    #[ORM\Column]
    private ?bool $started = false;

    #[ORM\Column]
    private ?bool $completed = false;

    #[ORM\OneToOne(mappedBy: 'game', cascade: ['persist', 'remove'])]
    private ?GameState $state = null;

    public function __construct()
    {
        $this->players = new ArrayCollection();
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

    public function getCreator(): ?GamePlayer
    {
        return $this->creator;
    }

    public function setCreator(?GamePlayer $creator): self
    {
        $this->creator = $creator;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function jsonSerialize(): mixed {
        $res = [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'started' => $this->started,
            'completed' => $this->completed,
            'players' => [],
            'creator' => $this->creator->jsonSerialize()
        ];

        $players = $this->getPlayers();
        foreach($players as $player) {
            $res['players'][] = $player->jsonSerialize();
        }

        return $res;
    }

    /**
     * @return Collection<int, GamePlayer>
     */
    public function getPlayers(): Collection
    {
        return $this->players;
    }

    public function addPlayer(GamePlayer $player): self
    {
        if (!$this->players->contains($player)) {
            $this->players->add($player);
            $player->setGame($this);
        }

        return $this;
    }

    public function removePlayer(GamePlayer $player): self
    {
        if ($this->players->removeElement($player)) {
            // set the owning side to null (unless already changed)
            if ($player->getGame() === $this) {
                $player->setGame(null);
            }
        }

        return $this;
    }

    public function isStarted(): ?bool
    {
        return $this->started;
    }

    public function setStarted(bool $started): self
    {
        $this->started = $started;

        return $this;
    }

    public function isCompleted(): ?bool
    {
        return $this->completed;
    }

    public function setCompleted(bool $completed): self
    {
        $this->completed = $completed;

        return $this;
    }

    public function getState(): ?GameState
    {
        return $this->state;
    }

    public function setState(?GameState $state): self
    {
        // unset the owning side of the relation if necessary
        if ($state === null && $this->state !== null) {
            $this->state->setGame(null);
        }

        // set the owning side of the relation if necessary
        if ($state !== null && $state->getGame() !== $this) {
            $state->setGame($this);
        }

        $this->state = $state;

        return $this;
    }
}
