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

	public function testCalculateAverage(): void
	{
		$game = new VideoGame();
		for ($i = 1; $i < 6; $i++) {
			$review = new Review();
			$review->setVideoGame($game);
			$review->setRating($i);
			$game->getReviews()->add($review);
		}

		$handler = new RatingHandler();
		$handler->calculateAverage($game);
		$this->assertEquals(3, $game->getAverageRating());
	}

}
