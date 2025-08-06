<?php

declare(strict_types=1);

namespace App\Tests\Functional\VideoGame;

use App\Model\Entity\Tag;
use App\Model\Entity\VideoGame;
use App\Tests\Functional\FunctionalTestCase;
use Symfony\Component\DomCrawler\Form;


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
	 * @param array $dataTag
	 * @return void
	 * @dataProvider tagsProviders
	 */
	public function testFilterVideoGammesByTags(array $dataTag):void
	{
		$this->get('/');
		self::assertResponseIsSuccessful();

		$crawler = $this->client->getCrawler();
		$form = $crawler->selectButton('Filtrer')->form();

		$doctrine = $this->client->getContainer()->get('doctrine.orm.entity_manager');
		$tagRepo = $doctrine->getRepository(Tag::class);
		$gamesRepo = $doctrine->getRepository(VideoGame::class);

		$allTags = $tagRepo->findAll();
		$allTagNames = array_map(static fn(Tag $tag) => $tag->getName(), $allTags);
		$badTags = array_filter($dataTag, static function (string $tagName) use ($allTagNames) {
			return !in_array($tagName, $allTagNames, true);
		});

		$expectedGames = 50;
		if (empty($badTags)){

			$values = [];
			foreach ($dataTag as $tagName) {
				/** @var Tag|null $tag */
				$tag = $tagRepo->findOneByName($tagName);
				$values[] = (string)$tag->getId();
			}
			$this->get('/', [
				'filter' => ['tags' => $values],
			]);
			self::assertResponseIsSuccessful();

			$expectedGames = 0;
			$games = $gamesRepo->findAll();

			foreach ($games as $game) {
				$gameTags = $game->getTags();
				$gameTagNames = array_map(static fn($tag) => $tag->getName(), $gameTags->toArray());
				$match = true;
				foreach($dataTag as $data){
					if (!in_array($data, $gameTagNames, true)){
						$match = false;
						break;
					}
				}

				if ($match === true){
					$expectedGames++;
				}

			}
		}

		self::assertSelectorTextContains('div.fw-bold',"sur les $expectedGames jeux vidéo");

	}

	public static function tagsProviders(): \Generator
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

