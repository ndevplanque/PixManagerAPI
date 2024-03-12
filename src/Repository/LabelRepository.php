<?php

namespace App\Repository;

use App\Entity\Label;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @extends ServiceEntityRepository<Label>
 *
 * @method Label|null find($id, $lockMode = null, $lockVersion = null)
 * @method Label|null findOneBy(array $criteria, array $orderBy = null)
 * @method Label[]    findAll()
 * @method Label[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LabelRepository extends ServiceEntityRepository
{
    private readonly EntityManagerInterface $manager;

    public function __construct(
        ManagerRegistry        $registry,
        EntityManagerInterface $manager
    )
    {
        parent::__construct($registry, Label::class);
        $this->manager = $manager;
    }

    public function insert(Label $label): Label
    {
        if ($this->findOneBy(['name' => $label->getName()]) !== null) {
            throw new HttpException(400, 'This label already exists !');
        }
        $this->manager->persist($label);
        $this->manager->flush();
        return $label;
    }

    public function delete(Label $label): void
    {
        $this->manager->remove($label);
        $this->manager->flush();
    }
}
