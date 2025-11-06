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

        $items = array_map(function ($l) {
            // cabinet mini
            $cab = null;
            if (method_exists($l, 'getCabinet') && $l->getCabinet()) {
                $cabEntity = $l->getCabinet();
                $cab = [
                    'id'   => method_exists($cabEntity, 'getId') ? $cabEntity->getId() : null,
                    'name' => method_exists($cabEntity, 'getName') ? $cabEntity->getName() : null,
                    'slug' => method_exists($cabEntity, 'getSlug') ? $cabEntity->getSlug() : null,
                    'city' => method_exists($cabEntity, 'getCity') ? $cabEntity->getCity() : null,
                ];
            }

            // specialties mini
            $specialties = [];
            if (method_exists($l, 'getSpecialties') && $l->getSpecialties()) {
                foreach ($l->getSpecialties() as $s) {
                    $specialties[] = [
                        'id' => method_exists($s, 'getId') ? $s->getId() : null,
                        'name' => method_exists($s, 'getName') ? $s->getName() : null,
                    ];
                }
            }

            return [
                'id'        => method_exists($l, 'getId') ? $l->getId() : null,
                'firstName' => method_exists($l, 'getFirstName') ? $l->getFirstName() : null,
                'lastName'  => method_exists($l, 'getLastName') ? $l->getLastName() : null,
                'slug'      => method_exists($l, 'getSlug') ? $l->getSlug() : null,
                'email'     => method_exists($l, 'getEmail') ? $l->getEmail() : null,
                'phone'     => method_exists($l, 'getPhone') ? $l->getPhone() : null,
                'city'      => method_exists($l, 'getCity') ? $l->getCity() : null,
                'photoUrl'  => method_exists($l, 'getPhotoUrl') ? $l->getPhotoUrl() : null,
                'cabinet'   => $cab,
                'specialties' => $specialties,
            ];
        }, $p->items);

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
        $l = $this->repo->findOneBy(['slug' => $slug]);
        if (!$l) {
            return $this->json(['error' => 'Not found'], 404);
        }

        // phones (fallback: getPhone())
        $phones = [];
        if (method_exists($l, 'getPhones') && $l->getPhones()) {
            foreach ($l->getPhones() as $p) {
                $phones[] = [
                    'label'     => method_exists($p,'getLabel') ? $p->getLabel() : null,
                    'number'    => method_exists($p,'getNumber') ? $p->getNumber() : null,
                    'isPrimary' => method_exists($p,'isPrimary') ? $p->isPrimary() : false,
                    'position'  => method_exists($p,'getPosition') ? $p->getPosition() : 0,
                ];
            }
        } elseif (method_exists($l, 'getPhone') && $l->getPhone()) {
            $phones[] = [
                'label' => null, 'number' => $l->getPhone(), 'isPrimary' => true, 'position' => 0
            ];
        }
        
        // emails (fallback: getEmail())
        $emails = [];
        if (method_exists($l, 'getEmails') && $l->getEmails()) {
            foreach ($l->getEmails() as $e) {
                $emails[] = [
                    'label'     => method_exists($e,'getLabel') ? $e->getLabel() : null,
                    'email'     => method_exists($e,'getEmail') ? $e->getEmail() : null,
                    'isPrimary' => method_exists($e,'isPrimary') ? $e->isPrimary() : false,
                    'position'  => method_exists($e,'getPosition') ? $e->getPosition() : 0,
                ];
            }
        } elseif (method_exists($l, 'getEmail') && $l->getEmail()) {
            $emails[] = [
                'label' => null, 'email' => $l->getEmail(), 'isPrimary' => true, 'position' => 0
            ];
        }

        // address (object Address OU rien)
        $address = null;
        if (method_exists($l, 'getAddress') && $l->getAddress()) {
            $a = $l->getAddress();
            // dd($a);
            $address = [
                'line1'      => method_exists($a,'getLine1') ? $a->getLine1() : null,
                'line2'      => method_exists($a,'getLine2') ? $a->getLine2() : null,
                'city'       => method_exists($a,'getCity') ? $a->getCity() : (method_exists($l,'getCity') ? $l->getCity() : null),
                'postalCode' => method_exists($a,'getPostalCode') ? $a->getPostalCode() : null,
                'country'    => method_exists($a,'getCountry') ? $a->getCountry() : null,
                'lat'        => method_exists($a,'getLat') ? $a->getLat() : null,
                'lng'        => method_exists($a,'getLng') ? $a->getLng() : null,
            ];
        } else {
            // (optionnel) tu peux remonter l'ancienne ville si tu veux
            if (method_exists($l, 'getCity') && $l->getCity()) {
                $address = ['line1' => null, 'line2' => null, 'city' => $l->getCity(), 'postalCode'=>null, 'country'=>null, 'lat'=>null, 'lng'=>null];
            }
        }
        
        // cabinet mini
        $cab = null;
        if (method_exists($l, 'getCabinet') && $l->getCabinet()) {
            $c = $l->getCabinet();
            $cab = [
                'id'   => method_exists($c,'getId') ? $c->getId() : null,
                'name' => method_exists($c,'getName') ? $c->getName() : null,
                'slug' => method_exists($c,'getSlug') ? $c->getSlug() : null,
                'city' => method_exists($c,'getCity') ? $c->getCity() : null,
            ];
        }

        // specialties mini
        $specialties = [];
        if (method_exists($l, 'getSpecialties') && $l->getSpecialties()) {
            foreach ($l->getSpecialties() as $s) {
                $specialties[] = [
                    'id' => method_exists($s,'getId') ? $s->getId() : null,
                    'name' => method_exists($s,'getName') ? $s->getName() : null,
                ];
            }
        }

        return $this->json([
            'id'        => method_exists($l, 'getId') ? $l->getId() : null,
            'firstName' => method_exists($l, 'getFirstName') ? $l->getFirstName() : null,
            'lastName'  => method_exists($l, 'getLastName') ? $l->getLastName() : null,
            'slug'      => method_exists($l, 'getSlug') ? $l->getSlug() : null,
            'barNumber' => method_exists($l, 'getBarNumber') ? $l->getBarNumber() : null,
            'biography' => method_exists($l, 'getBiography') ? $l->getBiography() : null,
            'photoUrl'  => method_exists($l, 'getPhotoUrl') ? $l->getPhotoUrl() : null,

            'cabinet'   => $cab,
            'phones'    => $phones,
            'emails'    => $emails,
            'address'   => $address,
            'specialties' => $specialties,
        ]);
    }
}
