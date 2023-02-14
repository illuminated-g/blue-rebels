<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\Persistence\ManagerRegistry;

use App\Entity\SessionUser;
use App\Entity\GamePlayer;
use App\Entity\Game;
use App\Entity\GameState;

use App\Service\GameService;

class GameController extends AbstractController
{
    const ALPHABET = "abcdefghijklmnopqrstuvwxyz";

    private $em;
    private $reg;
    private $gs;

    public function __construct(ManagerRegistry $reg, GameService $gs)
    {
        $this->reg = $reg;
        $this->em = $reg->getManager();
        $this->gs = $gs;
    }

    #[Route('/g/{code}', name:'quick_join')]
    public function quickJoin(Request $request, $code) {
        $session = $request->getSession();
        $userId = $session->get('id', 0);

        $sesUser;

        if ($userId == 0) {
            //need to create a player entry in the DB
            $session->start();
            $sesUser = new SessionUser();
            $this->em->persist($sesUser);
            $this->em->flush();
        } else {
            $sesUser = $this->reg->getRepository(SessionUser::class)->find($userId);
            if (!$sesUser) {
                $sesUser = new SessionUser();
                $em->persist($sesUser);
                $em->flush();
            }
        }

        $userId = $sesUser->getId();
        $session->set('id', $userId);

        if ($userId = 0) {
            return new Response("Unable to create session, ensure cookies are enabled and refresh.");
        }

        $this->join($request, $code);

        return $this->redirect('/game/' . $code . '/lobby');
    }

    #[Route('/game/create', name: 'create_game')]
    public function create(Request $request): JsonResponse
    {
        $r = $request->toArray();
        $name = htmlspecialchars($r['name']);
        $session = $request->getSession();

        $userId = $session->get('id');
        if ($userId == 0) {
            throw $this->createNotFoundException('Invalid session, reload home page'); 
        }

        $user = $this->reg->getRepository(SessionUser::class)->find($userId);
        if (!$user) {
            throw $this->createNotFoundException('Invalid user, reload home page');
        }

        $player = new GamePlayer();
        $player->setSessionUser($user);

        $code = "";
        $makeCode = true;

        while ($makeCode) {
            for ($i = 0; $i < 8; $i++) {
                $n = rand(0, strlen(GameController::ALPHABET) - 1);
                $code .= substr(GameController::ALPHABET, $n, 1);
            }

            //make sure code isn't already used
            $game = $this->reg->getRepository(Game::class)->findOneBy([
                'code' => $code
            ]);

            if(!$game) {
                $makeCode = false;
            }
        }

        $game = new Game();
        $game->setCreator($player);
        $game->setName($name);

        $player->setGame($game);
        $game->setCode($code);

        $this->em->persist($player);
        $this->em->persist($game);
        $this->em->flush();

        return new JsonResponse([
            'code' => $game->getCode()
        ]);
    }

    #[Route('/game/{code}/join', name: 'game_join')]
    public function join(Request $request, $code): JsonResponse
    {
        $session = $request->getSession();

        $userId = $session->get('id');
        if ($userId == 0) {
            throw $this->createNotFoundException('Invalid session, reload home page'); 
        }

        $user = $this->reg->getRepository(SessionUser::class)->find($userId);
        if (!$user) {
            throw $this->createNotFoundException('Invalid user, reload home page');
        }

        $game = $this->reg->getRepository(Game::class)->findOneBy([
            'code' => $code
        ]);

        if (!$game) {
            throw $this->createNotFoundException('Invalid game code');
        }

        $player = new GamePlayer();
        $player->setGame($game);
        $player->setSessionUser($user);
        $game->addPlayer($player);

        $this->em->persist($player);
        $this->em->persist($game);
        $this->em->flush();

        return new JsonResponse(["msg" => "OK"]);
    }

    #[Route('/game/{code}/lobby', name: 'game_lobby')]
    public function lobby(Request $request, $code): Response
    {
        $this->gs->startRequest($request, $code, false);

        return $this->render('game/lobby.html.twig', [
            'code' => $code
        ]);
    }

    #[Route('/game/{code}/info', name:'game_info')]
    function info(Request $request, $code) : JsonResponse
    {
        $gameInfo = $this->gs->startRequest($request, $code, false);

        return new JsonResponse([
            'game' => $gameInfo["game"],
            'player' => $gameInfo["player"]
        ]);
    }

