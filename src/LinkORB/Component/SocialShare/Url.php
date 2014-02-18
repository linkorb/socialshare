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
        $info['googleplus'] = $this->getShareCount('googleplus');

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
            case "googleplus":
                $checkurl = "https://clients6.google.com/rpc";
                $postarray = array(
                    'method' => 'pos.plusones.get',
                    'id' => 'p',
                    'params' => array('nolog'=>true, 'id'=>$this->url, 'source'=>'widget', 'userId'=>'@viewer', 'groupId'=>'@self'),
                    'jsonrpc' => '2.0',
                    'key' => 'p',
                    'apiVersion' => 'v1'
                );


                $client = new GuzzleClient($checkurl);
                $request = $client->post('', array('Content-Type' => 'application/json'), json_encode(array($postarray)));
                $response = $request->send();
                $json = $response->getBody();
                $data = json_decode($json);

                $info['count'] = (int)$data[0]->result->metadata->globalCounts->count;
                $info['countlabel'] = $this->labelText($info['count']);

                /*
                Based on http://bradsknutson.com/blog/get-google-share-count-url/
                However: this seems to no longer work, as ripple pulls in the content through a second ajax request (it's not in the initial response body)
                
                $shares_url = 'https://plus.google.com/ripple/details?url='. urlencode($this->url);
                $response = file_get_contents($shares_url);
                echo $response;
                $shares_match = preg_match('@([0-9]+) public shares@',$response,$matches);
                $shares = $matches[1];
                echo $shares;

                return intval( $json[0]['result']['metadata']['globalCounts']['count'] );
                */

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
