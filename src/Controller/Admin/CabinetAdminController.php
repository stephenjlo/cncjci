<?php
namespace App\Controller\Admin;

use App\Entity\Cabinet;
use App\Entity\User;
use App\Form\CabinetType;
use App\Repository\CabinetRepository;
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

#[Route('/admin/cabinets')]
#[IsGranted('ROLE_SUPER_ADMIN')]
class CabinetAdminController extends AbstractController
{
    public function __construct(
        private CabinetRepository $repository,
        private EntityManagerInterface $em,
        private UserCreationService $userCreationService,
        private FileUploadService $fileUploadService,
        private SluggerInterface $slugger
    ) {}

    #[Route('', name: 'admin_cabinet_index')]
    public function index(Request $request): Response
    {
        // Récupérer tous les cabinets (DataTable gérera la pagination côté client)
        $cabinets = $this->repository->findBy([], ['name' => 'ASC']);

        return $this->render('admin/cabinet/index.html.twig', [
            'cabinets' => $cabinets,
        ]);
    }

    #[Route('/new', name: 'admin_cabinet_new')]
    public function new(Request $request): Response
    {
        $cabinet = new Cabinet();

        // Initialiser avec au moins un email et un téléphone vide
        $email = new \App\Entity\EmailAddress();
        $email->setLabel('Principal');
        $email->setEmail('');
        $email->setIsPrimary(true);
        $email->setPosition(0);
        $cabinet->addEmail($email);

        $phone = new \App\Entity\Phone();
        $phone->setLabel('Standard');
        $phone->setNumber('');
        $phone->setIsPrimary(true);
        $phone->setPosition(0);
        $cabinet->addPhone($phone);

        $form = $this->createForm(CabinetType::class, $cabinet);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Générer le slug automatiquement si vide
            if (empty($cabinet->getSlug())) {
                $slug = $this->slugger->slug($cabinet->getName())->lower();
                $cabinet->setSlug($slug);
            }

            // Gérer l'upload du logo
            /** @var UploadedFile $logoFile */
            $logoFile = $form->get('logoFile')->getData();
            if ($logoFile) {
                try {
                    $logoUrl = $this->fileUploadService->upload($logoFile, 'cabinets');
                    $cabinet->setLogoUrl($logoUrl);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload du logo: ' . $e->getMessage());
                }
            } else {
                // Définir le logo par défaut
                $cabinet->setLogoUrl($this->fileUploadService->getDefaultCabinetLogo());
            }

            // Rattacher les avocats sélectionnés au cabinet
            if ($form->has('lawyers')) {
                $selectedLawyers = $form->get('lawyers')->getData();
                foreach ($selectedLawyers as $lawyer) {
                    $lawyer->setCabinet($cabinet);
                }
            }

            // Si un responsable est désigné, le rattacher au cabinet aussi
            $managingPartner = $cabinet->getManagingPartner();
            if ($managingPartner) {
                $managingPartner->setCabinet($cabinet);

                // Promouvoir en RESPO_CABINET si un User existe pour ce lawyer
                $user = $this->em->getRepository(User::class)->findOneBy(['lawyer' => $managingPartner]);
                if ($user) {
                    if (!in_array('ROLE_RESPO_CABINET', $user->getRoles(), true)) {
                        $user->addRole('ROLE_RESPO_CABINET');
                        $user->setCabinet($cabinet);
                        $this->addFlash('success', 'Le responsable ' . $managingPartner->getFullName() . ' a été promu en ROLE_RESPO_CABINET');
                    }
                }
            }

            $this->em->persist($cabinet);
            $this->em->flush();

            $lawyerCount = $cabinet->getLawyers()->count();
            $message = 'Cabinet créé avec succès';
            if ($lawyerCount > 0) {
                $message .= ' avec ' . $lawyerCount . ' avocat(s) rattaché(s)';
            }
            $this->addFlash('success', $message);
            return $this->redirectToRoute('admin_cabinet_index');
        }

