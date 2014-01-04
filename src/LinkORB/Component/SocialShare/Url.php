<?php

namespace LinkORB\Component\SocialShare;

use Guzzle\Http\Client as GuzzleClient;
use Doctrine\Common\Cache\CacheProvider;
use InvalidArgumentException;

class Url
{
    private $url;

    public function __construct($url)
    {
        $this->setUrl($url);
    }
    public function setUrl($url)
    {
        $this->url = $url;
    }

    private $cache;
    private $cachelifetime = 60;

    public function setCacheLifetime($seconds)
    {
        $this->cacheLifetime = $seconds;
    }

    public function setCache(CacheProvider $cache)
    {
        $this->cache = $cache;
    }

    public function getShareCounts()
    {
        $info = array();
        $info['pinterest'] = $this->getShareCount('pinterest');
        $info['twitter'] = $this->getShareCount('twitter');
        $info['facebook'] = $this->getShareCount('facebook');
        $info['linkedin'] = $this->getShareCount('linkedin');
        return $info;
    }

    private function labelText($number)
    {

        if ($number<1000) {
            return (int)$number;
        }

        $label = round($number / 1000, 1) . 'K';
        return $label;

    }

    public function getShareCount($type)
    {
        $info = array();

        $cachekey = 'sharecount_' . sha1($this->url) . '_' . $type;

        if ($this->cache) {
            if ($this->cache->contains($cachekey)) {
                return $this->cache->fetch($cachekey);
            }
        }

        switch ($type) {
            case "pinterest":

                $checkurl = "http://api.pinterest.com/v1/urls/count.json?url=" . urlencode($this->url) . "&callback=receiveCount";

                $client = new GuzzleClient($checkurl);
                $request = $client->get('');
                $response = $request->send();
                $json = $response->getBody();

                $json = preg_replace('/^receiveCount\((.*)\)$/', "\\1", $json); // hack to fetch pure json data
                $data = json_decode($json);

                $info['count'] = (int)$data->count;
                $info['countlabel'] = $this->labelText($info['count']);

                break;
            case "twitter":
                $checkurl = "http://cdn.api.twitter.com/1/urls/count.json?url=" . urlencode($this->url);

                $client = new GuzzleClient($checkurl);
                $request = $client->get('');
                $response = $request->send();
                $json = $response->getBody();
                $data = json_decode($json);

                $info['count'] = (int)$data->count;
                $info['countlabel'] = $this->labelText($info['count']);

                break;

            case "facebook":
                $checkurl = "http://graph.facebook.com/" . urlencode($this->url);

                $client = new GuzzleClient($checkurl);
                $request = $client->get('');
                $response = $request->send();
                $json = $response->getBody();

                $data = json_decode($json);

                $info['id'] = $data->id;
                $info['comments'] = $data->comments;
                $info['shares'] = $data->shares;
                $info['likes'] = $data->likes;
                $info['talking_about_count'] = $data->talking_about_count;
                $info['were_here_count'] = $data->were_here_count;
                $info['count'] = (int)$data->shares + (int)$data->likes + (int)$data->comments;
                $info['countlabel'] = $this->labelText($info['count']);

                break;

            case "linkedin":
                $checkurl = "http://www.linkedin.com/countserv/count/share?url=" . urlencode($this->url) . "&callback=receiveCount";

                $client = new GuzzleClient($checkurl);
                $request = $client->get();
                $response = $request->send();
                $json = trim($response->getBody(), ";");
                $json = preg_replace('/^receiveCount\((.*)\)$/', "\\1", $json); // hack to fetch pure json data

                $data = json_decode($json);

                $info['count'] = (int)$data->count;
                $info['countlabel'] = $this->labelText($info['count']);
                break;
            default:
                throw new InvalidArgumentException('Unsupported network type');
        }

        if ($this->cache) {
            $this->cache->save($cachekey, $info, $this->cachelifetime);
        }

        return $info;
    }
}