    #[Route('/game/{code}/start', name: 'game_start')]
    public function start(Request $request, $code): JsonResponse
    {
        $gameInfo = $this->gs->startRequest($request, $code, false);

        $creator = $gameInfo["game"]->getCreator();

        if ($creator->getSessionUser()->getId() != $gameInfo["userId"]) {
            $this->createNotFoundException("Oops, you can't start this game!");
        }

        if (count($gameInfo["players"]) < 5) {
            throw $this->createNotFoundException('Not enough players to start');
        }

        $game = $gameInfo["game"];
        $state = new GameState();
        $state->setGame($game);
        $game->setState($state);
        
        //Initialize game state to starting conditions
        $state->setRound(0); //0 based counting, first round is 0

        //Game starts of with first leader nominating the first mission tesm
        $state->setPhase("NominateTeam");

        //assign first leader, will increment with wraparound each team vote
        //as if sitting around a table
        $state->setLeaderIndex(rand(0, $gameInfo["playerCount"] - 1));

        //JSON arrays of failure (false) or success (true) for each round
        $state->setResults("[]");
        
        //JSON arrays of game player indices
        $state->setTeamPlayerIndices("[]");
        $state->setPreviousTeam("[]");

        //JSON array of bools for which players are bad
        $state->setIsBad(json_encode($this->gs->createBadPlayerArray($gameInfo["playerCount"])));

        $game->setStarted(true);
        $this->em->persist($game);
        $this->em->persist($state);
        $this->em->flush();

        return new JsonResponse(["msg" => "OK"]);
    }

    #[Route('/game/{code}', name: 'game')]
    public function game(Request $request, $code): Response
    {
        $gameInfo = $this->gs->startRequest($request, $code);

        return $this->render('game/game.html.twig', [
            'code' => $code,
            'name' => $gameInfo["game"]->getName()
        ]);
    }

    #[Route('/game/{code}/state', name: 'game_state')]
    public function state(Request $request, $code)
    {
        $gameInfo = $this->gs->startRequest($request, $code);

        $leaderIndex = $gameInfo["state"]->getLeaderIndex();
        $leader = NULL;
        $isLeader = false;

        if ($leaderIndex >= 0) {
            $leader = $gameInfo["players"][$leaderIndex];
            $isLeader = $leader->getId() == $gameInfo["player"]->getId();
        }

        $isOnTeam = false;
        $team = json_decode($gameInfo["state"]->getTeamPlayerIndices());
        foreach ($team as $i) {
            if ($gameInfo["players"][$i]->getId() == $gameInfo["player"]->getId()) {
                $isOnTeam = true;
                break;
            }
        }

        $isBad = json_decode($gameInfo["state"]->getIsBad())[$gameState["playerIndex"]];

        //Only do next phase checks on leader to prevent race condition on simulataneous player requests
        if ($isLeader) {
            $this->gs->checkPhase($gameInfo);
        }

        //Area is specific to a player, state is universal game state for all players
        return new JsonResponse([
            "area" => $this->gs->gameArea($gameInfo, $isLeader, $isOnTeam, $isBad),
            "state" => $gameInfo["state"]
        ]);
    }

    #[Route('/game/{code}/amibad', name: 'game_amibad')]
    public function amibad(Request $request, $code)
    {
        $gameInfo = $this->gs->startRequest($request, $code);

        $isBad = json_decode($gameInfo["state"]->getIsBad())[$gameInfo["playerIndex"]];

        return new JsonResponse([
            'isBad' => $isBad
        ]);
    }

    #[Route('/game/{code}/nominate', name: 'game_nominate')]
    public function nominate(Request $request, $code)
    {
        $gameInfo = $this->gs->startRequest($request, $code);

        //Should be doing a regex to enforce JSON array of integers here
        $r = $request->toArray()["team"];
        $this->gs->doNominate($gameInfo["state"], $r);

        return new JsonResponse(["msg" => "OK"]);
    }

    #[Route('/game/{code}/vote', name: 'game_vote')]
    public function vote(Request $request, $code)
    {
        $gameInfo = $this->gs->startRequest($request, $code);

        //Should be doing a regex to enforce JSON array of integers here
        $vote = $request->toArray()["vote"];

        $gameInfo["player"]->setVote($vote);
        $this->em->persist($gameInfo["player"]);
        $this->em->flush();

        return new JsonResponse(["msg" => "OK"]);
    }

    #[Route('/game/{code}/mission', name: 'game_mission')]
    public function mission(Request $request, $code)
    {
        $gameInfo = $this->gs->startRequest($request, $code);

        //Should be doing a regex to enforce JSON array of integers here
        $vote = $request->toArray()["vote"];

        $gameInfo["player"]->setVote($vote);
        $this->em->persist($gameInfo["player"]);
        $this->em->flush();

        return new JsonResponse(["msg" => "OK"]);
    }
}
