<?php

namespace App\Doctrine\DataFixtures;

use App\Model\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TagFixtures extends Fixture
{

	public function load(ObjectManager $manager): void
    {
		$videosGamesTag = [
	    'RPG',
	    'Rythm',
	    'Adventure',
	    'Souls-Like',
	    'Action',
	    'Sci-fi',
	    'Family',
	    'Horror',
	    'Puzzle',
	    'Turn-Based',
	    'Rogue-Like',
	    'Romance',
	    'Coop'
    ];

	    $tags = array_fill_callback(0, count($videosGamesTag) - 1, static fn (int $index): Tag => (new Tag)
		    ->setName($videosGamesTag[$index])
	    );

		array_walk($tags, [$manager, 'persist']);
        $manager->flush();
    }
}
