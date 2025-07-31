<?php

namespace App\Tests;

use App\Model\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AddNoteTest extends WebTestCase
{

	public function testAccessGame(): void
	{
		$client = static::createClient();
		$urlGenerator = $client->getContainer()->get('router.default');
		$userRepo = $client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
		$testUser = $userRepo->find(1);
		$client->loginUser($testUser);

		$client->request(Request::METHOD_GET, $urlGenerator->generate('video_games_show', ['slug' => 'jeu-video-0']));

		self::assertResponseStatusCodeSame(Response::HTTP_OK);
		self::assertSelectorTextContains('h1', 'Jeu vidéo 0');
	}

}
