<?php

namespace App\Tests\Functional\VideoGame;

use App\Model\Entity\Review;
use App\Model\Entity\User;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Faker\Factory;
use Random\RandomException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AddNoteTest extends WebTestCase
{
	public function setUp(): void
	{
		$this->client = static::createClient();
		$this->urlGenerator = $this->client->getContainer()->get('router.default');
		$this->userRepo = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
		$this->reviewRepo = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(Review::class);
		$this->entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');
	}

	public function testAccessGame():void
	{
		$this->client->request(Request::METHOD_POST, $this->urlGenerator->generate('video_games_show', ['slug' => 'jeu-video-5']));

		self::assertResponseStatusCodeSame(Response::HTTP_OK);
	}

//	public function testNoAuthorizedUserReview(): void
//	{
//
//		$this->client->request(Request::METHOD_POST, '/jeu-video-5', [
//			'review' => [
//				'comment' => 'Test comment',
//				'rating' => 5,
//				'_token' => 'gfervgrtgf'
//			]
//		]);
//		self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
//	}


	/**
	 * @throws OptimisticLockException
	 * @throws RandomException
	 * @throws ORMException
	 */
	public function testPostReview(): void
	{
		$testUser = $this->userRepo->find(1);
		$this->client->loginUser($testUser);
		$crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('video_games_show', ['slug' => 'jeu-video-5']));

		self::assertResponseStatusCodeSame(Response::HTTP_OK);
		self::assertSelectorTextContains('h1', 'Jeu vidéo 5');


		$faker = Factory::create();
		$randNumber = random_int(1, 5);
		$fakeText = $faker->text();

		$form = $crawler->selectButton('Poster')->form();
		$form['review[rating]'] = $randNumber;
		$form['review[comment]'] = $fakeText;
		$this->client->submit($form);
		self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
		$this->client->followRedirect();

		//Vérification de la rewiew dans le dom
		self::assertSelectorTextContains('div.list-group-item:last-child p', $fakeText);
		self::assertSelectorTextContains('div.list-group-item:last-child span.value', $randNumber);
		self::assertSelectorTextContains('div.list-group-item:last-child h3', $testUser->getUsername());

		//Vérification de la review dans la BDD
		$review = $this->reviewRepo->findBy([
			'user' => $testUser,
			'rating' => $randNumber,
			'comment' => $fakeText,
		]);

		self::assertNotNull($review);
		self::assertSelectorTextNotContains('button', 'Poster');
		if ($review !== null){
			$this->entityManager->remove($review[0]);
			$this->entityManager->flush();
		}

	}

}
