<?php

namespace App\Controller;

use App\Entity\UserProfile;
use App\Form\UserProfileType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class SettingsProfileController extends AbstractController
{
    public $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/settings/profile', name: 'app_settings_profile')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function index(Request $request, UserRepository $users): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $userProfile = $user->getUserProfile() ?? new UserProfile();
        
        $form = $this->createForm(UserProfileType::class, $userProfile);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $userProfile = $form->getData();
            $user->setUserProfile($userProfile);
            $this->em->persist($user);
            $this->em->flush();
            $this->addFlash('success', 'Your user profile settings were saved.');
            
            return $this->redirectToRoute('app_settings_profile');
        }
        return $this->render('settings_profile/profile.html.twig', [
            'form' =>$form->createView(),
            
        ]);
    }
}
