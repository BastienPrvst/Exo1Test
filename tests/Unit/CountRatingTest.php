<?php

namespace App\Tests\Unit;

use App\Model\Entity\NumberOfRatingPerValue;
use App\Model\Entity\Review;
use App\Model\Entity\VideoGame;
use App\Rating\RatingHandler;
use PHPUnit\Framework\TestCase;

class CountRatingTest extends TestCase
{
	/**
	 * @dataProvider provideCountRatingsPerValue
	 */
    public function testCountRatingsPerValue(?array $values): void
    {
		$game = new VideoGame();
		$handler = new RatingHandler();
	    $expectedValues = new NumberOfRatingPerValue();

		if (!empty($values)) {
			foreach ($values as $iValue) {
				$review = new Review();
				$review
					->setRating($iValue)
					->setVideoGame($game);

				$game->getReviews()->add($review);
			}

			foreach ($values as $value) {
				match ($value) {
					1 => $expectedValues->increaseOne(),
					2 => $expectedValues->increaseTwo(),
					3 => $expectedValues->increaseThree(),
					4 => $expectedValues->increaseFour(),
					5 => $expectedValues->increaseFive()
				};
			}
		}

		$handler->countRatingsPerValue($game);
		$this->assertEquals($expectedValues, $game->getNumberOfRatingsPerValue());

    }

	public function provideCountRatingsPerValue(): \Generator
	{
		yield [[1, 1, 2, 1, 5]];
		yield [[1, 1, 2, 1, 5, 4, 1, 5, 4, 3, 2]];
		yield [[2, 4, 4, 4, 5]];
		yield [[]];
	}
}
