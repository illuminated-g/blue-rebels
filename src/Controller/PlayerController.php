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

class PlayerController extends AbstractController
{
    #[Route('/player/{id}/name', name: 'player_name')]
    public function name(Request $request, GamePlayer $player, ManagerRegistry $doctrine): Response
    {
        // {"name":"NewNameValue"}
        $r = $request->toArray();
        $name = htmlspecialchars($r['name']);
        $session = $request->getSession();

        $em = $doctrine->getManager();

        $userId = $session->get('id');
        if ($userId == 0) {
            throw $this->createNotFoundException('Invalid session, reload home page'); 
        }

        if ($player->getSessionUser()->getId() != $userId) {
            throw $this->createNotFoundException('Unauthorized player');
        }

        $player->setName($name);
        $em->persist($player);
        $em->flush();

        return new JsonResponse([
            'name' => $player->getName()
        ]);
    }
}