        return $this->render('admin/cabinet/form.html.twig', [
            'form' => $form,
            'cabinet' => $cabinet,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_cabinet_edit')]
    public function edit(Cabinet $cabinet, Request $request): Response
    {
        $this->denyAccessUnlessGranted('CABINET_EDIT', $cabinet);

        $oldManagingPartner = $cabinet->getManagingPartner();
        $oldLogoUrl = $cabinet->getLogoUrl();

        // Initialiser les collections si elles sont vides
        if ($cabinet->getEmails()->isEmpty()) {
            $email = new \App\Entity\EmailAddress();
            $email->setLabel('Principal');
            $email->setEmail('');
            $email->setIsPrimary(true);
            $email->setPosition(0);
            $cabinet->addEmail($email);
        }

        if ($cabinet->getPhones()->isEmpty()) {
            $phone = new \App\Entity\Phone();
            $phone->setLabel('Standard');
            $phone->setNumber('');
            $phone->setIsPrimary(true);
            $phone->setPosition(0);
            $cabinet->addPhone($phone);
        }

        $form = $this->createForm(CabinetType::class, $cabinet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Générer le slug automatiquement si vide
            if (empty($cabinet->getSlug())) {
                $slug = $this->slugger->slug($cabinet->getName())->lower();
                $cabinet->setSlug($slug);
            }

            // Gérer l'upload du logo
            /** @var UploadedFile $logoFile */
            $logoFile = $form->get('logoFile')->getData();
            if ($logoFile) {
                try {
                    // Supprimer l'ancien logo s'il existe et n'est pas le logo par défaut
                    if ($oldLogoUrl && $oldLogoUrl !== $this->fileUploadService->getDefaultCabinetLogo()) {
                        $this->fileUploadService->delete($oldLogoUrl);
                    }

                    // Uploader le nouveau logo
                    $logoUrl = $this->fileUploadService->upload($logoFile, 'cabinets');
                    $cabinet->setLogoUrl($logoUrl);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload du logo: ' . $e->getMessage());
                }
            }

            $newManagingPartner = $cabinet->getManagingPartner();

            // Si le responsable a changé, mettre à jour les rôles
            if ($newManagingPartner && $newManagingPartner !== $oldManagingPartner) {
                // Trouver le User associé au nouveau managingPartner
                $user = $this->em->getRepository(User::class)
                    ->findOneBy(['lawyer' => $newManagingPartner]);

                if ($user) {
                    // Promouvoir en RESPO_CABINET
                    $this->userCreationService->promoteToRespoCabinet($user);
                    // Associer le cabinet au User
                    $user->setCabinet($cabinet);

                    $this->addFlash('info',
                        $newManagingPartner->getFullName() . ' a été promu(e) en Responsable de Cabinet.');
                }

                // Rétrograder l'ancien responsable si nécessaire
                if ($oldManagingPartner) {
                    $oldUser = $this->em->getRepository(User::class)
                        ->findOneBy(['lawyer' => $oldManagingPartner]);

                    if ($oldUser && $oldUser->isRespoCabinet()) {
                        // Retirer ROLE_RESPO_CABINET
                        $roles = array_filter($oldUser->getRoles(),
                            fn($role) => $role !== 'ROLE_RESPO_CABINET'
                        );
                        $oldUser->setRoles($roles);
                        $oldUser->setCabinet(null);
                    }
                }
            }

            $this->em->flush();

            $this->addFlash('success', 'Cabinet modifié avec succès');
            return $this->redirectToRoute('admin_cabinet_index');
        }

        return $this->render('admin/cabinet/form.html.twig', [
            'form' => $form,
            'cabinet' => $cabinet,
        ]);
    }

    #[Route('/{id}/lawyers', name: 'admin_cabinet_lawyers')]
    public function manageLawyers(Cabinet $cabinet, Request $request): Response
    {
        $this->denyAccessUnlessGranted('CABINET_MANAGE_LAWYERS', $cabinet);

        // Récupérer tous les avocats disponibles
        $allLawyers = $this->em->getRepository(\App\Entity\Lawyer::class)->findAll();

        // Récupérer les avocats du cabinet
        $cabinetLawyers = $cabinet->getLawyers();

        // Traiter les actions POST (rattachement, détachement, désignation)
        if ($request->isMethod('POST')) {
            $action = $request->request->get('action');
            $lawyerId = $request->request->getInt('lawyer_id');

            if ($lawyerId) {
                $lawyer = $this->em->getRepository(\App\Entity\Lawyer::class)->find($lawyerId);

                if ($lawyer) {
                    switch ($action) {
                        case 'attach':
                            $lawyer->setCabinet($cabinet);
                            $this->em->flush();
                            $this->addFlash('success', $lawyer->getFullName() . ' a été rattaché(e) au cabinet');
                            break;

                        case 'detach':
                            // Vérifier que ce n'est pas le responsable
                            if ($cabinet->getManagingPartner() === $lawyer) {
                                $this->addFlash('error', 'Impossible de détacher le responsable du cabinet. Désignez d\'abord un autre responsable.');
                            } else {
                                $lawyer->setCabinet(null);
                                $this->em->flush();
                                $this->addFlash('success', $lawyer->getFullName() . ' a été détaché(e) du cabinet');
                            }
                            break;

                        case 'designate':
                            $oldManagingPartner = $cabinet->getManagingPartner();

                            // S'assurer que le lawyer est rattaché au cabinet
                            if ($lawyer->getCabinet() !== $cabinet) {
                                $lawyer->setCabinet($cabinet);
                            }

                            $cabinet->setManagingPartner($lawyer);

                            // Promouvoir en RESPO_CABINET si un User existe
                            $user = $this->em->getRepository(User::class)->findOneBy(['lawyer' => $lawyer]);
                            if ($user) {
                                $this->userCreationService->promoteToRespoCabinet($user);
                                $user->setCabinet($cabinet);
                            }

                            // Rétrograder l'ancien responsable si nécessaire
                            if ($oldManagingPartner && $oldManagingPartner !== $lawyer) {
                                $oldUser = $this->em->getRepository(User::class)
                                    ->findOneBy(['lawyer' => $oldManagingPartner]);

                                if ($oldUser && $oldUser->isRespoCabinet()) {
                                    $roles = array_filter($oldUser->getRoles(),
                                        fn($role) => $role !== 'ROLE_RESPO_CABINET'
                                    );
                                    $oldUser->setRoles($roles);
                                    $oldUser->setCabinet(null);
                                }
                            }

                            $this->em->flush();
                            $this->addFlash('success', $lawyer->getFullName() . ' a été désigné(e) comme responsable du cabinet');
                            break;
                    }
                }
            }

            return $this->redirectToRoute('admin_cabinet_lawyers', ['id' => $cabinet->getId()]);
        }

        return $this->render('admin/cabinet/lawyers.html.twig', [
            'cabinet' => $cabinet,
            'cabinetLawyers' => $cabinetLawyers,
            'allLawyers' => $allLawyers,
        ]);
    }

    #[Route('/{id}/toggle', name: 'admin_cabinet_toggle', methods: ['POST'])]
    public function toggle(Cabinet $cabinet): Response
    {
        $this->denyAccessUnlessGranted('CABINET_DELETE', $cabinet);

        $cabinet->setIsActive(!$cabinet->isActive());
        $this->em->flush();

        $status = $cabinet->isActive() ? 'activé' : 'désactivé';
        $this->addFlash('success', "Cabinet {$status} avec succès");

        return $this->redirectToRoute('admin_cabinet_index');
    }
}