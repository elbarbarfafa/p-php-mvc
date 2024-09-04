<?php

declare(strict_types=1);

namespace App\Document;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

#[ODM\Document(collection:'reservations')]
class Reservation {

    #[ODM\Id]
    public ?string $code_hotel = null;
    #[ODM\Id]
    public string $code_client;
    #[ODM\Id]
    public string $code_chambre;
    #[ODM\Field()]
    public DateTime $date_debut;

    #[ODM\Field()]
    public DateTime $date_fin;
}