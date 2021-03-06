<?php

namespace App\Command\Items;

use app\Command\Items\Get as GetItems;
use Goutte\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FetchFish extends Command
{
    protected static $defaultName = 'fetch:fish';

    public function configure()
    {
        $this
        ->setDescription('Fetch data of fish\'s type item');
    }

    public function getLists(OutputInterface $output) : array
    {
        $output->writeln('Mendapatkan data ...');
        $client = new Client();
        $crawler = $client->request('GET', 'https://terraria.gamepedia.com/Angler/Quests');

        $list = $crawler->filter('tr td.il2c > span > span > span')->each(function ($node, $i) {
            return $node->text();
        });

        $output->writeln('Data berhasil diunduh.');
        return $list;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $get = new GetItems('Angler/Quests');
        $crawler = $get->getCrawler('Angler/Quests');
        $get->getTitle($crawler);
        $get->getInfo($crawler);
        $get->getStat($crawler);
        $get->getCraft($crawler, 'table.terraria');
        $get->json['craft'][0]['title'] = 'Type';
        $get->saveJson('_Fish');

        foreach ($this->getLists($output) as $item) {
            if ($item) {
                $name = str_replace("'", '', $item);
                $name = str_replace("/", '_', $name);
                $get->json = ['refer' => '_Fish'];
                $get->saveJson($name);
                $output->writeln("[<fg=green>Ok</>] $name.json (Type)");
            }
        }

        return Command::SUCCESS;
    }
}