<?php

namespace App\Repository;

use App\Entity\ParticipacaoVoto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<ParticipacaoVoto> */
class ParticipacaoVotoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ParticipacaoVoto::class);
    }
}
