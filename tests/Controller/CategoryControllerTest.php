<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CategoryControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/categories');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Categories');
    }
}
