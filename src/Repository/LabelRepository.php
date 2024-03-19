<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Label;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
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
    public function __construct(
        ManagerRegistry                         $registry,
        private readonly EntityManagerInterface $manager
    )
    {
        parent::__construct($registry, Label::class);
    }

    /** Tries to insert a Label object */
    public function insert(Label $label): Label
    {
        if ($this->findOneBy(['name' => $label->getName()]) !== null) {
            throw new HttpException(400, "Label {$label->getName()} already exists!");
        }
        $this->manager->persist($label);
        $this->manager->flush();
        return $label;
    }

    /** Finds a Label object by name or create it if it doesn't exist */
    public function findOrInsert(string $labelName): Label
    {
        $label = $this->findOneBy(['name' => $labelName]);
        if ($label === null) {
            $this->manager->persist($label = new Label($labelName));
            $this->manager->flush();
            return $label;
        }
        return $label;
    }

    public function delete(Label $label): void
    {
        $this->manager->remove($label);
        $this->manager->flush();
    }
}
