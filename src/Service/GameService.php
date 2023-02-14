<?php

namespace App\Service;

use Doctrine\Persistence\ManagerRegistry;

use App\Entity\Game;
use App\Entity\GamePlayer;
use App\Entity\GameState;
use App\Entity\SessionUser;

class GameService {
    private $em;
    private $reg;

    public function __construct(ManagerRegistry $reg)
    {
        $this->reg = $reg;
        $this->em = $reg->getManager();
    }
    
    function teamCount($playerCount, $round) {
        static $teamCount = [
            [], [], [], [], [],
            [2, 3, 2, 3, 3],
            [2, 3, 4, 3, 4],
            [2, 3, 3, 4, 4],
            [3, 4, 4, 5, 5],
            [3, 4, 4, 5, 5],
            [3, 4, 4, 5, 5]
        ];
    
        return $teamCount[$playerCount][$round];
    }

    function badPlayerCount($playerCount) {
        static $playerCounts = [
            0, 0, 0, 0, 0, 2, 2, 3, 3, 3, 4
        ];

        return $playerCounts[$playerCount];
    }

    function playerCountValid($playerCount) {
        static $gameCountValid = [
            false,
            false,
            false,
            false,
            false,
            true,
            true,
            true,
            true,
            true,
            true
        ];

        return $gameCountValid[$playerCount];
    }

    public function startRequest($request, $code, $withState = true) {
        $session = $request->getSession();
        $userId = $session->get('id');

        $ret = [];

        if ($userId == 0) {
            throw $this->createNotFoundException('Invalid session, reload home page'); 
        }
        $ret["userId"] = $userId;

        $user = $this->reg->getRepository(SessionUser::class)->find($userId);
        if (!$user) {
            throw $this->createNotFoundException('Invalid user, reload home page');
        }
        $ret["user"] = $user;

        $game = $this->reg->getRepository(Game::class)->findOneBy([
            'code' => $code
        ]);

        if (!$game) {
            throw $this->createNotFoundException('Invalid game code');
        }
        $ret["game"] = $game;

        $players = $game->getPlayers();
        for($i = 0; $i < count($players); $i++) {
            if ($players[$i]->getSessionUser()->getId() == $user->getId()) {
                $ret["player"] = $players[$i];
                $ret["playerIndex"] = $i;
                break;
            }
        }
        $ret["players"] = $players;
        $ret["playerCount"] = count($players);

        if ($withState) {
            $state = $game->getState();
            if (!$state) {
                throw $this->createNotFoundException('Nonexistent game state');
            }
            $ret["state"] = $state;
        }

        return $ret;
    }

    public function createBadPlayerArray($playerCount) {
        $badCount = $this->badPlayerCount($playerCount);

        $badPool = [];
        for ($i = 0; $i < $playerCount; $i++) {
            $badPool[] = $i < $badCount;
        }

        $badPlayers = [];
        for ($i = 0; $i < $playerCount; $i++) {
            $badChoice = rand(0, count($badPool) - 1);
            $badPlayers[] = $badPool[$badChoice];
            array_splice($badPool, $badChoice, 1);
        }

        return $badPlayers;
    }

