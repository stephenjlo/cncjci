<?php
namespace App\EventSubscriber;

use App\Entity\Address;
use App\Entity\Cabinet;
use App\Entity\Lawyer;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;

/**
 * Subscriber pour éviter la création d'entités Address vides
 */
#[AsDoctrineListener(event: Events::prePersist)]
#[AsDoctrineListener(event: Events::preUpdate)]
class AddressValidationSubscriber
{
    public function prePersist(PrePersistEventArgs $args): void
    {
        $this->handleAddress($args->getObject());
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $this->handleAddress($args->getObject());
    }

    private function handleAddress(object $entity): void
    {
        // Vérifier si l'entité a une adresse
        if (!($entity instanceof Cabinet || $entity instanceof Lawyer)) {
            return;
        }

        $address = $entity->getAddress();

        // Si pas d'adresse, rien à faire
        if (!$address) {
            return;
        }

        // Vérifier si l'adresse est vide (tous les champs sont null ou vides)
        if ($this->isAddressEmpty($address)) {
            // Supprimer l'association avec l'adresse vide
            $entity->setAddress(null);
        }
    }

    private function isAddressEmpty(Address $address): bool
    {
        return empty($address->getLine1())
            && empty($address->getLine2())
            && empty($address->getCity())
            && empty($address->getPostalCode())
            && empty($address->getLat())
            && empty($address->getLng());
    }
}
