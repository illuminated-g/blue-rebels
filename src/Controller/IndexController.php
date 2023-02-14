<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

use App\Entity\SessionUser;

class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {
        $session = $request->getSession();
        $userId = $session->get('id', 0);

        $em = $doctrine->getManager();

        $sesUser;

        if ($userId == 0) {
            //need to create a player entry in the DB
            $session->start();
            $sesUser = new SessionUser();
            $em->persist($sesUser);
            $em->flush();
        } else {
            $sesUser = $doctrine->getRepository(SessionUser::class)->find($userId);
            if (!$sesUser) {
                $sesUser = new SessionUser();
                $em->persist($sesUser);
                $em->flush();
            }
        }

        $userId = $sesUser->getId();
        $session->set('id', $userId);

        return $this->render('index/index.html.twig', [
        ]);
    }
}
