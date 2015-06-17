<?php

namespace CrawlerBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    private $result;

    private $result_css;

    public function testSendWebsite()
    {
        $client = static::createClient();

        $crawler = $client->request('POST', '/api/website/', array('website' => 'http://www.we-ma.com'));
        // Assert that the response status code is 2xx
        $this->assertTrue($client->getResponse()->isSuccessful());
        // Assert a specific 200 status code
        $this->assertEquals(
            200, // or Symfony\Component\HttpFoundation\Response::HTTP_OK
            $client->getResponse()->getStatusCode()
        );
        $this->result = json_decode($client->getResponse()->getContent(), true);

        $this->assertGreaterThan(0, $this->result['id']);


        $crawler = $client->request('GET', '/api/website/'.$this->result['id'].'/'.$this->result['token']);

        $this->result_css = json_decode($client->getResponse()->getContent(), true);

        $this->assertGreaterThan(0, count($this->result_css));

    }
    public function testgetWebsiteCSS()
    {
        $client = static::createClient();

    }
    public function testErrorWebsite()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/api/website/', array('website' => 'http://atenea.solutions'));

        $this->assertEquals(
                405,
                $client->getResponse()->getStatusCode()
        );
        $crawler = $client->request('POST', '/api/website/', array('website' => ''));

        $this->assertEquals(
                404,
                $client->getResponse()->getStatusCode()
        );

        $crawler = $client->request('POST', '/api/website/', array('website' => 'http://url-que-no-existe-blablabla-no-no-no.com'));

        $this->assertEquals(
                500,
                $client->getResponse()->getStatusCode()
        );
    }
}
