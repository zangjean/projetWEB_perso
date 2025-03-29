<?php

namespace App\DataFixtures;

use App\Entity\Pays;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PaysFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $pays = [
            ['France', 'FR'],
            ['Allemagne', 'DE'],
            ['Italie', 'IT'],
            ['Espagne', 'ES'],
            ['Portugal', 'PT'],
            ['Royaume-Uni', 'GB'],
            ['Belgique', 'BE'],
            ['Cambodge', 'KH'],
            ['Canada', 'CA'],
            ['Suisse', 'CH'],
            ['Russie', 'RU'],
            ['Japon', 'JP'],
            ['Australie', 'AU'],
            ['Chine', 'CN'],
            ['Inde', 'IN'],
            ['Etats-Unis', 'US'],
            ['Kazakhstan', 'KZ'],
            ['Mexique', 'MX'],
            ['Chipre', 'CY'],
            ['Arabie Saoudite', 'SA'],
            ['Tunisie', 'TN'],
            ['Turquie', 'TR'],
            ['Pakistan', 'PK'],
            ['Nigeria', 'NG'],
            ['Algerie', 'DZ'],
            ['Liban', 'LB'],
            ['Egypte', 'EG'],
            ['Maroc', 'MA'],
        ];

        /*
        foreach ($pays as [$nom, $code]) {
            $pays1 = new Pays();
            $pays1
                ->setNom($nom)
                ->setCode($code);
            $manager->persist($pays1);
        }*/

        //pour avoir acces au pays ->
        foreach ($pays as $index => [$nom, $code]) {
            $pays1 = new Pays();
            $pays1
                ->setNom($nom)
                ->setCode($code);
            $manager->persist($pays1);

            // On stocke une référence nommée
            $this->addReference($code, $pays1);
        }

        $manager->flush();
    }
}
