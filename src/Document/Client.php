<?php

declare(strict_types=1);

namespace App\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

#[ODM\Document(collection:'clients')]
class Client {

    #[ODM\Id]
    public ?string $code_client = null;

    #[ODM\Field()]
    public int $nom_client;

    #[ODM\Field()]
    public string $adresse_client;
    
}