    public function gameArea($gameInfo, $isLeader, $isOnTeam, $isBad) {
        $round = $gameInfo["game"]->getState()->getRound();
        $playerCount = $gameInfo["playerCount"];
        $teamCount = $this->teamCount($playerCount, $round);

        $message = "Unhandled case!";
        $showTeam = false;
        $inputType = "None";
        $hint = "";

        switch ($gameInfo["state"]->getPhase()) {
            case 'NominateTeam':
                if ($isLeader) {
                    $message = "Select " . $this->teamCount($playerCount, $round) . " players to nominate for the mission.";
                    $inputType = "NominateTeam";
                    
                    if ($isBad) {
                        $hint = "Since you're bad you'll want this mission to fail but if the good players know you're nominating a bad player then they'll reject the mission.";
                    } else {
                        $hint = "Try and nominate good players so the mission succeeds.";
                    }
                } else {
                    $message = "Waiting for leader to nominate team.";
                    if ($isBad) {
                        $hint = "Try to convince the leader to nominate bad players for the mission. " .
                            "However, if good players also know the bad players then they can reject the mission so you need to be sneaky about it.";
                    } else {
                        $hint = "Once the leader nominates the team, try and figure out if there are any bad players on the team.";
                    }
                }
                break;
            
            case 'VoteTeam':
                $message = "Vote whether to approve nominated team for mission.";
                $showTeam = true;
                $inputType = "VoteTeam";

                if ($isBad) {
                    $hint = "Voting to approve a mission team may reveal what side you're on. If you approve a team with known bad players then they'll think you're bad too.";
                } else {
                    $hint = "Try and figure out if any of the players are bad and would cause the mission to fail. " .
                        "However, rejecting a mission too many times in a row will lose the game.";
                }
                break;
            
            case 'TeamRejected':
                $message = "The mission team was rejected.";
                $inputType = "OkayButton";
                if ($isBad) {
                    $hint = "Too many people thought at least one person on the team was bad. " .
                        "Keep getting known bad people on teams to win by too many rejected missions or find out who they think was bad.";
                } else {
                    $hint = "Careful, too many repeated rejections will lose the game. Try and find out who is bad and convince leaders to choose good players.";
                }
                break;
            
            case 'VoteMission':
                $showTeam = true;

                if ($isOnTeam) {
                    $message = "Select whether you want this mission to succeed or fail.";
                    $inputType = "VoteMission";
                    if ($isBad) {
                        $hint = "Don't take too long to vote, it could reveal who you are.";
                    } else {
                        $hint = "Since you're good, you should really just choose to succeed.";
                    }
                } else {
                    $message = "Waiting for the team to succeed or fail the mission.";
                    $hint = "Once the mission is done, discuss to try and figure out what side players are on.";
                }
                break;
            
            case 'MissionSuccess':
                $message = "The mission succeeded! Onto the next mission.";
                $inputType = "OkayButton";
                break;
            
            case 'MissionFailure':
                $message = "The mission failed! The group should discuss to try and figure out why.";
                $inputType = "OkayButton";
                break;
            
            case 'GameLose':
                $message = "The BAD group has won!";
                $hint = "A new game will have to be created.";
                break;
            
            case 'GameWin':
                $message = "The GOOD group has won!";
                $hint = "A new game will have to be created.";
                break;
        }

        return [
            "message" => $message,
            "showTeam" => $showTeam,
            "inputType" => $inputType,
            "hint" => $hint,
            "teamCount" => $teamCount,
            "hasVoted" => !is_null($gameInfo["player"]->getVote()),
            "playerIndex" => $gameInfo["playerIndex"]
        ];
    }

    public function doNominate($state, $teamIndices) {
        $state->setTeamPlayerIndices(json_encode($teamIndices));
        $state->setPhase("VoteTeam");

        $this->em->persist($state);
        $this->em->flush();
    }

    public function playerIndex($game, $userId) {
        $playerIndex = 0;
        $players = $game->getPlayers();
        for($i = 0; $i < count($players); $i++) {
            if ($players[$i]->getSessionUser()->getId() == $userId) {
                return $i;
            }
        }
    }

    public function hasAllVotes($players) {
        $playerCount = count($players);
        $voteCount = 0;
        foreach($players as $p) {
            if (!is_null($p->getVote())) {
                $voteCount += 1;
            }
        }

        return $voteCount == $playerCount;
    }

    public function hasAllMissionVotes($gameInfo) {
        $teamCount = $this->teamCount($gameInfo["playerCount"], $gameInfo["state"]->getRound());
        $voteCount = 0;
        foreach ($gameInfo["players"] as $p) {
            if (!is_null($p->getVote())) {
                $voteCount += 1;
            }
        }

        return $voteCount == $teamCount;
    }

