<?php
namespace App\Controller\Admin;

use App\Entity\Lawyer;
use App\Form\LawyerType;
use App\Repository\LawyerRepository;
use App\Service\UserCreationService;
use App\Service\FileUploadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/lawyers')]
class LawyerAdminController extends AbstractController
{
    public function __construct(
        private LawyerRepository $repository,
        private EntityManagerInterface $em,
        private UserCreationService $userCreationService,
        private FileUploadService $fileUploadService,
        private SluggerInterface $slugger
    ) {}

    #[Route('', name: 'admin_lawyer_index')]
    #[IsGranted('ROLE_RESPO_CABINET')]
    public function index(Request $request): Response
    {
        $user = $this->getUser();
        $page = $request->query->getInt('page', 1);
        $search = $request->query->get('search', '');
        $cabinetFilter = $request->query->getInt('cabinet', 0);

        $qb = $this->repository->createQueryBuilder('l');

        // RESPO_CABINET voit seulement les lawyers de son cabinet
        if ($user->isRespoCabinet() && !$user->isSuperAdmin()) {
            $qb->where('l.cabinet = :cabinet')
                ->setParameter('cabinet', $user->getCabinet());
        } elseif ($cabinetFilter > 0) {
            // SUPER_ADMIN peut filtrer par cabinet via le paramètre GET
            $qb->where('l.cabinet = :cabinet')
                ->setParameter('cabinet', $cabinetFilter);
        }

       /* if ($search) {
            $qb->andWhere('CONCAT(l.firstName, \' \', l.lastName) LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        $qb->orderBy('l.lastName', 'ASC')
            ->setMaxResults(20)
            ->setFirstResult(($page - 1) * 20);*/

        $lawyers = $qb->getQuery()->getResult();

        return $this->render('admin/lawyer/index.html.twig', [
            'lawyers' => $lawyers,
            'search' => $search,
            'page' => $page,
            'cabinetFilter' => $cabinetFilter,
        ]);
    }

