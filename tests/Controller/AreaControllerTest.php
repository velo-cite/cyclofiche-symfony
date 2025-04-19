<?php

namespace App\Tests\Controller;

use App\Entity\Area;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class AreaControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $areaRepository;
    private string $path = '/admin/area/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->areaRepository = $this->manager->getRepository(Area::class);

        foreach ($this->areaRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Area index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'area[libelle]' => 'Testing',
            'area[coordinates]' => 'Testing',
            'area[organisations]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->areaRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Area();
        $fixture->setLibelle('My Title');
        $fixture->setCoordinates('My Title');
        $fixture->setOrganisations('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Area');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Area();
        $fixture->setLibelle('Value');
        $fixture->setCoordinates('Value');
        $fixture->setOrganisations('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'area[libelle]' => 'Something New',
            'area[coordinates]' => 'Something New',
            'area[organisations]' => 'Something New',
        ]);

        self::assertResponseRedirects('/admin/area/');

        $fixture = $this->areaRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getLibelle());
        self::assertSame('Something New', $fixture[0]->getCoordinates());
        self::assertSame('Something New', $fixture[0]->getOrganisations());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Area();
        $fixture->setLibelle('Value');
        $fixture->setCoordinates('Value');
        $fixture->setOrganisations('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/admin/area/');
        self::assertSame(0, $this->areaRepository->count([]));
    }
}
