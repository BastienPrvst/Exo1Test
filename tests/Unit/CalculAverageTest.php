<?php

namespace App\Tests\Unit;

use App\Model\Entity\Review;
use App\Model\Entity\VideoGame;
use App\Rating\RatingHandler;
use PHPUnit\Framework\TestCase;

class CalculAverageTest extends TestCase
{

	public function testCalculateAverageWithNoReview(): void
    {
		$handler = new RatingHandler();
		$game = new VideoGame();
		$handler->calculateAverage($game);
		$this->assertNull($game->getAverageRating());
    }

	/**
	 * @dataProvider AverageDataProvider
	 */
	public function testCalculateAverage(?array $reviewsValues): void
	{
		$game = new VideoGame();
		if (!empty($reviewsValues)) {
			foreach ($reviewsValues as $iValue) {
				$review = new Review();
				$review->setVideoGame($game);
				$review->setRating($iValue);
				$game->getReviews()->add($review);
			}

			$expectedValue = ceil(array_sum($reviewsValues) /count($reviewsValues));
		}else{
			$expectedValue = null;
		}

		$handler = new RatingHandler();
		$handler->calculateAverage($game);
		$this->assertEquals($expectedValue, $game->getAverageRating());
	}

	public static function averageDataProvider(): \Generator
	{
		yield [[1, 2, 3]];
		yield [[]];
		yield [[5, 5, 4, 5, 3, 2, 1, 2, 5]];
		yield [[1,1,1,2]];
	}

}
