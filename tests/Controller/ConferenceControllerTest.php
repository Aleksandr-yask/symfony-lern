<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Panther\PantherTestCase;

class ConferenceControllerTest extends PantherTestCase
{
    public function testIndex()
    {
        $client = static::createPantherClient([
            'external_base_uri' => $_SERVER['SYMFONY_DEFAULT_ROUTE_URL']
        ]);
        $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Give your feedback');
    }


    public function testConferencePage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertCount(2, $crawler->filter('h4'));

        $client->clickLink('Amsterdam 2019');
        //$this->assertPageTitleContains('Amsterdam');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Amsterdam 2019 Conference');
        //$this->assertSelectorExists('div:contains("There are 1 comments")');
    }

    public function testCommentSubmission()
    {
        $client = static::createClient();
        $client->request('GET', '/conference/amsterdam-2019');
        $client->submitForm('Submit', [
            'comment_form[author]' => 'Redriget',
            'comment_form[text]' => 'Feed back from tests',
            'comment_form[email]' => 'me@tester.ru',
            'comment_form[photo]' => dirname(__DIR__, 2).'/public/images/under_construction.git',
        ]);
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorExists('p:contains("Feed back from tests")');

    }

    #symfony run psql -c "INSERT INTO admin (id, username, roles, password) VALUES (nextval('admin_id_seq'), 'admin', '[\"ROLE_ADMIN\"]', '\$argon2id\$v=19\$m=65536,t=4,p=1\$VL/8ZWEQind/pYjnq5woaQ\$yGcPaoS23IVaLTEsUkVZLAi6h1DoE8nO75gr84isPB0')" -n
}