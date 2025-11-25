<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Cabinet;
use App\Repository\CabinetRepository;
use App\Repository\LawyerRepository;
use App\Service\FileUploadService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/cabinets')]
class CabinetController extends AbstractController
{
    public function __construct(
        private CabinetRepository $repo,
        private LawyerRepository $lawyers,
        private FileUploadService $fileUploadService
    ) {}

    #[Route('', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $q = [
            'page' => $request->query->getInt('page', 1),
            'pageSize' => $request->query->getInt('pageSize', 12),
            'name' => $request->query->get('name'),
            'type' => $request->query->get('type'),
            'city' => $request->query->get('city'),
        ];

        $p = $this->repo->search($q);

        $items = array_map([$this, 'serializeCabinetList'], $p->items);

        return $this->json([
            'items' => $items,
            'total' => $p->total,
            'page' => $p->page,
            'pageSize' => $p->pageSize
        ]);
    }

    #[Route('/{slug}', methods: ['GET'])]
    public function show(string $slug): JsonResponse
    {
        $cabinet = $this->repo->findOneBy(['slug' => $slug, 'isActive' => true]);

        if (!$cabinet) {
            return $this->json(['error' => 'Not found'], 404);
        }

        return $this->json($this->serializeCabinetDetail($cabinet));
    }

    // ═══════════════════════════════════════════════════
    // SERIALIZERS
    // ═══════════════════════════════════════════════════

    private function serializeCabinetList(Cabinet $cabinet): array
    {
        $type = $cabinet->getType(); // Retourne CabinetType ou string (fallback)

        $typeData = null;
        if (is_object($type)) {
            $typeData = [
                'id'   => $type->getId(),
                'name' => $type->getName(),
                'slug' => $type->getSlug(),
            ];
        } elseif ($type) {
            $typeData = ['id' => null, 'name' => (string)$type, 'slug' => null];
        }

        $logoUrl = $cabinet->getLogoUrl();
        if (empty($logoUrl)) {
            $logoUrl = $this->fileUploadService->getDefaultCabinetLogo();
        } else {
            // Convertir en URL absolue pour l'API
            $logoUrl = $this->fileUploadService->getAbsoluteUrl($logoUrl);
        }

        return [
            'id'      => $cabinet->getId(),
            'name'    => $cabinet->getName(),
            'slug'    => $cabinet->getSlug(),
            'type'    => $typeData,
            'city'    => $cabinet->getCity(),
            'email'   => $cabinet->getEmail(),
            'phone'   => $cabinet->getPhone(),
            'website' => $cabinet->getWebsite(),
            'logoUrl' => $logoUrl,
        ];
    }

    private function serializeCabinetDetail(Cabinet $cabinet): array
    {
        // Type
        $type = $cabinet->getType();
        $typeData = null;
        if (is_object($type)) {
            $typeData = [
                'id'   => $type->getId(),
                'name' => $type->getName(),
                'slug' => $type->getSlug(),
            ];
        } elseif ($type) {
            $typeData = ['id' => null, 'name' => (string)$type, 'slug' => null];
        }

        // Téléphones
        $phones = [];
        foreach ($cabinet->getPhones() as $p) {
            $phones[] = [
                'label'     => $p->getLabel(),
                'number'    => $p->getNumber(),
                'isPrimary' => $p->isPrimary(),
                'position'  => $p->getPosition(),
            ];
        }
        if (empty($phones) && $cabinet->getPhone()) {
            $phones[] = [
                'label' => null,
                'number' => $cabinet->getPhone(),
                'isPrimary' => true,
                'position' => 0
            ];
        }

        // Emails
        $emails = [];
        foreach ($cabinet->getEmails() as $e) {
            $emails[] = [
                'label'     => $e->getLabel(),
                'email'     => $e->getEmail(),
                'isPrimary' => $e->isPrimary(),
                'position'  => $e->getPosition(),
            ];
        }
        if (empty($emails) && $cabinet->getEmail()) {
            $emails[] = [
                'label' => null,
                'email' => $cabinet->getEmail(),
                'isPrimary' => true,
                'position' => 0
            ];
        }

        // Adresse
        $address = null;
        if ($addr = $cabinet->getAddress()) {
            $address = [
                'line1'      => $addr->getLine1(),
                'line2'      => $addr->getLine2(),
                'city'       => $addr->getCity(),
                'postalCode' => $addr->getPostalCode(),
                'country'    => $addr->getCountry(),
                'lat'        => $addr->getLat(),
                'lng'        => $addr->getLng(),
            ];
        } else {
            // Fallback
            $address = [
                'line1' => null,
                'line2' => null,
                'city'  => $cabinet->getCity(),
                'postalCode' => null,
                'country' => 'Côte d\'Ivoire',
                'lat'   => $cabinet->getLat(),
                'lng'   => $cabinet->getLng(),
            ];
        }

        // Associé gérant
        $managingPartner = null;
        if ($mp = $cabinet->getManagingPartner()) {
            $managingPartner = [
                'id'        => $mp->getId(),
                'firstName' => $mp->getFirstName(),
                'lastName'  => $mp->getLastName(),
                'slug'      => $mp->getSlug(),
                'photoUrl'  => $this->fileUploadService->getAbsoluteUrl($mp->getPhotoUrl()),
            ];
        }

        // Avocats (uniquement ceux actifs)
        $lawyersCollection = $cabinet->getLawyers();
        if ($lawyersCollection->isEmpty()) {
            // Fallback: chercher par cabinet_id
            $lawyersCollection = $this->lawyers->findBy(['cabinet' => $cabinet, 'isActive' => true]);
        } else {
            // Filtrer pour ne garder que les lawyers actifs
            $lawyersCollection = array_filter($lawyersCollection->toArray(), fn($l) => $l->isActive());
        }

        // Tri: associé gérant en premier
        $mp = $cabinet->getManagingPartner();
        if ($mp && !empty($lawyersCollection)) {
            usort($lawyersCollection, function ($a, $b) use ($mp) {
                $aIsMP = ($a->getId() === $mp->getId()) ? 0 : 1;
                $bIsMP = ($b->getId() === $mp->getId()) ? 0 : 1;
                if ($aIsMP !== $bIsMP) return $aIsMP <=> $bIsMP;
                return strcmp($a->getLastName(), $b->getLastName());
            });
        }

        $lawyersData = array_map(fn($l) => [
            'id'        => $l->getId(),
            'firstName' => $l->getFirstName(),
            'lastName'  => $l->getLastName(),
            'slug'      => $l->getSlug(),
            'photoUrl'  => $this->fileUploadService->getAbsoluteUrl($l->getPhotoUrl()),
        ], $lawyersCollection);

        $logoUrl = $cabinet->getLogoUrl();
        if (empty($logoUrl)) {
            $logoUrl = $this->fileUploadService->getDefaultCabinetLogo();
        } else {
            // Convertir en URL absolue pour l'API
            $logoUrl = $this->fileUploadService->getAbsoluteUrl($logoUrl);
        }

        return [
            'id'          => $cabinet->getId(),
            'name'        => $cabinet->getName(),
            'slug'        => $cabinet->getSlug(),
            'type'        => $typeData,
            'website'     => $cabinet->getWebsite(),
            'description' => $cabinet->getDescription(),
            'logoUrl'     => $logoUrl,
            'address'     => $address,
            'lat'         => $cabinet->getLat(),
            'lng'         => $cabinet->getLng(),
            'phones'      => $phones,
            'emails'      => $emails,
            'managingPartner' => $managingPartner,
            'lawyers'     => $lawyersData,
        ];
    }
}