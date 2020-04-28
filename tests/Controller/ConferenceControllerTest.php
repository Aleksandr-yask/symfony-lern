<?php
namespace App\Tests\Controller;

use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ConferenceControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
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
            'comment_form[email]' => $email = 'me@tester.ru',
            'comment_form[photo]' => dirname(__DIR__, 2).'/public/images/under_construction.git',
        ]);
        $this->assertResponseRedirects();

        $comment = self::$container->get(CommentRepository::class)->findOneByEmail($email);
        $comment->setState('published');
        self::$container->get(EntityManagerInterface::class)->flush();

        $client->followRedirect();
        $this->assertSelectorExists('p:contains("Feed back from tests")');

    }

    public function testMailerAssertions()
    {
        $client = static::createClient(); $client->request('GET', '/');
        $this->assertEmailCount(1);
        $event = $this->getMailerEvent(0);
        $this->assertEmailIsQueued($event);
        $email = $this->getMailerMessage(0);
        $this->assertEmailHeaderSame($email, 'To', 'fabien@example.com');
        $this->assertEmailTextBodyContains($email, 'Bar');
        $this->assertEmailAttachmentCount($email, 1);
    }

    #symfony run psql -c "INSERT INTO admin (id, username, roles, password) VALUES (nextval('admin_id_seq'), 'admin', '[\"ROLE_ADMIN\"]', '\$argon2id\$v=19\$m=65536,t=4,p=1\$VL/8ZWEQind/pYjnq5woaQ\$yGcPaoS23IVaLTEsUkVZLAi6h1DoE8nO75gr84isPB0')" -n
}
// $argon2id$v=19$m=65536,t=4,p=1$VL/8ZWEQind/pYjnq5woaQ$yGcPaoS23IVaLTEsUkVZLAi6h1DoE8nO75gr84isPB0