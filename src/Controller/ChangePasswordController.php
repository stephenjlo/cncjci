<?php
namespace App\Controller;

use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ChangePasswordController extends AbstractController
{
    #[Route('/change-password', name: 'app_change_password')]
    #[IsGranted('ROLE_USER')]
    public function changePassword(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em
    ): Response {
        $user = $this->getUser();
        $isMandatory = $user->mustChangePassword();

        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier que le mot de passe actuel est correct
            if (!$passwordHasher->isPasswordValid($user, $form->get('currentPassword')->getData())) {
                $this->addFlash('error', 'Le mot de passe actuel est incorrect.');

                return $this->render('security/change_password.html.twig', [
                    'form' => $form,
                    'is_mandatory' => $isMandatory,
                ]);
            }

            // Hasher et définir le nouveau mot de passe
            $newPassword = $form->get('newPassword')->getData();
            $user->setPassword($passwordHasher->hashPassword($user, $newPassword));

            // Désactiver le flag mustChangePassword
            $user->setMustChangePassword(false);

            $em->flush();

            $this->addFlash('success', 'Votre mot de passe a été modifié avec succès.');

            // Rediriger vers le dashboard
            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('security/change_password.html.twig', [
            'form' => $form,
            'is_mandatory' => $isMandatory,
        ]);
    }
}
