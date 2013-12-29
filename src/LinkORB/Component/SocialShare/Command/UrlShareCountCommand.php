<?php

namespace LinkORB\Component\SocialShare\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use LinkORB\Component\SocialShare\Url as SocialShareUrl;

use Doctrine\Common\Cache\FilesystemCache;

class UrlShareCountCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('socialshare:urlsharecount')
            ->setDescription(
                'Report sharecounts for <url>'
            )
            ->addArgument(
                'url',
                InputArgument::REQUIRED,
                'URL to check'
            );
    }
    
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $url = $input->getArgument('url');
        $u = new SocialShareUrl($url);

        $cache = new FilesystemCache('/tmp/');
        $u->setCache($cache);

        print_r($u->getShareCounts());


    }
}
