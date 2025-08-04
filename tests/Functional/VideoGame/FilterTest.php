<?php

declare(strict_types=1);

namespace App\Tests\Functional\VideoGame;

use App\Model\Entity\Tag;
use App\Model\Entity\VideoGame;
use App\Tests\Functional\FunctionalTestCase;


final class FilterTest extends FunctionalTestCase
{
    public function testShouldListTenVideoGames(): void
    {
        $this->get('/');
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(10, 'article.game-card');
        $this->client->clickLink('2');
        self::assertResponseIsSuccessful();
    }

    public function testShouldFilterVideoGamesBySearch(): void
    {
        $this->get('/');
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(10, 'article.game-card');
        $this->client->submitForm('Filtrer', ['filter[search]' => 'Jeu vidéo 49'], 'GET');
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(1, 'article.game-card');
    }

	/**
	 * @param array $data
	 * @return void
	 * @dataProvider tagsProviders
	 */
	public function testFilterVideoGammesByTags(array $data):void
	{
		$this->get('/');
		self::assertResponseIsSuccessful();

		$crawler = $this->client->getCrawler();
		$form = $crawler->selectButton('Filtrer')->form();
		$doctrine = $this->client->getContainer()->get('doctrine.orm.entity_manager');
		$tagRepo = $doctrine->getRepository(Tag::class);
		$gamesRepo = $doctrine->getRepository(VideoGame::class);

		//Si le dataprovider n'est pas vide

		if (!empty($data)){
			$formData = [];

			foreach ($data as $tagName) {
				$tag = $tagRepo->findOneBy(['name' => $tagName]);
				if ($tag) {
					$formData['filter[tags]'][] = $tag->getId();
				}
			}

			$this->client->submit($form, $formData);
		}else{
			//Si vide
			$this->client->submit($form);
		}



		$expectedValues = [];
		$games = $gamesRepo->findAll();
		foreach ($games as $game) {
			/* @var $game VideoGame */
			$tags = $game->getTags();
			foreach ($tags as $tag) {
				if (in_array($tag->getName(), $data, true)) {
					$expectedValues[] = $game->getTitle();
				}
			}
		}

		$expectedGames = count($expectedValues);

		self::assertSelectorCount($expectedGames, 'article.game.game-card');
	}

	public function tagsProviders(): \Generator
	{
		yield [[
			'RPG',
			'Adventure',
			'Horror'
		]];

		yield [[]];

		yield[[
			'MauvaisTag'
		]];

		yield [[
			'RPG',
			'MauvaisTag'
		]];

		yield [[
			'Horror'
		]];

	}
}
