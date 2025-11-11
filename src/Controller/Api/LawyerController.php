<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Repository\LawyerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/lawyers')]
class LawyerController extends AbstractController
{
    public function __construct(private LawyerRepository $repo) {}

    #[Route('', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $q = [
            'page' => $request->query->getInt('page', 1),
            'pageSize' => $request->query->getInt('pageSize', 12),
            'name' => $request->query->get('name'),
            'cabinet' => $request->query->get('cabinet'),
            'city' => $request->query->get('city'),
            'specialty' => $request->query->get('specialty'),
        ];

        $p = $this->repo->search($q);

        $items = array_map([$this, 'serializeLawyerList'], $p->items);

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
        $lawyer = $this->repo->findOneBy(['slug' => $slug]);

        if (!$lawyer) {
            return $this->json(['error' => 'Not found'], 404);
        }

        return $this->json($this->serializeLawyerDetail($lawyer));
    }

    // ═══════════════════════════════════════════════════
    // SERIALIZERS (Logique métier séparée)
    // ═══════════════════════════════════════════════════

    private function serializeLawyerList($lawyer): array
    {
        $cabinet = $lawyer->getCabinet();

        return [
            'id'        => $lawyer->getId(),
            'firstName' => $lawyer->getFirstName(),
            'lastName'  => $lawyer->getLastName(),
            'slug'      => $lawyer->getSlug(),
            'email'     => $lawyer->getEmail(), // Fallback intelligent dans l'entité
            'phone'     => $lawyer->getPhone(), // Fallback intelligent dans l'entité
            'city'      => $lawyer->getCity(),  // Fallback intelligent dans l'entité
            'photoUrl'  => $lawyer->getPhotoUrl(),
            'cabinet'   => $cabinet ? [
                'id'   => $cabinet->getId(),
                'name' => $cabinet->getName(),
                'slug' => $cabinet->getSlug(),
                'city' => $cabinet->getCity(),
            ] : null,
            'specialties' => array_map(fn($s) => [
                'id' => $s->getId(),
                'name' => $s->getName(),
            ], $lawyer->getSpecialties()->toArray()),
        ];
    }

    private function serializeLawyerDetail($lawyer): array
    {
        // Téléphones
        $phones = [];
        foreach ($lawyer->getPhones() as $p) {
            $phones[] = [
                'label'     => $p->getLabel(),
                'number'    => $p->getNumber(),
                'isPrimary' => $p->isPrimary(),
                'position'  => $p->getPosition(),
            ];
        }

        // Si aucun téléphone dans la collection, fallback sur ancien champ
        if (empty($phones) && $lawyer->getPhone()) {
            $phones[] = [
                'label' => null,
                'number' => $lawyer->getPhone(),
                'isPrimary' => true,
                'position' => 0
            ];
        }

        // Emails
        $emails = [];
        foreach ($lawyer->getEmails() as $e) {
            $emails[] = [
                'label'     => $e->getLabel(),
                'email'     => $e->getEmail(),
                'isPrimary' => $e->isPrimary(),
                'position'  => $e->getPosition(),
            ];
        }

        // Si aucun email dans la collection, fallback sur ancien champ
        if (empty($emails) && $lawyer->getEmail()) {
            $emails[] = [
                'label' => null,
                'email' => $lawyer->getEmail(),
                'isPrimary' => true,
                'position' => 0
            ];
        }

        // Adresse
        $address = null;
        if ($addr = $lawyer->getAddress()) {
            $address = [
                'line1'      => $addr->getLine1(),
                'line2'      => $addr->getLine2(),
                'city'       => $addr->getCity(),
                'postalCode' => $addr->getPostalCode(),
                'country'    => $addr->getCountry(),
                'lat'        => $addr->getLat(),
                'lng'        => $addr->getLng(),
            ];
        } elseif ($lawyer->getCity()) {
            // Fallback si seulement city est présent
            $address = [
                'line1' => null,
                'line2' => null,
                'city' => $lawyer->getCity(),
                'postalCode' => null,
                'country' => 'Côte d\'Ivoire',
                'lat' => null,
                'lng' => null
            ];
        }

        // Cabinet
        $cabinet = null;
        if ($cab = $lawyer->getCabinet()) {
            $cabinet = [
                'id'   => $cab->getId(),
                'name' => $cab->getName(),
                'slug' => $cab->getSlug(),
                'city' => $cab->getCity(),
            ];
        }

        // Spécialités
        $specialties = array_map(fn($s) => [
            'id' => $s->getId(),
            'name' => $s->getName(),
        ], $lawyer->getSpecialties()->toArray());

        return [
            'id'        => $lawyer->getId(),
            'firstName' => $lawyer->getFirstName(),
            'lastName'  => $lawyer->getLastName(),
            'slug'      => $lawyer->getSlug(),
            'barNumber' => $lawyer->getBarNumber(),
            'biography' => $lawyer->getBiography(),
            'photoUrl'  => $lawyer->getPhotoUrl(),
            'cabinet'   => $cabinet,
            'phones'    => $phones,
            'emails'    => $emails,
            'address'   => $address,
            'specialties' => $specialties,
        ];
    }
}