    #[Route('/new', name: 'admin_lawyer_new')]
    #[IsGranted('ROLE_RESPO_CABINET')]
    public function new(Request $request): Response
    {
        $lawyer = new Lawyer();

        // Initialiser avec au moins un email et un téléphone vide
        $email = new \App\Entity\EmailAddress();
        $email->setLabel('Professionnel');
        $email->setEmail('');
        $email->setIsPrimary(true);
        $email->setPosition(0);
        $lawyer->addEmail($email);

        $phone = new \App\Entity\Phone();
        $phone->setLabel('Bureau');
        $phone->setNumber('');
        $phone->setIsPrimary(true);
        $phone->setPosition(0);
        $lawyer->addPhone($phone);

        // Si RESPO_CABINET, pré-remplir avec son cabinet
        if ($this->getUser()->isRespoCabinet() && !$this->getUser()->isSuperAdmin()) {
            $lawyer->setCabinet($this->getUser()->getCabinet());
        }

        $form = $this->createForm(LawyerType::class, $lawyer, [
            'user' => $this->getUser(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Générer le slug automatiquement si vide
            if (empty($lawyer->getSlug())) {
                $slug = $this->slugger->slug($lawyer->getFirstName() . ' ' . $lawyer->getLastName())->lower();
                $lawyer->setSlug($slug);
            }

            // Gérer l'upload de la photo
            /** @var UploadedFile $photoFile */
            $photoFile = $form->get('photoFile')->getData();
            if ($photoFile) {
                try {
                    $photoUrl = $this->fileUploadService->upload($photoFile, 'lawyers');
                    $lawyer->setPhotoUrl($photoUrl);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de la photo: ' . $e->getMessage());
                }
            } else {
                // Définir la photo par défaut si nécessaire
                $defaultPhoto = $this->fileUploadService->getDefaultLawyerPhoto();
                if ($defaultPhoto) {
                    $lawyer->setPhotoUrl($defaultPhoto);
                }
            }

            $this->em->persist($lawyer);
            $this->em->flush();

            // Créer automatiquement un compte User pour cet avocat
            $user = $this->userCreationService->createUserForLawyer($lawyer);

            if ($user) {
                $this->addFlash('success', 'Avocat créé avec succès. Un compte utilisateur a été créé avec le mot de passe par défaut : ChangeMe2024!');
            } else {
                $this->addFlash('warning', 'Avocat créé mais impossible de créer le compte utilisateur (email manquant)');
            }

            return $this->redirectToRoute('admin_lawyer_index');
        }

        return $this->render('admin/lawyer/form.html.twig', [
            'form' => $form,
            'lawyer' => $lawyer,
            'self_edit' => false,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_lawyer_edit')]
    public function edit(Lawyer $lawyer, Request $request): Response
    {
        $this->denyAccessUnlessGranted('LAWYER_EDIT', $lawyer);

        $oldPhotoUrl = $lawyer->getPhotoUrl();

        // Initialiser les collections si elles sont vides
        if ($lawyer->getEmails()->isEmpty()) {
            $email = new \App\Entity\EmailAddress();
            $email->setLabel('Professionnel');
            $email->setEmail('');
            $email->setIsPrimary(true);
            $email->setPosition(0);
            $lawyer->addEmail($email);
        }

        if ($lawyer->getPhones()->isEmpty()) {
            $phone = new \App\Entity\Phone();
            $phone->setLabel('Bureau');
            $phone->setNumber('');
            $phone->setIsPrimary(true);
            $phone->setPosition(0);
            $lawyer->addPhone($phone);
        }

        $form = $this->createForm(LawyerType::class, $lawyer, [
            'user' => $this->getUser(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Générer le slug automatiquement si vide
            if (empty($lawyer->getSlug())) {
                $slug = $this->slugger->slug($lawyer->getFirstName() . ' ' . $lawyer->getLastName())->lower();
                $lawyer->setSlug($slug);
            }

            // Gérer l'upload de la photo
            /** @var UploadedFile $photoFile */
            $photoFile = $form->get('photoFile')->getData();
            if ($photoFile) {
                try {
                    // Supprimer l'ancienne photo si elle existe et n'est pas la photo par défaut
                    if ($oldPhotoUrl && $oldPhotoUrl !== $this->fileUploadService->getDefaultLawyerPhoto()) {
                        $this->fileUploadService->delete($oldPhotoUrl);
                    }

                    // Uploader la nouvelle photo
                    $photoUrl = $this->fileUploadService->upload($photoFile, 'lawyers');
                    $lawyer->setPhotoUrl($photoUrl);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de la photo: ' . $e->getMessage());
                }
            }

            $this->em->flush();

            $this->addFlash('success', 'Avocat modifié avec succès');
            return $this->redirectToRoute('admin_lawyer_index');
        }

        return $this->render('admin/lawyer/form.html.twig', [
            'form' => $form,
            'lawyer' => $lawyer,
            'self_edit' => false,
        ]);
    }

    #[Route('/me', name: 'admin_lawyer_profile')]
    #[IsGranted('ROLE_LAWYER')]
    public function profile(Request $request): Response
    {
        $user = $this->getUser();
        $lawyer = $user->getLawyer();

        if (!$lawyer) {
            throw $this->createNotFoundException('Profil avocat non trouvé');
        }

        $this->denyAccessUnlessGranted('LAWYER_EDIT', $lawyer);

        $oldPhotoUrl = $lawyer->getPhotoUrl();

        // Initialiser les collections si elles sont vides
        if ($lawyer->getEmails()->isEmpty()) {
            $email = new \App\Entity\EmailAddress();
            $email->setLabel('Professionnel');
            $email->setEmail('');
            $email->setIsPrimary(true);
            $email->setPosition(0);
            $lawyer->addEmail($email);
        }

        if ($lawyer->getPhones()->isEmpty()) {
            $phone = new \App\Entity\Phone();
            $phone->setLabel('Bureau');
            $phone->setNumber('');
            $phone->setIsPrimary(true);
            $phone->setPosition(0);
            $lawyer->addPhone($phone);
        }

        // Form limité pour le lawyer (pas de champs sensibles)
        $form = $this->createForm(LawyerType::class, $lawyer, [
            'user' => $user,
            'self_edit' => true,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gérer l'upload de la photo
            /** @var UploadedFile $photoFile */
            $photoFile = $form->get('photoFile')->getData();
            if ($photoFile) {
                try {
                    // Supprimer l'ancienne photo si elle existe et n'est pas la photo par défaut
                    if ($oldPhotoUrl && $oldPhotoUrl !== $this->fileUploadService->getDefaultLawyerPhoto()) {
                        $this->fileUploadService->delete($oldPhotoUrl);
                    }

                    // Uploader la nouvelle photo
                    $photoUrl = $this->fileUploadService->upload($photoFile, 'lawyers');
                    $lawyer->setPhotoUrl($photoUrl);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de la photo: ' . $e->getMessage());
                }
            }

            $this->em->flush();

            $this->addFlash('success', 'Profil mis à jour avec succès');
            return $this->redirectToRoute('admin_lawyer_profile');
        }

        return $this->render('admin/lawyer/profile.html.twig', [
            'form' => $form,
            'lawyer' => $lawyer,
        ]);
    }

    #[Route('/{id}/create-user', name: 'admin_lawyer_create_user', methods: ['POST'])]
    #[IsGranted('ROLE_RESPO_CABINET')]
    public function createUser(Lawyer $lawyer): Response
    {
        $this->denyAccessUnlessGranted('LAWYER_EDIT', $lawyer);

        // Vérifier si un User existe déjà pour ce lawyer
        $existingUser = $this->em->getRepository(\App\Entity\User::class)
            ->findOneBy(['lawyer' => $lawyer]);

        if ($existingUser) {
            $this->addFlash('warning', 'Un compte utilisateur existe déjà pour cet avocat');
            return $this->redirectToRoute('admin_lawyer_index');
        }

        // Créer le compte User via le service
        $user = $this->userCreationService->createUserForLawyer($lawyer);

        if ($user) {
            $this->addFlash('success', sprintf(
                'Compte utilisateur créé avec succès pour %s. Email de connexion : %s | Mot de passe par défaut : ChangeMe2024!',
                $lawyer->getFullName(),
                $user->getEmail()
            ));
        } else {
            $this->addFlash('error', 'Impossible de créer le compte utilisateur (email manquant)');
        }

        return $this->redirectToRoute('admin_lawyer_index');
    }
}