    public function tallyVotes($gameInfo) {
        $players = $gameInfo["players"];
        $tally = 0;
        foreach ($players as $p) {
            if ($p->getVote()) {
                $tally += 1;
            }
        }

        echo $tally;

        return $tally;
    }

    public function checkPhase($gameInfo) {
        $phase = $gameInfo["state"]->getPhase();
        $state = $gameInfo["state"];

        if ($phase == "VoteTeam" && $this->hasAllVotes($gameInfo["players"])) {
            $tally = $this->tallyVotes($gameInfo);
            $toAccept = $gameInfo["playerCount"] / 2;
            $accepted = $tally > $toAccept;
            $nextPhase = $accepted ? "VoteMission" : "TeamRejected";

            if (!$accepted) {
                $rejected = $state->getRejectCount() + 1;
                $state->setRejectCount($rejected);
                if ($rejected == 5) {
                    $nextPhase = "GameLose";
                }
            }

            //clear all votes, then transition to next phase
            $players = $gameInfo["players"];
            foreach ($players as $p) {
                $p->setVote(NULL);
                $this->em->persist($p);
            }

            $gameInfo["state"]->setPhase($nextPhase);
            $this->em->persist($gameInfo["state"]);
            $this->em->flush();
        } else if ($phase == "VoteMission" && $this->hasAllMissionVotes($gameInfo)) {
            $tally = $this->tallyVotes($gameInfo);
            $success = false;

            if ($tally == $this->teamCount($gameInfo["playerCount"], $state->getRound())) {
                $success = true;
            }

            //clear all votes
            $players = $gameInfo["players"];
            foreach ($players as $p) {
                $p->setVote(NULL);
                $this->em->persist($p);
            }

            $results = json_decode($state->getResults());
            $results[] = $success;
            $state->setResults(json_encode($results));
            $state->setRound($state->getRound() + 1);
            $state->setPreviousTeam($state->getTeamPlayerIndices());
            $state->setTeamPlayerIndices("[]");
            $state->setRejectCount(0);

            $winCount = 0;
            $loseCount = 0;
            foreach ($results as $r) {
                if ($r) $winCount++;
                else $loseCount++;
            }

            if ($winCount == 3) {
                $gameInfo["game"]->setCompleted(true);
                $this->em->persist($gameInfo["game"]);
                $state->setPhase("GameWin");
            } else if ($loseCount == 3) {
                $gameInfo["game"]->setCompleted(true);
                $this->em->persist($gameInfo["game"]);
                $state->setPhase("GameLose");
            } else {
                $state->setPhase($success ? "MissionSuccess" : "MissionFailure");
            }
            
            $this->em->persist($state);
            $this->em->flush();
        } else if ( ($phase == "MissionSuccess" || $phase == "MissionFailure") && $this->hasAllVotes($gameInfo["players"])) {
            $leaderIndex = $state->getLeaderIndex() + 1;
            if ($leaderIndex == $gameInfo["playerCount"]) {
                $leaderIndex = 0;
            }

            $state->setLeaderIndex($leaderIndex);
            $this->em->persist($state);
            $this->em->flush();

            //clear all votes
            $players = $gameInfo["players"];
            foreach ($players as $p) {
                $p->setVote(NULL);
                $this->em->persist($p);
            }

            $state->setPhase("NominateTeam");
            $this->em->persist($state);
            $this->em->flush();
        } else if ($phase == "TeamRejected" && $this->hasAllVotes($gameInfo["players"])) {
            $state = $gameInfo["state"];

            //clear all votes
            $players = $gameInfo["players"];
            foreach ($players as $p) {
                $p->setVote(NULL);
                $this->em->persist($p);
            }

            $state->setTeamPlayerIndices("[]");
            $state->setPhase("NominateTeam");
            
            $leaderIndex = $state->getLeaderIndex() + 1;
            if ($leaderIndex == $gameInfo["playerCount"]) {
                $leaderIndex = 0;
            }

            $state->setLeaderIndex($leaderIndex);
            
            $this->em->persist($state);
            $this->em->flush();
        }
    }
}