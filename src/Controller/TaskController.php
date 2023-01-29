<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\Extension\Core\Type\FileType;
// import urlizer
use Cocur\Slugify\Slugify;
use Cocur\Slugify\SlugifyInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Gedmo\Sluggable\Util\Urlizer;



class TaskController extends AbstractController
{

    private $em;

	public function __construct(
		EntityManagerInterface $em
	){
		$this->em = $em;
	}

    #[Route('/add', name: 'add_task')]
	#[IsGranted("ROLE_ADMIN")]
    public function index(Request $request)
    {


		//if user is not logged in display error message
		

        $task = new Task();
		 $user = new User();
		 $user = $this->getUser();
		 $task->setUser($user);


		$form = $this->createForm(TaskType::class, $task)     
		->add('submit', SubmitType::class, [
			'label' => 'Ajouter la tâche',
			'attr' => [
				'class' => 'btn btn-primary'
			]
			]);

		$form->handleRequest($request);

		
		$user = $this->getUser();


		if($form->isSubmitted() && $form->isValid()){

			$uploadedFile = $form['Illustration']->getData();
			
			if ($uploadedFile) {
				$destination = $this->getParameter('kernel.project_dir').'/public/uploads';
				$originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
				$newFilename = Urlizer::urlize($originalFilename).'-'.uniqid().'.'.$uploadedFile->guessExtension();
				$uploadedFile->move(
					$destination,
					$newFilename
				);
				$task->setIllustration($newFilename);
			}

			$this->em->persist($user);
			$this->em->persist($task);

			$this->em->flush();


			return $this->redirectToRoute('app_home');
		}

		return $this->render('Task/index.html.twig',[
			'form' => $form->createView()
		]);
    }

	#[Route('/edit/{id}', name: 'edit_task')]
	#[IsGranted("ROLE_ADMIN")]
	public function edit(Request $request, Task $task){
		$form = $this->createForm(TaskType::class, $task)->add('save', SubmitType::class, [
			'label' => 'Mettre à jour la tâche',
		]);

		$form->handleRequest($request);

		if($form->isSubmitted() && $form->isValid()){

			$uploadedFile = $form['Illustration']->getData();
			
			if ($uploadedFile) {
				$destination = $this->getParameter('kernel.project_dir').'/public/uploads';
				$originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
				$newFilename = Urlizer::urlize($originalFilename).'-'.uniqid().'.'.$uploadedFile->guessExtension();
				$uploadedFile->move(
					$destination,
					$newFilename
				);
				$task->setIllustration($newFilename);
			}


			$this->em->flush();

			return $this->redirectToRoute('app_home');
		}

		return $this->render('Task/edit.html.twig',[
			'form' => $form->createView()
		]);
	}

	#[Route('/delete/{id}', name: 'delete_task')]
	#[IsGranted("ROLE_ADMIN")]
	public function delete(Task $task){


	// if the task is removed, remove the illustration too
		$illustration = $task->getIllustration();
		if($illustration){
			unlink($this->getParameter('kernel.project_dir').'/public/uploads/'.$illustration);
		}


		$this->em->remove($task);
		$this->em->flush();

		return $this->redirectToRoute('app_home');
	}
}
