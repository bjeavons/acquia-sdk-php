<?php

namespace Acquia\Test\Insight;

use Acquia\Insight\InsightApiClient;
use Acquia\Insight\InsightApiAuthPlugin;

class InsightApiClientTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @param string|null $responseFile
     * @param int $responseCode
     *
     * @return \Acquia\Insight\InsightApiClient
     */
    public function getInsightApiClient($responseFile = null, $responseCode = 200)
    {
        $insight_api = InsightApiClient::factory(array(
            'base_url' => 'https://insight.api.example.com',
            'username' => 'test-username',
            'password' => 'test-password',
        ));

        $this->requestListener = new InsightApiRequestListener();
        $insight_api->getEventDispatcher()->addSubscriber($this->requestListener);

        if ($responseFile !== null) {
            $this->addMockResponse($insight_api, $responseFile, $responseCode);
        }

        return $insight_api;
    }

    public function testSubscriptions()
    {
        $insight_api = $this->getInsightApiClient(__DIR__ . '/mock-responses/subscriptions.json');
        $response = $insight_api->getSubscriptions();

        $expectedResponse = array(
            "401325b5-326d-7a84-b1df-40ae95fb45f5" => array(
                "name" => "site.example.com"
            ),
            "40133b14-ee0c-102e-83df-1231390f2cc1" => array(
               "name" => "example.com"
            ),
        );

        $this->assertSame($expectedResponse, $response);
    }

    public function testSites()
    {
        $insight_api = $this->getInsightApiClient(__DIR__ . '/mock-responses/sites.json');
        $response = $insight_api->getSubscriptions();

        $expectedResponse = array(
            "fe6d03a8-0c6d-11e4-9ea8-4c8e79f3c2ce" => array(
                "host"=> "example.com",
                "ip"=> "127.0.0.1",
                "last_update"=> "1374790424",
                "code_check"=> true,
                "config_check"=> true
            ),
            "fdab82a6-0c6d-11e4-9ae8-4a8d79f3c2ce" => array(
                "host"=> "stage.example.com",
                "ip"=> "127.0.0.1",
                "last_update"=> "1382473288",
                "code_check"=> true,
                "config_check"=> true
            ),
        );

        $this->assertSame($expectedResponse, $response);
    }

    public function testSite()
    {
        $insight_api = $this->getInsightApiClient(__DIR__ . '/mock-responses/site.json');
        $response = $insight_api->getSubscriptions();

        $expectedResponse = array(
            "host"=> "example.com",
            "ip"=> "127.0.0.1",
            "last_update"=> "1374790424",
            "code_check"=> true,
            "config_check"=> true
        );

        $this->assertSame($expectedResponse, $response);
    }

    public function testScore()
    {
        $insight_api = $this->getInsightApiClient(__DIR__ . '/mock-responses/score.json');
        $response = $insight_api->getSubscriptions();

        $expectedResponse = array(
            "score" => "80",
            "last_update" => "1374790424",
        );

        $this->assertSame($expectedResponse, $response);
    }

    public function testScoreHistory()
    {
        $insight_api = $this->getInsightApiClient(__DIR__ . '/mock-responses/score-history.json');
        $response = $insight_api->getSubscriptions();

        $expectedResponse = array(
            "1374790424" => array(
                "score" => "80"
            ),
            "1382473288" => array(
                "score" => "65"
            )
        );

        $this->assertSame($expectedResponse, $response);
    }

    public function testAlerts()
    {
        $insight_api = $this->getInsightApiClient(__DIR__ . '/mock-responses/alerts.json');
        $response = $insight_api->getSubscriptions();

        $expectedResponse = array(
            "f93dbb19-a6a0-11e2-b0d3-12313931d529" => array(
                "title"=> "No variables sent - Limited alerts",
                "ignored"=> false,
                "solved"=> false,
                "types" => array(
                    "best_practices",
                    "general",
                    "high_level"
                ),
                "severity"=> "128"
            ),
            "f93dbb1b-a6a0-11e2-b0d3-12313931d529" => array(
                "title" => "Anonymous users have admin privileges",
                "ignored" => false,
                "solved"=> false,
                "types" => array(
                    "general",
                    "security",
                    "high_level"
                ),
                "severity"=> "128"
            )
        );

        $this->assertSame($expectedResponse, $response);
    }

    /**
     * @param \Acquia\Insight\InsightApiClient $insight_api
     * @param string $responseFile
     * @param int $responseCode
     */
    public function addMockResponse(InsightApiClient $insight_api, $responseFile, $responseCode)
    {
        $mock = new \Guzzle\Plugin\Mock\MockPlugin();

        $response = new \Guzzle\Http\Message\Response($responseCode);
        if (is_string($responseFile)) {
            $response->setBody(file_get_contents($responseFile));
        }

        $mock->addResponse($response);
        $insight_api->addSubscriber($mock);
    }
}