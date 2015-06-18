<?php

namespace CrawlerBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    private $result;

    private $result_css;

    private function getWebsite(){
        $client = static::createClient();

        $client->request('POST', '/api/website/', array('website' => 'http://www.we-ma.com'));
        // Assert that the response status code is 2xx
        $this->assertTrue($client->getResponse()->isSuccessful());
        // Assert a specific 200 status code
        $this->assertEquals(
                200, // or Symfony\Component\HttpFoundation\Response::HTTP_OK
                $client->getResponse()->getStatusCode()
        );
        return json_decode($client->getResponse()->getContent(), true);
    }

    private function getCSS(){
        $client = static::createClient();

        $this->result = $this->getWebsite();

        $this->assertGreaterThan(0, $this->result['id']);

        $client->request('GET', '/api/website/'.$this->result['id'].'/'.$this->result['token']);

        return json_decode($client->getResponse()->getContent(), true);
    }
    public function testWebsite()
    {
        $this->result_css = $this->getCSS();

        $this->assertGreaterThan(0, count($this->result_css));

    }
    public function testCSS()
    {
        $client = static::createClient();

        $this->result_css = $this->getCSS();

        $this->assertGreaterThan(0, count($this->result_css));
        //var_dump($this->result_css);
        $client->request('POST', '/api/css/', array('id' => $this->result_css['css'][3]['id'], 'token' => $this->result_css['token']));

        $this->assertEquals(
                200,
                $client->getResponse()->getStatusCode()
        );

        $client->request('GET', '/api/css/'.$this->result_css['css'][3]['id'].'/'.$this->result_css['token']);

        $this->assertEquals(
                200,
                $client->getResponse()->getStatusCode()
        );
        $this->assertGreaterThan(0, count(json_decode($client->getResponse()->getContent(), true)));

    }
    public function testErrorWebsite()
    {
        $client = static::createClient();
        /**
         * Método no permitido
         */
        $crawler = $client->request('GET', '/api/website/', array('website' => 'http://atenea.solutions'));

        $this->assertEquals(
                405,
                $client->getResponse()->getStatusCode()
        );
        /**
         * No se pasó parámetros
         */
        $crawler = $client->request('POST', '/api/website/', array('website' => ''));

        $this->assertEquals(
                404,
                $client->getResponse()->getStatusCode()
        );
        /**
         * No existe la URL para crawl
         */
        $crawler = $client->request('POST', '/api/website/', array('website' => 'http://url-que-no-existe-blablabla-no-no-no.com'));

        $this->assertEquals(
                500,
                $client->getResponse()->getStatusCode()
        );
        /**
         * Método no permitido
         */
        $crawler = $client->request('POST', '/api/website/12/testtest', array());

        $this->assertEquals(
                405,
                $client->getResponse()->getStatusCode()
        );
        /**
         * No existe este (Token inválido)
         */
        $crawler = $client->request('POST', '/api/website/12/testtest', array());

        $this->assertEquals(
                405,
                $client->getResponse()->getStatusCode()
        );
    }
}
