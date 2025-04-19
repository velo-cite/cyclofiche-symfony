<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class UserControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $userRepository;
    private string $path = '/admin/user/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->userRepository = $this->manager->getRepository(User::class);

        foreach ($this->userRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('User index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'user[password]' => 'Testing',
            'user[email]' => 'Testing',
            'user[roles]' => 'Testing',
            'user[firstname]' => 'Testing',
            'user[lastname]' => 'Testing',
            'user[phone]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->userRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new User();
        $fixture->setPassword('My Title');
        $fixture->setEmail('My Title');
        $fixture->setRoles('My Title');
        $fixture->setFirstname('My Title');
        $fixture->setLastname('My Title');
        $fixture->setPhone('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('User');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new User();
        $fixture->setPassword('Value');
        $fixture->setEmail('Value');
        $fixture->setRoles('Value');
        $fixture->setFirstname('Value');
        $fixture->setLastname('Value');
        $fixture->setPhone('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'user[password]' => 'Something New',
            'user[email]' => 'Something New',
            'user[roles]' => 'Something New',
            'user[firstname]' => 'Something New',
            'user[lastname]' => 'Something New',
            'user[phone]' => 'Something New',
        ]);

        self::assertResponseRedirects('/admin/user/');

        $fixture = $this->userRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getPassword());
        self::assertSame('Something New', $fixture[0]->getEmail());
        self::assertSame('Something New', $fixture[0]->getRoles());
        self::assertSame('Something New', $fixture[0]->getFirstname());
        self::assertSame('Something New', $fixture[0]->getLastname());
        self::assertSame('Something New', $fixture[0]->getPhone());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new User();
        $fixture->setPassword('Value');
        $fixture->setEmail('Value');
        $fixture->setRoles('Value');
        $fixture->setFirstname('Value');
        $fixture->setLastname('Value');
        $fixture->setPhone('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/admin/user/');
        self::assertSame(0, $this->userRepository->count([]));
    }
}
