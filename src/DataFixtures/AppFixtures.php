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
            $label = new Label(LABELS[$i]['name']);

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
        'name' => 'chat mignon à la maison.jpg',
        'labels' => ['cats', 'cute', 'at_home'],
        'email' => 'dev@team.fr',
    ],
    [
        'name' => 'fratrie de chats mignons dehors.jpg',
        'labels' => ['cats', 'outdoors', 'cute'],
        'email' => 'dev@team.fr',
    ],
    [
        'name' => 'chien majestueux.jpg',
        'labels' => ['dogs'],
        'email' => 'dev@team.fr',
    ],
];
const LABELS = [
    ['name' => 'cats'],
    ['name' => 'dogs'],
    ['name' => 'at_home'],
    ['name' => 'cute'],
    ['name' => 'outdoors'],
];
const USERS = [
    [
        'email' => 'dev@team.fr',
        'password' => 'azerty',
        'is_admin' => true,
        'roles' => ['ROLE_ADMIN', 'ROLE_USER'],
    ],
    [
        'email' => 'user@team.fr',
        'password' => 'azerty',
        'is_admin' => false,
        'roles' => ['ROLE_USER'],
    ],
];
