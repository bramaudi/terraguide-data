<?php

namespace App\Command\Items;

use app\Command\Items\Get;
use Goutte\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;

class FetchDyes extends Command
{
    protected static $defaultName = 'fetch:dyes';

    public function configure()
    {   
        $this
        ->setDescription('Fetch data of dye\'s type item');
    }

    public function getColors(Crawler $crawler) : array
    {
        $list = $crawler->filter('table tr td:nth-child(2) span > span > span')->each(function (Crawler $node) {
            return $node->extract(['class'])[0] == null ? $node->text() : '';
        });
        
        $colors = [];
        foreach ($list as $dye) {
            if ($dye) $colors[] = $dye;
        }

        return $colors;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
        $headline = ['Basic Dyes', 'Bright Dyes', 'Gradient Dyes', 'Compound Dye', 'Compound Dye', 'Strange Dyes', 'Lunar Dyes', 'Other Dyes', 'Unobtainable Dyes'];
    
        $info = [
            'All basic dyes are crafted at the Dye Vat. Their crafting ingredients (not including Silver Dye and Brown Dye) can be found growing as background objects in the natural world or are guaranteed drops from special enemies. All dye plants show on the map. None of the Basic Dyes are visible in complete darkness.',
            'All colored basic dyes (except for black) can be combined with Silver Dye to craft Bright Dyes at the Dye Vat. None of the Bright Dyes are visible in complete darkness.',
            'These dyes produce fade effects between multiple colors. They are crafted at the Dye Vat. None of the Gradient Dyes are visible in complete darkness.',
            'These dyes form patterns with multiple colors, applied as separate blocks depending on the shape of the item. All colored basic dyes, and some "flame" gradient dyes, can be combined with Black or Silver Dye to craft "and Black Dye" or "and Silver Dye", respectively. They are crafted at the Dye Vat. None of the Compound Dyes are visible in complete darkness.',
            'These dyes form patterns with multiple colors, applied as separate blocks depending on the shape of the item. All colored basic dyes, and some "flame" gradient dyes, can be combined with Black or Silver Dye to craft "and Black Dye" or "and Silver Dye", respectively. They are crafted at the Dye Vat. None of the Compound Dyes are visible in complete darkness.',
            'These dye are given as quest rewards by the Dye Trader in exchange for Strange Plants. Dyes with a rarity level of Orange or Cyan can only be awarded in a Hardmode world. Strange Dyes have varying degrees of visibility in complete darkness.',
            'These animated dyes are crafted using Lunar Fragments at the Dye Vat. Solar Dye is the only Lunar Dye with any visibility in complete darkness',
            '',
            'The item(s) or effects described in this section exist as functional game items, but cannot be acquired through normal gameplay.'
        ];

        $get = new Get('Dyes');
        $crawler = $get->getCrawler('Dyes');
        $get->getTitle($crawler);
        $get->getInfo($crawler);
        $get->getStat($crawler);
        $get->getCraft($crawler, '.terraria.lined.align-center');
        $headline = $get->customCraft($headline, $info);
        $get->saveJson("_Dyes");

        foreach ($this->getColors($crawler) as $color) {
            $name = str_replace("'", '', $color);
            $name = str_replace("/", '_', $name);
            $get->json = ['refer' => '_Dyes'];
            $get->saveJson($name);
            $output->writeln("[<fg=green>Ok</>] $name.json ($headline)");
        }

        return Command::SUCCESS;
    }
}