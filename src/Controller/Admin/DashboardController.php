<?php
namespace App\Controller\Admin;

use App\Repository\CabinetRepository;
use App\Repository\LawyerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_USER')]
class DashboardController extends AbstractController
{
    public function __construct(
        private CabinetRepository $cabinetRepository,
        private LawyerRepository $lawyerRepository
    ) {}

    #[Route('', name: 'admin_dashboard')]
    public function index(): Response
    {
        $user = $this->getUser();
        $stats = [];
        $lastActivity = [];

        // ═══════════════════════════════════════════════════
        // STATISTIQUES POUR SUPER_ADMIN
        // ═══════════════════════════════════════════════════
        if ($user->isSuperAdmin()) {
            $totalCabinets = $this->cabinetRepository->count([]);
            $activeCabinets = $this->cabinetRepository->count(['isActive' => true]);
            $inactiveCabinets = $totalCabinets - $activeCabinets;

            $totalLawyers = $this->lawyerRepository->count([]);
            $activeLawyers = $this->lawyerRepository->count(['isActive' => true]);
            $inactiveLawyers = $totalLawyers - $activeLawyers;

            $stats = [
                'totalCabinets' => $totalCabinets,
                'activeCabinets' => $activeCabinets,
                'inactiveCabinets' => $inactiveCabinets,
                'totalLawyers' => $totalLawyers,
                'activeLawyers' => $activeLawyers,
                'inactiveLawyers' => $inactiveLawyers,
            ];

            // Dernière activité - dernier lawyer créé
            $lastLawyer = $this->lawyerRepository->findOneBy([], ['id' => 'DESC']);
            if ($lastLawyer) {
                $lastActivity = [
                    'lawyer' => $lastLawyer->getFullName(),
                    'date' => 'Récemment', // Nécessiterait un champ createdAt dans l'entité
                ];
            }
        }

        // ═══════════════════════════════════════════════════
        // STATISTIQUES POUR RESPO_CABINET
        // ═══════════════════════════════════════════════════
        elseif ($user->isRespoCabinet() && $user->getCabinet()) {
            $cabinet = $user->getCabinet();

            $totalCabinetLawyers = $this->lawyerRepository->count(['cabinet' => $cabinet]);
            $activeCabinetLawyers = $this->lawyerRepository->count(['cabinet' => $cabinet, 'isActive' => true]);
            $inactiveCabinetLawyers = $totalCabinetLawyers - $activeCabinetLawyers;

            $stats = [
                'cabinetLawyers' => $totalCabinetLawyers,
                'activeCabinetLawyers' => $activeCabinetLawyers,
                'inactiveCabinetLawyers' => $inactiveCabinetLawyers,
            ];

            // Dernière activité - dernier lawyer du cabinet
            $lastLawyer = $this->lawyerRepository->findOneBy(
                ['cabinet' => $cabinet],
                ['id' => 'DESC']
            );
            if ($lastLawyer) {
                $lastActivity = [
                    'lawyer' => $lastLawyer->getFullName(),
                    'date' => 'Récemment',
                ];
            }
        }

        return $this->render('admin/dashboard.html.twig', [
            'user' => $user,
            'stats' => $stats,
            'lastActivity' => $lastActivity,
        ]);
    }
}