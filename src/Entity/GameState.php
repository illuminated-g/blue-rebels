<?php

namespace App\Entity;

use App\Repository\GameStateRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameStateRepository::class)]
class GameState implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'state', cascade: ['persist', 'remove'])]
    private ?Game $game = null;

    #[ORM\Column(length: 255)]
    private ?string $phase = null;

    #[ORM\Column]
    private ?int $round = null;

    #[ORM\Column]
    private ?int $leaderIndex = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $results = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $teamPlayerIndices = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $isBad = null;

    #[ORM\Column]
    private ?int $rejectCount = 0;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $previousTeam = null;


    public function jsonSerialize(): mixed {
        //Note: only members that are safe to send to clients are serialized, e.g. not isBad
        $res = [
            "phase" => $this->phase,
            "round" => $this->round,
            "leaderIndex" => $this->leaderIndex,
            "results" => json_decode($this->results),
            "teamPlayerIndices" => json_decode($this->teamPlayerIndices),
            "previousTeam" => json_decode($this->previousTeam),
            "rejectedCount" => $this->rejectCount
        ];

        $results = json_decode($this->results);
        $resultCount = count($results);
        $successCount = 0;
        for ($i = 0; $i < $resultCount; $i++) {
            if ($results[$i]) $successCount++;
        }
        $failureCount = $resultCount - $successCount;

        $res["failures"] = $failureCount;
        $res["successes"] = $successCount;

        return $res;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPhase(): ?string
    {
        return $this->phase;
    }

    public function setPhase(string $phase): self
    {
        $this->phase = $phase;

        return $this;
    }

    public function getRound(): ?int
    {
        return $this->round;
    }

    public function setRound(int $round): self
    {
        $this->round = $round;

        return $this;
    }

    public function getLeaderIndex(): ?int
    {
        return $this->leaderIndex;
    }

    public function setLeaderIndex(int $leaderIndex): self
    {
        $this->leaderIndex = $leaderIndex;

        return $this;
    }

    public function getResults(): ?string
    {
        return $this->results;
    }

    public function setResults(string $results): self
    {
        $this->results = $results;

        return $this;
    }

    public function getTeamPlayerIndices(): ?string
    {
        return $this->teamPlayerIndices;
    }

    public function setTeamPlayerIndices(string $teamPlayerIndices): self
    {
        $this->teamPlayerIndices = $teamPlayerIndices;

        return $this;
    }

    public function getIsBad(): ?string
    {
        return $this->isBad;
    }

    public function setIsBad(string $isBad): self
    {
        $this->isBad = $isBad;

        return $this;
    }

    public function getRejectCount(): ?int
    {
        return $this->rejectCount;
    }

    public function setRejectCount(int $rejectCount): self
    {
        $this->rejectCount = $rejectCount;

        return $this;
    }

    public function getPreviousTeam(): ?string
    {
        return $this->previousTeam;
    }

    public function setPreviousTeam(string $previousTeam): self
    {
        $this->previousTeam = $previousTeam;

        return $this;
    }
}
