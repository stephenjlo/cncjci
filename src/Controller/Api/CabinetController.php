<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Cabinet;
use App\Repository\CabinetRepository;
use App\Repository\LawyerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/cabinets')]
class CabinetController extends AbstractController
{
    public function __construct(
        private CabinetRepository $repo,
        private LawyerRepository $lawyers
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

        $items = array_map(function (Cabinet $c) {
            // type (entité OU string)
            $typeData = null;
            if (method_exists($c, 'getType') && $c->getType()) {
                $type = $c->getType();
                if (is_object($type)) {
                    $typeData = [
                        'id'   => method_exists($type, 'getId') ? $type->getId() : null,
                        'name' => method_exists($type, 'getName') ? $type->getName() : (string)$type,
                        'slug' => method_exists($type, 'getSlug') ? $type->getSlug() : null,
                    ];
                } else {
                    $typeData = ['id' => null, 'name' => (string)$type, 'slug' => null];
                }
            }

            return [
                'id'      => method_exists($c, 'getId') ? $c->getId() : null,
                'name'    => method_exists($c, 'getName') ? $c->getName() : null,
                'slug'    => method_exists($c, 'getSlug') ? $c->getSlug() : null,
                'type'    => $typeData,
                'city'    => method_exists($c, 'getCity') ? $c->getCity() : null,
                'email'   => method_exists($c, 'getEmail') ? $c->getEmail() : null,
                'phone'   => method_exists($c, 'getPhone') ? $c->getPhone() : null,
                'website' => method_exists($c, 'getWebsite') ? $c->getWebsite() : null,
                'logoUrl' => method_exists($c, 'getLogoUrl') ? $c->getLogoUrl() : null,
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
        $c = $this->repo->findOneBy(['slug' => $slug]);
        if (!$c) {
            return $this->json(['error' => 'Not found'], 404);
        }

        // ---- Avocats du cabinet (relation OU repository)
        $lawyers = [];
        if (method_exists($c, 'getLawyers') && $c->getLawyers()) {
            $lawyers = $c->getLawyers()->toArray();
        } else {
            // fallback: recherche par repo
            $lawyers = $this->lawyers->findBy(['cabinet' => $c]);
        }

        // tri : associé gérant en premier
        if (!empty($lawyers)) {
            usort($lawyers, function ($a, $b) use ($c) {
                $mp = method_exists($c, 'getManagingPartner') ? $c->getManagingPartner() : null;
                $aIs = ($mp && method_exists($a,'getId') && $a->getId() === $mp->getId()) ? 0 : 1;
                $bIs = ($mp && method_exists($b,'getId') && $b->getId() === $mp->getId()) ? 0 : 1;
                if ($aIs !== $bIs) return $aIs <=> $bIs;
                $aLast = method_exists($a,'getLastName') ? $a->getLastName() : '';
                $bLast = method_exists($b,'getLastName') ? $b->getLastName() : '';
                return strcmp($aLast, $bLast);
            });
        }

        // ---- Type (entité OU string)
        $typeData = null;
        if (method_exists($c, 'getType') && $c->getType()) {
            $type = $c->getType();
            if (is_object($type)) {
                $typeData = [
                    'id'   => method_exists($type,'getId') ? $type->getId() : null,
                    'name' => method_exists($type,'getName') ? $type->getName() : (string)$type,
                    'slug' => method_exists($type,'getSlug') ? $type->getSlug() : null,
                ];
            } else {
                $typeData = ['id' => null, 'name' => (string)$type, 'slug' => null];
            }
        }

        // ---- Phones (collection OU champ simple)
        $phones = [];
        if (method_exists($c, 'getPhones') && $c->getPhones()) {
            foreach ($c->getPhones() as $p) {
                $phones[] = [
                    'label'     => method_exists($p,'getLabel') ? $p->getLabel() : null,
                    'number'    => method_exists($p,'getNumber') ? $p->getNumber() : null,
                    'isPrimary' => method_exists($p,'isPrimary') ? $p->isPrimary() : false,
                    'position'  => method_exists($p,'getPosition') ? $p->getPosition() : 0,
                ];
            }
        } elseif (method_exists($c, 'getPhone') && $c->getPhone()) {
            $phones[] = ['label' => null, 'number' => $c->getPhone(), 'isPrimary' => true, 'position' => 0];
        }

        // ---- Emails (collection OU champ simple)
        $emails = [];
        if (method_exists($c, 'getEmails') && $c->getEmails()) {
            foreach ($c->getEmails() as $e) {
                $emails[] = [
                    'label'     => method_exists($e,'getLabel') ? $e->getLabel() : null,
                    'email'     => method_exists($e,'getEmail') ? $e->getEmail() : null,
                    'isPrimary' => method_exists($e,'isPrimary') ? $e->isPrimary() : false,
                    'position'  => method_exists($e,'getPosition') ? $e->getPosition() : 0,
                ];
            }
        } elseif (method_exists($c, 'getEmail') && $c->getEmail()) {
            $emails[] = ['label' => null, 'email' => $c->getEmail(), 'isPrimary' => true, 'position' => 0];
        }

        // ---- Address (objet Address OU anciens champs)
        $address = null;
        if (method_exists($c, 'getAddress') && $c->getAddress()) {
            $a = $c->getAddress();
            $address = [
                'line1'      => method_exists($a,'getLine1') ? $a->getLine1() : null,
                'line2'      => method_exists($a,'getLine2') ? $a->getLine2() : null,
                'city'       => method_exists($a,'getCity') ? $a->getCity() : (method_exists($c,'getCity') ? $c->getCity() : null),
                'postalCode' => method_exists($a,'getPostalCode') ? $a->getPostalCode() : null,
                'country'    => method_exists($a,'getCountry') ? $a->getCountry() : null,
                'lat'        => method_exists($a,'getLat') ? $a->getLat() : (method_exists($c,'getLat') ? $c->getLat() : null),
                'lng'        => method_exists($a,'getLng') ? $a->getLng() : (method_exists($c,'getLng') ? $c->getLng() : null),
            ];
        } else {
            // fallback : anciens champs du cabinet si existants
            $address = [
                'line1' => null,
                'line2' => null,
                'city'  => method_exists($c,'getCity') ? $c->getCity() : null,
                'postalCode' => null,
                'country' => null,
                'lat'   => method_exists($c,'getLat') ? $c->getLat() : null,
                'lng'   => method_exists($c,'getLng') ? $c->getLng() : null,
            ];
        }

        // ---- Associé gérant
        $mpData = null;
        if (method_exists($c, 'getManagingPartner') && $c->getManagingPartner()) {
            $mp = $c->getManagingPartner();
            $mpData = [
                'id'        => method_exists($mp,'getId') ? $mp->getId() : null,
                'firstName' => method_exists($mp,'getFirstName') ? $mp->getFirstName() : null,
                'lastName'  => method_exists($mp,'getLastName') ? $mp->getLastName() : null,
                'slug'      => method_exists($mp,'getSlug') ? $mp->getSlug() : null,
                'photoUrl'  => method_exists($mp,'getPhotoUrl') ? $mp->getPhotoUrl() : null,
            ];
        }

        // ---- Liste avocats (mini)
        $lawyersArr = array_map(function ($l) {
            return [
                'id'        => method_exists($l,'getId') ? $l->getId() : null,
                'firstName' => method_exists($l,'getFirstName') ? $l->getFirstName() : null,
                'lastName'  => method_exists($l,'getLastName') ? $l->getLastName() : null,
                'slug'      => method_exists($l,'getSlug') ? $l->getSlug() : null,
                'photoUrl'  => method_exists($l,'getPhotoUrl') ? $l->getPhotoUrl() : null,
            ];
        }, $lawyers);

        $payload = [
            'id'          => method_exists($c,'getId') ? $c->getId() : null,
            'name'        => method_exists($c,'getName') ? $c->getName() : null,
            'slug'        => method_exists($c,'getSlug') ? $c->getSlug() : null,
            'type'        => $typeData,
            'website'     => method_exists($c,'getWebsite') ? $c->getWebsite() : null,
            'description' => method_exists($c,'getDescription') ? $c->getDescription() : null,
            'address'     => $address,
            // on laisse aussi les anciens lat/lng si présents
            'lat'         => method_exists($c,'getLat') ? $c->getLat() : (is_array($address) ? $address['lat'] : null),
            'lng'         => method_exists($c,'getLng') ? $c->getLng() : (is_array($address) ? $address['lng'] : null),
            'phones'      => $phones,
            'emails'      => $emails,
            'managingPartner' => $mpData,
            'lawyers'     => $lawyersArr,
        ];

        return $this->json($payload);
    }
}
