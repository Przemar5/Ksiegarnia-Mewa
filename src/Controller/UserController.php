<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Controller\ErrorController;
use App\Form\UserEditType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/uzytkownicy", name="user_")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**
	 * @Route("/{id}", name="show", methods="GET", requirements={"id"="\d+"})
     */
    public function show($id, Request $request)
    {
    	$user = $this
    		->getDoctrine()
    		->getRepository(User::class)
    		->findOneBy([
    			'id' => $id,
    			'deletedAt' => null,
    		])
    	;

    	if (empty($user)) {
            $errorController = new ErrorController();
            return $errorController->renderPageNotFound();
    	}

    	return $this->render('user/show.html.twig', [
    		'user' => $user,
    	]);
    }

    /**
	 * @Route("/{id}/edytuj", name="edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     */
    public function edit($id, Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
    	$user = $this
    		->getDoctrine()
    		->getRepository(User::class)
    		->findOneBy([
    			'id' => $id,
    			'deletedAt' => null,
    		])
    	;

    	if (empty($user)) {
            $errorController = new ErrorController();
            return $errorController->renderPageNotFound();
    	}

    	$form = $this->createForm(UserEditType::class, $user);
    	$form->handleRequest($request);

    	if ($form->isSubmitted() && $form->isValid()) {
    		// encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $this->getDoctrine()->getManager()->flush();

            $message = ($this->getUser() === $user) 
	            ? 'Twoje dane zostały zmienione.' 
	            : 'Dane użytkownika zostały zmienione.';
            $this->addFlash('success', $message);

            return $this->redirectToRoute('user_show', [
            	'id' => $user->getId(),
            ]);
    	}

    	return $this->render('user/edit.html.twig', [
    		'user' => $user,
    		'userEditForm' => $form->createView(),
    	]);
    }

    /**
	 * @Route("/{id}/usun", name="delete", methods={"POST","DELETE"}, requirements={"id"="\d+"})
     */
    public function delete($id, Request $request, TokenStorageInterface $tokenStorage)
    {
    	$user = $this
    		->getDoctrine()
    		->getRepository(User::class)
    		->findOneBy([
    			'id' => $id,
    			'deletedAt' => null,
    		])
    	;

    	if (empty($user) || ($user !== $this->getUser() && 
    		!$this->getUser()->hasRole('ROLE_ADMIN'))) {

            $errorController = new ErrorController();
            return $errorController->renderPageNotFound();
    	}

    	$submittedToken = $request->request->get('_token');

        if ($this->isCsrfTokenValid('user_delete_confirm'.$user->getId(), $submittedToken)) {
        	$user->setEmail($user->getEmail() . '_' . time() . '_' . iniqid());
        	$user->setDeletedAt(new \DateTime());

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('app_logout');
        }

    	return $this->render('user/delete.html.twig', [
    		'user' => $user,
    	]);
    }
}
