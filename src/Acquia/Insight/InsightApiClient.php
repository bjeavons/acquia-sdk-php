<?php

namespace Acquia\Insight;

use Acquia\Rest\ServiceManagerAware;
use Guzzle\Common\Collection;
use Acquia\Json\Json;
use Guzzle\Service\Client;

class InsightApiClient extends Client implements ServiceManagerAware
{
    const BASE_URL         = 'https://api.insight.acquia.com';
    const BASE_PATH        = '/api/v1';

    /**
     * {@inheritdoc}
     *
     * @return \Acquia\Insight\InsightApiClient
     */
    public static function factory($config = array())
    {
        $required = array(
            'base_url',
            'username',
            'password',
        );

        $defaults = array(
            'base_url' => self::BASE_URL,
            'base_path' => self::BASE_PATH,
        );

        // Instantiate the Acquia Insight API plugin.
        $config = Collection::fromConfig($config, $defaults, $required);
        $client = new static($config->get('base_url'), $config);
        $client->setDefaultHeaders(array(
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ));

        $plugin = new InsightApiAuthPlugin($config->get('username'), $config->get('password'));
        $client->addSubscriber($plugin);

        return $client;
    }

    /**
     * {@inheritdoc}
     */
    public function getBuilderParams()
    {
        return array(
            'base_url' => $this->getConfig('base_url'),
            'username' => $this->getConfig('username'),
            'password' => $this->getConfig('password'),
        );
    }

    /**
     * @return array
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function getSubscriptions()
    {
        $result = $this->call('{+base_path}/subscriptions');
        return $result;
    }

    /**
     * @param string $subscription_uuid
     * @return array
     */
    public function getSitesBySubscription($subscription_uuid)
    {
        $result = $this->call('{+base_path}/subscriptions/' . $subscription_uuid . '/sites');
        return $result;
    }

    /**
     * @param string $site_uuid
     * @return array
     */
    public function getSite($site_uuid)
    {
        $result = $this->call('{+base_path}/sites/' . $site_uuid);
        return $result;
    }

    /**
     * @param string $site_uuid
     * @return array
     */
    public function getScore($site_uuid)
    {
        $result = $this->call('{+base_path}/sites/' . $site_uuid . '/score');
        return $result;
    }

    /**
     * @param string $site_uuid
     * @return array
     */
    public function getScoreHistory($site_uuid)
    {
        $result = $this->call('{+base_path}/sites/' . $site_uuid . '/score-history');
        return $result;
    }

    /**
     * @param string $site_uuid
     * @return array
     */
    public function getAlerts($site_uuid)
    {
        $result = $this->call('{+base_path}/sites/' . $site_uuid . '/alerts');
        return $result;
    }

    /**
     * Make a request and return the response.
     *
     * @param string $path
     * @return array|bool|float|int|string
     */
    protected function call($path)
    {
        $request = $this->get($path);
        return $request->send()->json();
    }
}