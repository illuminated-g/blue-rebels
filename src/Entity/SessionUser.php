<?php

namespace App\Entity;

use App\Repository\SessionUserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SessionUserRepository::class)]
class SessionUser
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'sessionUser', targetEntity: GamePlayer::class)]
    private Collection $game_players;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $default_name = null;

    public function __construct()
    {
        $this->game_players = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, GamePlayer>
     */
    public function getGamePlayers(): Collection
    {
        return $this->game_players;
    }

    public function addGamePlayer(GamePlayer $gamePlayer): self
    {
        if (!$this->game_players->contains($gamePlayer)) {
            $this->game_players->add($gamePlayer);
            $gamePlayer->setSessionUser($this);
        }

        return $this;
    }

    public function removeGamePlayer(GamePlayer $gamePlayer): self
    {
        if ($this->game_players->removeElement($gamePlayer)) {
            // set the owning side to null (unless already changed)
            if ($gamePlayer->getSessionUser() === $this) {
                $gamePlayer->setSessionUser(null);
            }
        }

        return $this;
    }

    public function getDefaultName(): ?string
    {
        return $this->default_name;
    }

    public function setDefaultName(?string $default_name): self
    {
        $this->default_name = $default_name;

        return $this;
    }
}
