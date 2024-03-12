<?php

namespace App\Repository;

use App\Entity\Photo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Photo>
 *
 * @method Photo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Photo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Photo[]    findAll()
 * @method Photo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhotoRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry                         $registry,
        private readonly EntityManagerInterface $manager,
    )
    {
        parent::__construct($registry, Photo::class);
    }

    public function insert(Photo $photo): Photo
    {
        $this->manager->persist($photo);
        $this->manager->flush();
        return $photo;
    }

    public function update(Photo $photo): Photo
    {
        $this->manager->persist($photo);
        $this->manager->flush();
        return $photo;
    }

    public function delete(Photo $photo): void
    {
        $this->manager->remove($photo);
        $this->manager->flush();
    }
}
