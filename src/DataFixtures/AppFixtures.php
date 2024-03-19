<?php

namespace App\DataFixtures;

use App\Entity\AppUser;
use App\Entity\Label;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // create labels
        for ($i = 0; $i < count(LABELS); $i++) {
            $label = new Label();
            $label->setName(LABELS[$i]['name']);

            $manager->persist($label);
            $manager->flush();
        }


        // create users and default albums
        for ($i = 0; $i < count(USERS); $i++) {
            $user = new AppUser();
            $user->setEmail(USERS[$i]['email']);
            $user->setPassword(USERS[$i]['password']);
            $user->setIsAdmin(USERS[$i]['is_admin']);
            $user->setRoles(USERS[$i]['roles']);

            $manager->persist($user);
            $manager->flush();

            $manager->persist($user->newAlbum());
            $manager->flush();
        }

        // create photos
        for ($i = 0; $i < count(PHOTOS); $i++) {
            $user = $manager->getRepository(AppUser::class)->findOneBy(['email' => PHOTOS[$i]['email']]);
            $photo = $user->newPhoto(PHOTOS[$i]['name']);

            $labels = PHOTOS[$i]['labels'];
            for ($j = 0; $j < count($labels); $j++) {
                $photo->addLabel(
                    $manager->getRepository(Label::class)->findOneBy(['name' => $labels[$j]])
                );
            }

            $manager->persist($photo);
            $manager->flush();
        }
    }
}

const PHOTOS = [
    [
        'name' => 'macron.jpg',
        'labels' => ['macron'],
        'email' => 'macron@demission.fr',
    ],
    [
        'name' => 'brigitte.jpg',
        'labels' => ['chien'],
        'email' => 'macron@demission.fr',
    ],
];
const LABELS = [
    ['name' => 'macron'],
    ['name' => 'chien'],
    ['name' => 'chat'],
    ['name' => 'montagne'],
];
const USERS = [
    [
        'email' => 'macron@demission.fr',
        'password' => 'explosion',
        'is_admin' => true,
        'roles' => ['ROLE_ADMIN', 'ROLE_USER'],
    ],
    [
        'email' => 'daniel@team.fr',
        'password' => 'explosion',
        'is_admin' => false,
        'roles' => ['ROLE_USER'],
    ],
    [
        'email' => 'jerome@team.fr',
        'password' => 'explosion',
        'is_admin' => false,
        'roles' => ['ROLE_USER'],
    ],
    [
        'email' => 'sacha@team.fr',
        'password' => 'explosion',
        'is_admin' => false,
        'roles' => ['ROLE_USER'],
    ],
    [
        'email' => 'hurkan@team.fr',
        'password' => 'explosion',
        'is_admin' => false,
        'roles' => ['ROLE_USER'],
    ],
];
