<?php

declare(strict_types=1);

namespace App\Document;

use DateTime;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

#[ODM\Document(collection:'reservations')]
class Reservation {

    #[ODM\Id]
    public $id;

    #[ODM\ReferenceOne(targetDocument:Hotel::class)]
    public $hotel;
    #[ODM\ReferenceOne(targetDocument:Client::class)]
    public $client;
    #[ODM\ReferenceMany(targetDocument: Chambre::class)]
    public $chambres;

    #[ODM\Field(nullable:true)]
    public ?string $comment;

    #[ODM\Field()]
    public DateTime $date_debut;

    #[ODM\Field()]
    public DateTime $date_fin;
}