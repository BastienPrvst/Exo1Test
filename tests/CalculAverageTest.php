<?php

namespace App\Tests;

use App\Model\Entity\Review;
use App\Model\Entity\User;
use App\Model\Entity\VideoGame;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use App\Rating\RatingHandler;

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

	public function averageDataProvider(): \Generator
	{
		yield [[1, 2, 3]];
		yield [[]];
		yield [[5, 5, 4, 5, 3, 2, 1, 2, 5]];
	}

}
