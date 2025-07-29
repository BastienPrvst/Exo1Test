<?php

namespace App\Doctrine\DataFixtures;

use App\Model\Entity\Tag;
use App\Rating\CalculateAverageRating;
use App\Rating\CountRatingsPerValue;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

class TagFixtures extends Fixture
{

	public function __construct(
		private readonly Generator $faker,
		private readonly CalculateAverageRating $calculateAverageRating,
		private readonly CountRatingsPerValue $countRatingsPerValue
	)
	{
	}

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
