<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Task;
use App\Repository\TaskRepository;


class HomeController extends AbstractController
{

    #[Route('/', name: 'app_home')]
    #[IsGranted("ROLE_ADMIN")]
    public function index(TaskRepository $taskRepo): Response
    {
        // if(!$this->getUser()){
        //     return $this->redirectToRoute('app_login');
        // }else{
        //    //user tasks
        //     $tasks = $taskRepo->findBy(['user' => $this->getUser()]);
        //     return $this->render('home/index.html.twig', [
        //         'controller_name' => 'HomeController',
        //         'tasks' => $tasks

        //     ]);
        // }
      //user tasks
      $tasks = $taskRepo->findBy(['user' => $this->getUser()]);
      return $this->render('home/index.html.twig', [
          'controller_name' => 'HomeController',
          'tasks' => $tasks

      ]);

    }
}
