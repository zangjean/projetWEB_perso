<?php

namespace App\DataFixtures;

use App\Entity\Pays;
use App\Entity\Produit;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            PaysFixtures::class,
        ];
    }
    public function load(ObjectManager $em): void
    {
        $produits = [
            ['The Legend of Zelda: Tears of the Kingdom', 'Aventure épique dans un monde ouvert.', 59, 12],
            ['Elden Ring', 'RPG d’action dans un monde sombre et fantastique.', 49, 8],
            ['Call of Duty: Modern Warfare III', 'FPS multijoueur nerveux et réaliste.', 69, 15],
            ['Stardew Valley', 'Simulation de ferme relaxante et rétro.', 39, 30],
            ['Hades', 'Rogue-like dynamique dans la mythologie grecque.', 29, 20],
            ['Super Mario Odyssey', 'Plateforme 3D colorée et inventive.', 59, 10],
            ['Spider-Man 2', 'Incarnez Peter Parker dans New York.', 69, 5],
            ['Minecraft', 'Construction et exploration en monde cubique.', 45, 25],
            ['Hollow Knight', 'Metroidvania sombre et raffiné.', 39, 18],
            ['Final Fantasy XVI', 'RPG narratif à grande échelle.', 59, 7],
            ['Celeste', 'Plateforme exigeante et touchante.', 29, 22],
            ['Cyberpunk 2077', 'RPG futuriste en monde ouvert.', 49, 10],
            ['Among Us', 'Multijoueur de déduction sociale.', 35, 50],
            ['Dead Cells', 'Roguevania nerveux et stylé.', 39, 16],
            ['God of War: Ragnarök', 'Aventure mythologique épique.', 59, 9],
            ['It Takes Two', 'Jeu coopératif plein de créativité.', 45, 12],
            ['Slay the Spire', 'Deck-building stratégique.', 29, 20],
            ['Pokémon Écarlate', 'Attrapez-les tous dans une nouvelle région.', 59, 13],
            ['Terraria', 'Survie 2D et exploration sans fin.', 35, 26],
            ['Resident Evil 4 Remake', 'Horreur et action remasterisées.', 39, 11],
        ];

        foreach ($produits as [$nom, $description, $prixUnitaire, $quantiteEnStock]) {
            $produit1 = new Produit();
            $produit1
                ->setNom($nom)
                ->setDescription($description)
                ->setPrixUnitaire($prixUnitaire)
                ->setQuantiteEnStock($quantiteEnStock);
            $em->persist($produit1);
            switch ($nom)
            {
                case 'The Legend of Zelda: Tears of the Kingdom':
                    $produit1->addPays($this->getReference('FR', Pays::class));
                    $produit1->addPays($this->getReference('IT', Pays::class));
                    break;

                case 'Elden Ring':
                    $produit1->addPays($this->getReference('DE', Pays::class));
                    break;

                case 'Call of Duty: Modern Warfare III':
                    $produit1->addPays($this->getReference('ES', Pays::class));
                    $produit1->addPays($this->getReference('PT', Pays::class));
                    break;

                case 'Stardew Valley':
                    $produit1->addPays($this->getReference('GB', Pays::class));
                    $produit1->addPays($this->getReference('KH', Pays::class));
                    $produit1->addPays($this->getReference('CA', Pays::class));
                    break;

                case 'Hades':
                    $produit1->addPays($this->getReference('CH', Pays::class));
                    $produit1->addPays($this->getReference('RU', Pays::class));
                    break;

                case 'Super Mario Odyssey':
                    $produit1->addPays($this->getReference('JP', Pays::class));
                    $produit1->addPays($this->getReference('AU', Pays::class));
                    break;

                case 'Spider-Man 2':
                    $produit1->addPays($this->getReference('CN', Pays::class));
                    break;

                case 'Minecraft':
                    $produit1->addPays($this->getReference('IN', Pays::class));
                    $produit1->addPays($this->getReference('FR', Pays::class));
                    break;

                case 'Hollow Knight':
                    $produit1->addPays($this->getReference('US', Pays::class));
                    break;

                case 'Final Fantasy XVI':
                    $produit1->addPays($this->getReference('JP', Pays::class));
                    break;

                case 'Celeste':
                    $produit1->addPays($this->getReference('KZ', Pays::class));
                    break;

                case 'Cyberpunk 2077':
                    $produit1->addPays($this->getReference('MX', Pays::class));
                    $produit1->addPays($this->getReference('CY', Pays::class));
                    break;

                case 'Among Us':
                    $produit1->addPays($this->getReference('US', Pays::class));
                    $produit1->addPays($this->getReference('FR', Pays::class));
                    break;

                case 'Dead Cells':
                    $produit1->addPays($this->getReference('SA', Pays::class));
                    break;

                case 'God of War: Ragnarök':
                    $produit1->addPays($this->getReference('PT', Pays::class));
                    $produit1->addPays($this->getReference('GB', Pays::class));
                    break;

                case 'It Takes Two':
                    $produit1->addPays($this->getReference('TN', Pays::class));
                    break;

                case 'Slay the Spire':
                    $produit1->addPays($this->getReference('TR', Pays::class));
                    break;

                case 'Pokémon Écarlate':
                    $produit1->addPays($this->getReference('JP', Pays::class));
                    $produit1->addPays($this->getReference('IT', Pays::class));
                    break;

                case 'Terraria':
                    $produit1->addPays($this->getReference('PK', Pays::class));
                    break;

                case 'Resident Evil 4 Remake':
                    $produit1->addPays($this->getReference('DE', Pays::class));
                    $produit1->addPays($this->getReference('DZ', Pays::class));
                    break;
            }

        }
        $em->flush();

    }


}
