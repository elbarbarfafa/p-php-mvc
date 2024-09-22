<?php

declare(strict_types=1);

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

#[ODM\Document(collection:'chambres')]
class Chambre {

    #[ODM\Id]
    public ?string $code_chambre = null;

    #[ODM\Field()]
    public int $etage;

    #[ODM\Field()]
    public string $type;

    #[ODM\Field()]
    public int $nombre_lit;

    #[ODM\ReferenceOne(targetDocument:Hotel::class)]
    public $hotel;
    
}