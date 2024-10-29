<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class ChartjsController extends AbstractController
{
    // Runescape experience table up to level 99
    private array $experienceTable = [
        1 => 0, 
        2 => 83, 
        3 => 174, 
        4 => 276, 
        5 => 388, 
        6 => 512, 
        7 => 650, 
        8 => 801, 
        9 => 969, 
        10 => 1154, 
        11 => 1358, 
        12 => 1584, 
        13 => 1833, 
        14 => 2107, 
        15 => 2411,
        16 => 2746, 
        17 => 3115, 
        18 => 3523, 
        19 => 3973, 
        20 => 4470, 
        21 => 5018, 
        22 => 5624, 
        23 => 6291, 
        24 => 7028, 
        25 => 7842, 
        26 => 8740, 
        27 => 9730, 
        28 => 10824,
        29 => 12031, 
        30 => 13363, 
        31 => 14833, 
        32 => 16456, 
        33 => 18247, 
        34 => 20224, 
        35 => 22406, 
        36 => 24815, 
        37 => 27473, 
        38 => 30408, 
        39 => 33648,
        40 => 37224, 
        41 => 41171, 
        42 => 45529, 
        43 => 50339, 
        44 => 55649, 
        45 => 61512, 
        46 => 67983, 
        47 => 75127, 
        48 => 83014, 
        49 => 91721, 
        50 => 101333,
        51 => 111945, 
        52 => 123660, 
        53 => 136594, 
        54 => 150872, 
        55 => 166636, 
        56 => 184040, 
        57 => 203254, 
        58 => 224466, 
        59 => 247886, 
        60 => 273742,
        61 => 302288, 
        62 => 333804, 
        63 => 368599, 
        64 => 407015, 
        65 => 449428, 
        66 => 496254, 
        67 => 547953, 
        68 => 605032, 
        69 => 668051, 
        70 => 737627,
        71 => 814445, 
        72 => 899257, 
        73 => 992895, 
        74 => 1096278, 
        75 => 1210421, 
        76 => 1336443, 
        77 => 1475581, 
        78 => 1629200, 
        79 => 1798808,
        80 => 1986068, 
        81 => 2192818, 
        82 => 2421087, 
        83 => 2673114, 
        84 => 2951373, 
        85 => 3258594, 
        86 => 3597792, 
        87 => 3972294, 
        88 => 4385776,
        89 => 4842295, 
        90 => 5346332, 
        91 => 5902831, 
        92 => 6517253, 
        93 => 7195629, 
        94 => 7944614, 
        95 => 8771558, 
        96 => 9684577, 
        97 => 10692629,
        98 => 11805606, 
        99 => 13034431
    ];

    #[Route('/chartjs', name: 'app_chartjs')]
    public function __invoke(ChartBuilderInterface $chartBuilder): Response
    {
        $levels = array_keys($this->experienceTable);
        $ancientRemainsExpPerLevel = [];
        array_walk($this->experienceTable, function (int $experience, int $level) use (&$ancientRemainsExpPerLevel) { 
            $ancientRemainsExpPerLevel[$level] = 170 * $level; 
        });

        $expPerLevelChart = $chartBuilder->createChart(Chart::TYPE_LINE);
        $expPerLevelChart->setData([
            'labels' => $levels,
            'datasets' => [
                [
                    'label' => 'Experience 📈',
                    'backgroundColor' => 'rgb(255, 99, 132, .4)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => array_values($this->experienceTable),
                    'tension' => 0.4,
                ]
            ],
        ]);
        $expPerLevelChart->setOptions([
            'maintainAspectRatio' => false,
        ]);


        $ancientRemainsExpPerLevelChart = $chartBuilder->createChart(Chart::TYPE_LINE);
        $ancientRemainsExpPerLevelChart->setData([
            'labels' => $levels,
            'datasets' => [
                [
                    'label' => 'Ancient remains 💀',
                    'backgroundColor' => 'rgb(111, 99, 132, .4)',
                    'borderColor' => 'rgb(133, 99, 257)',
                    'data' => array_values($ancientRemainsExpPerLevel),
                    'tension' => 0.7,
                ]
            ],
        ]);
        $ancientRemainsExpPerLevelChart->setOptions([
            'maintainAspectRatio' => false,
        ]);

        $currentLevel = 1;
        $currentExp = 0;
        $uses = [];
        $expFromRemainsPerLevel = [];


        while($currentExp < $this->experienceTable[99])
        {    
            if($currentExp < $this->experienceTable[$currentLevel+1])
            {
                $currentExp += $ancientRemainsExpPerLevel[$currentLevel];
                if(array_key_exists($currentLevel, $uses))
                {
                    $uses[$currentLevel] += 1;
                    $expFromRemainsPerLevel[$currentLevel] += $ancientRemainsExpPerLevel[$currentLevel]; 
                } else {
                    $uses[$currentLevel] = 1;
                    $expFromRemainsPerLevel[$currentLevel] = $ancientRemainsExpPerLevel[$currentLevel]; 
                }
            } else {
                $currentLevel += 1;
            }
            continue;
        }


        $totalAncientRemainsRequired = 0;
        array_walk($uses, function (int $nrTimesUsed, int $level) use (&$totalAncientRemainsRequired) {$totalAncientRemainsRequired += $nrTimesUsed;});


        $ancientRemainsTotalsChart = $chartBuilder->createChart(Chart::TYPE_LINE);
        $ancientRemainsTotalsChart->setData([
            'labels' => array_keys($uses),
            'datasets' => [
                [
                    'label' => 'Uses per level',
                    'backgroundColor' => 'rgb(0, 255, 255, .4)',
                    'borderColor' => 'rgb(0, 255, 255)',
                    'data' => array_values($uses),
                    'tension' => 0.7,
                ],
                // [
                //     'label' => 'Exp after each levels uses',
                //     'backgroundColor' => 'rgb(122, 99, 132, .4)',
                //     'borderColor' => 'rgb(200, 99, 257)',
                //     'data' => array_values($expFromRemainsPerLevel),
                //     'tension' => 0.7,
                // ],
            ],
        ]);
        $ancientRemainsTotalsChart->setOptions([
            'maintainAspectRatio' => false,
        ]);
        

        return $this->render('chartjs/chartjs.html.twig', [
            'expPerLevelChart' => $expPerLevelChart,
            'ancientRemainsExpPerLevelChart' => $ancientRemainsExpPerLevelChart,
            'ancientRemainsTotalsChart' => $ancientRemainsTotalsChart,
            'totalAncientRemainsRequired' => $totalAncientRemainsRequired
        ]);
    }
}
