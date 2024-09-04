<?php

declare(strict_types=1);

namespace App\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

#[ODM\Document(collection:'hotels')]
class Hotel {

    #[ODM\Id]
    public ?string $code_hotel = null;

    #[ODM\Field()]
    public string $nom_hotel;
    #[ODM\Field()]
    public string $adresse_hotel;
    #[ODM\Field()]
    public string $categorie_hotel;

    
}