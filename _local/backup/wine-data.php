<?php

function _wineSpecs($ref, $targetVol, $refVol) {
    $ratio = $targetVol / $refVol;
    $cr = pow($ratio, 1/3);
    $sr = pow($ratio, 2/3);

    $diameter = max(200, round($ref['diameter'] * $cr));
    $height = max(300, round($ref['height'] * $cr));

    if ($targetVol < 500) $wall = 2;
    elseif ($targetVol < 3000) $wall = 3;
    elseif ($targetVol < 10000) $wall = 3;
    elseif ($targetVol < 50000) $wall = 4;
    elseif ($targetVol < 100000) $wall = 5;
    else $wall = 6;

    $weight = max(round($ref['weight'] * $sr), 10);
    $power = round($ref['power'] * $sr, 1);

    $priceScale = pow($ratio, 0.78);
    $price = max(round($ref['price'] * $priceScale / 500) * 500, 5000);
    $price = round($price * 1.3);

    $full_volume = round(pi() * pow($diameter / 2, 2) * $height / 1000);
    $working_volume = round($full_volume * 0.8);

    return compact('diameter', 'height', 'wall', 'weight', 'power', 'price', 'full_volume', 'working_volume');
}

function _wineVolumes(&$data) {
    foreach ($data as $cat => &$d) {
        if (empty($d['spec_ref'])) continue;
        $refVol = key($d['spec_ref']);
        $refSpec = $d['spec_ref'][$refVol];
        $d['specs'] = [];
        foreach ($d['volumes'] as $v) {
            $d['specs'][$v] = _wineSpecs($refSpec, $v, $refVol);
        }
    }
    unset($d);
}

$wineData = [
    'specs' => [
        500 => [
            'diameter' => 800,
            'height' => 1400,
            'wall' => 2,
            'weight' => 85,
            'power' => 4.0,
            'price' => 237405,
            'full_volume' => 703717,
            'working_volume' => 562974
        ],
        1000 => [
            'diameter' => 1008,
            'height' => 1764,
            'wall' => 2,
            'weight' => 135,
            'power' => 6.3,
            'price' => 292585,
            'full_volume' => 1407698,
            'working_volume' => 1126158
        ],
        1500 => [
            'diameter' => 1154,
            'height' => 2019,
            'wall' => 2,
            'weight' => 177,
            'power' => 8.3,
            'price' => 339150,
            'full_volume' => 2111727,
            'working_volume' => 1689382
        ],
        2000 => [
            'diameter' => 1270,
            'height' => 2222,
            'wall' => 2,
            'weight' => 214,
            'power' => 10.1,
            'price' => 386469,
            'full_volume' => 2814760,
            'working_volume' => 2251808
        ],
        3000 => [
            'diameter' => 1454,
            'height' => 2544,
            'wall' => 3,
            'weight' => 281,
            'power' => 13.2,
            'price' => 452724,
            'full_volume' => 4224116,
            'working_volume' => 3379293
        ],
        4000 => [
            'diameter' => 1600,
            'height' => 2800,
            'wall' => 3,
            'weight' => 340,
            'power' => 16.0,
            'price' => 524875,
            'full_volume' => 5629734,
            'working_volume' => 4503787
        ],
        5000 => [
            'diameter' => 1724,
            'height' => 3016,
            'wall' => 3,
            'weight' => 395,
            'power' => 18.6,
            'price' => 624577,
            'full_volume' => 7040374,
            'working_volume' => 5632299
        ],
        6000 => [
            'diameter' => 1832,
            'height' => 3205,
            'wall' => 3,
            'weight' => 446,
            'power' => 21.0,
            'price' => 726750,
            'full_volume' => 8448291,
            'working_volume' => 6758633
        ],
        6300 => [
            'diameter' => 1862,
            'height' => 3258,
            'wall' => 3,
            'weight' => 460,
            'power' => 21.7,
            'price' => 742900,
            'full_volume' => 8871567,
            'working_volume' => 7097254
        ],
        8000 => [
            'diameter' => 2016,
            'height' => 3528,
            'wall' => 3,
            'weight' => 540,
            'power' => 25.4,
            'price' => 888250,
            'full_volume' => 11261585,
            'working_volume' => 9009268
        ],
        10000 => [
            'diameter' => 2172,
            'height' => 3800,
            'wall' => 4,
            'weight' => 626,
            'power' => 29.5,
            'price' => 844322,
            'full_volume' => 14079691,
            'working_volume' => 11263753
        ],
        12500 => [
            'diameter' => 2339,
            'height' => 4094,
            'wall' => 4,
            'weight' => 727,
            'power' => 34.2,
            'price' => 995916,
            'full_volume' => 17591309,
            'working_volume' => 14073047
        ],
        15000 => [
            'diameter' => 2486,
            'height' => 4350,
            'wall' => 4,
            'weight' => 821,
            'power' => 38.6,
            'price' => 1130500,
            'full_volume' => 21114528,
            'working_volume' => 16891622
        ],
        16000 => [
            'diameter' => 2540,
            'height' => 4445,
            'wall' => 4,
            'weight' => 857,
            'power' => 40.3,
            'price' => 1195100,
            'full_volume' => 22523147,
            'working_volume' => 18018518
        ],
        20000 => [
            'diameter' => 2736,
            'height' => 4788,
            'wall' => 4,
            'weight' => 994,
            'power' => 46.8,
            'price' => 1453500,
            'full_volume' => 28149858,
            'working_volume' => 22519886
        ],
        25000 => [
            'diameter' => 2947,
            'height' => 5158,
            'wall' => 4,
            'weight' => 1154,
            'power' => 54.3,
            'price' => 1776500,
            'full_volume' => 35182888,
            'working_volume' => 28146310
        ],
        30000 => [
            'diameter' => 3132,
            'height' => 5481,
            'wall' => 4,
            'weight' => 1303,
            'power' => 61.3,
            'price' => 2261000,
            'full_volume' => 42227288,
            'working_volume' => 33781830
        ],
        31500 => [
            'diameter' => 3183,
            'height' => 5571,
            'wall' => 4,
            'weight' => 1346,
            'power' => 63.3,
            'price' => 2357900,
            'full_volume' => 44329856,
            'working_volume' => 35463885
        ],
        40000 => [
            'diameter' => 3447,
            'height' => 6032,
            'wall' => 4,
            'weight' => 1578,
            'power' => 74.3,
            'price' => 2907000,
            'full_volume' => 56290328,
            'working_volume' => 45032262
        ],
        50000 => [
            'diameter' => 3713,
            'height' => 6498,
            'wall' => 5,
            'weight' => 1831,
            'power' => 86.2,
            'price' => 3633750,
            'full_volume' => 70358972,
            'working_volume' => 56287178
        ]
    ],
    ],
    'specs' => [
        500 => [
            'diameter' => 700,
            'height' => 1500,
            'wall' => 2,
            'weight' => 80,
            'power' => 4.0,
            'price' => 237405,
            'full_volume' => 577268,
            'working_volume' => 461814
        ],
        1000 => [
            'diameter' => 882,
            'height' => 1890,
            'wall' => 2,
            'weight' => 127,
            'power' => 6.3,
            'price' => 292585,
            'full_volume' => 1154752,
            'working_volume' => 923802
        ],
        1500 => [
            'diameter' => 1010,
            'height' => 2163,
            'wall' => 2,
            'weight' => 166,
            'power' => 8.3,
            'price' => 339150,
            'full_volume' => 1732962,
            'working_volume' => 1386370
        ],
        2000 => [
            'diameter' => 1111,
            'height' => 2381,
            'wall' => 2,
            'weight' => 202,
            'power' => 10.1,
            'price' => 386469,
            'full_volume' => 2308221,
            'working_volume' => 1846577
        ],
        3000 => [
            'diameter' => 1272,
            'height' => 2726,
            'wall' => 3,
            'weight' => 264,
            'power' => 13.2,
            'price' => 452724,
            'full_volume' => 3464096,
            'working_volume' => 2771277
        ],
        4000 => [
            'diameter' => 1400,
            'height' => 3000,
            'wall' => 3,
            'weight' => 320,
            'power' => 16.0,
            'price' => 524875,
            'full_volume' => 4618141,
            'working_volume' => 3694513
        ],
        5000 => [
            'diameter' => 1508,
            'height' => 3232,
            'wall' => 3,
            'weight' => 371,
            'power' => 18.6,
            'price' => 624577,
            'full_volume' => 5772500,
            'working_volume' => 4618000
        ],
        6000 => [
            'diameter' => 1603,
            'height' => 3434,
            'wall' => 3,
            'weight' => 419,
            'power' => 21.0,
            'price' => 726750,
            'full_volume' => 6930383,
            'working_volume' => 5544306
        ],
        6300 => [
            'diameter' => 1629,
            'height' => 3490,
            'wall' => 3,
            'weight' => 433,
            'power' => 21.7,
            'price' => 742900,
            'full_volume' => 7273735,
            'working_volume' => 5818988
        ],
        8000 => [
            'diameter' => 1764,
            'height' => 3780,
            'wall' => 3,
            'weight' => 508,
            'power' => 25.4,
            'price' => 888250,
            'full_volume' => 9238019,
            'working_volume' => 7390415
        ],
        10000 => [
            'diameter' => 1900,
            'height' => 4072,
            'wall' => 4,
            'weight' => 589,
            'power' => 29.5,
            'price' => 844322,
            'full_volume' => 11545290,
            'working_volume' => 9236232
        ],
        12500 => [
            'diameter' => 2047,
            'height' => 4386,
            'wall' => 4,
            'weight' => 684,
            'power' => 34.2,
            'price' => 995916,
            'full_volume' => 14434249,
            'working_volume' => 11547399
        ],
        15000 => [
            'diameter' => 2175,
            'height' => 4661,
            'wall' => 4,
            'weight' => 772,
            'power' => 38.6,
            'price' => 1130500,
            'full_volume' => 17317592,
            'working_volume' => 13854074
        ],
        16000 => [
            'diameter' => 2222,
            'height' => 4762,
            'wall' => 4,
            'weight' => 806,
            'power' => 40.3,
            'price' => 1195100,
            'full_volume' => 18465768,
            'working_volume' => 14772614
        ],
        20000 => [
            'diameter' => 2394,
            'height' => 5130,
            'wall' => 4,
            'weight' => 936,
            'power' => 46.8,
            'price' => 1453500,
            'full_volume' => 23091680,
            'working_volume' => 18473344
        ],
        25000 => [
            'diameter' => 2579,
            'height' => 5526,
            'wall' => 4,
            'weight' => 1086,
            'power' => 54.3,
            'price' => 1776500,
            'full_volume' => 28867119,
            'working_volume' => 23093695
        ],
        31500 => [
            'diameter' => 2785,
            'height' => 5969,
            'wall' => 4,
            'weight' => 1267,
            'power' => 63.3,
            'price' => 2357900,
            'full_volume' => 36361506,
            'working_volume' => 29089205
        ]
    ],
    ],
    'specs' => [
        1000 => [
            'diameter' => 877,
            'height' => 1754,
            'wall' => 2,
            'weight' => 120,
            'power' => 0.0,
            'price' => 206530,
            'full_volume' => 1059543,
            'working_volume' => 847634
        ],
        1500 => [
            'diameter' => 1004,
            'height' => 2008,
            'wall' => 2,
            'weight' => 157,
            'power' => 0.0,
            'price' => 239400,
            'full_volume' => 1589721,
            'working_volume' => 1271777
        ],
        2000 => [
            'diameter' => 1105,
            'height' => 2210,
            'wall' => 2,
            'weight' => 190,
            'power' => 0.0,
            'price' => 272802,
            'full_volume' => 2119370,
            'working_volume' => 1695496
        ],
        3000 => [
            'diameter' => 1265,
            'height' => 2530,
            'wall' => 3,
            'weight' => 249,
            'power' => 0.0,
            'price' => 319570,
            'full_volume' => 3179739,
            'working_volume' => 2543791
        ],
        4000 => [
            'diameter' => 1392,
            'height' => 2785,
            'wall' => 3,
            'weight' => 302,
            'power' => 0.0,
            'price' => 370500,
            'full_volume' => 4238318,
            'working_volume' => 3390654
        ],
        5000 => [
            'diameter' => 1500,
            'height' => 3000,
            'wall' => 3,
            'weight' => 350,
            'power' => 0.0,
            'price' => 440877,
            'full_volume' => 5301438,
            'working_volume' => 4241150
        ],
        6300 => [
            'diameter' => 1620,
            'height' => 3240,
            'wall' => 3,
            'weight' => 408,
            'power' => 0.0,
            'price' => 524400,
            'full_volume' => 6678285,
            'working_volume' => 5342628
        ],
        8000 => [
            'diameter' => 1754,
            'height' => 3509,
            'wall' => 3,
            'weight' => 479,
            'power' => 0.0,
            'price' => 627000,
            'full_volume' => 8478762,
            'working_volume' => 6783010
        ],
        10000 => [
            'diameter' => 1890,
            'height' => 3780,
            'wall' => 4,
            'weight' => 556,
            'power' => 0.0,
            'price' => 595992,
            'full_volume' => 10604869,
            'working_volume' => 8483895
        ],
        12500 => [
            'diameter' => 2036,
            'height' => 4072,
            'wall' => 4,
            'weight' => 645,
            'power' => 0.0,
            'price' => 702999,
            'full_volume' => 13257242,
            'working_volume' => 10605794
        ],
        15000 => [
            'diameter' => 2163,
            'height' => 4327,
            'wall' => 4,
            'weight' => 728,
            'power' => 0.0,
            'price' => 798000,
            'full_volume' => 15899732,
            'working_volume' => 12719786
        ],
        16000 => [
            'diameter' => 2210,
            'height' => 4421,
            'wall' => 4,
            'weight' => 760,
            'power' => 0.0,
            'price' => 843600,
            'full_volume' => 16958793,
            'working_volume' => 13567034
        ],
        20000 => [
            'diameter' => 2381,
            'height' => 4762,
            'wall' => 4,
            'weight' => 882,
            'power' => 0.0,
            'price' => 1026000,
            'full_volume' => 21203037,
            'working_volume' => 16962430
        ],
        25000 => [
            'diameter' => 2565,
            'height' => 5130,
            'wall' => 4,
            'weight' => 1023,
            'power' => 0.0,
            'price' => 1254000,
            'full_volume' => 26508307,
            'working_volume' => 21206646
        ],
        30000 => [
            'diameter' => 2726,
            'height' => 5451,
            'wall' => 4,
            'weight' => 1156,
            'power' => 0.0,
            'price' => 1596000,
            'full_volume' => 31813963,
            'working_volume' => 25451170
        ],
        31500 => [
            'diameter' => 2770,
            'height' => 5541,
            'wall' => 4,
            'weight' => 1194,
            'power' => 0.0,
            'price' => 1664400,
            'full_volume' => 33391626,
            'working_volume' => 26713301
        ],
        40000 => [
            'diameter' => 3000,
            'height' => 6000,
            'wall' => 4,
            'weight' => 1400,
            'power' => 0.0,
            'price' => 2052000,
            'full_volume' => 42411501,
            'working_volume' => 33929201
        ],
        50000 => [
            'diameter' => 3232,
            'height' => 6463,
            'wall' => 5,
            'weight' => 1625,
            'power' => 0.0,
            'price' => 2565000,
            'full_volume' => 53023299,
            'working_volume' => 42418639
        ],
        63000 => [
            'diameter' => 3490,
            'height' => 6981,
            'wall' => 5,
            'weight' => 1895,
            'power' => 0.0,
            'price' => 3280350,
            'full_volume' => 66781839,
            'working_volume' => 53425471
        ],
        80000 => [
            'diameter' => 3780,
            'height' => 7560,
            'wall' => 5,
            'weight' => 2222,
            'power' => 0.0,
            'price' => 4104000,
            'full_volume' => 84838948,
            'working_volume' => 67871158
        ],
        100000 => [
            'diameter' => 4072,
            'height' => 8143,
            'wall' => 6,
            'weight' => 2579,
            'power' => 0.0,
            'price' => 5130000,
            'full_volume' => 106044917,
            'working_volume' => 84835934
        ],
        125000 => [
            'diameter' => 4386,
            'height' => 8772,
            'wall' => 6,
            'weight' => 2992,
            'power' => 0.0,
            'price' => 6555000,
            'full_volume' => 132533528,
            'working_volume' => 106026822
        ],
        160000 => [
            'diameter' => 4762,
            'height' => 9524,
            'wall' => 6,
            'weight' => 3528,
            'power' => 0.0,
            'price' => 8493000,
            'full_volume' => 169624293,
            'working_volume' => 135699434
        ],
        200000 => [
            'diameter' => 5130,
            'height' => 10260,
            'wall' => 6,
            'weight' => 4094,
            'power' => 0.0,
            'price' => 10545000,
            'full_volume' => 212066453,
            'working_volume' => 169653162
        ]
    ],
    ],
    'specs' => [
        500 => [
            'diameter' => 714,
            'height' => 1270,
            'wall' => 2,
            'weight' => 82,
            'power' => 3.8,
            'price' => 237405,
            'full_volume' => 508499,
            'working_volume' => 406799
        ],
        1000 => [
            'diameter' => 900,
            'height' => 1600,
            'wall' => 2,
            'weight' => 130,
            'power' => 6.0,
            'price' => 292585,
            'full_volume' => 1017876,
            'working_volume' => 814301
        ],
        1500 => [
            'diameter' => 1030,
            'height' => 1832,
            'wall' => 2,
            'weight' => 170,
            'power' => 7.9,
            'price' => 339150,
            'full_volume' => 1526475,
            'working_volume' => 1221180
        ],
        2000 => [
            'diameter' => 1134,
            'height' => 2016,
            'wall' => 2,
            'weight' => 206,
            'power' => 9.5,
            'price' => 386469,
            'full_volume' => 2036135,
            'working_volume' => 1628908
        ],
        3000 => [
            'diameter' => 1298,
            'height' => 2308,
            'wall' => 3,
            'weight' => 270,
            'power' => 12.5,
            'price' => 452724,
            'full_volume' => 3054042,
            'working_volume' => 2443234
        ],
        4000 => [
            'diameter' => 1429,
            'height' => 2540,
            'wall' => 3,
            'weight' => 328,
            'power' => 15.1,
            'price' => 524875,
            'full_volume' => 4073691,
            'working_volume' => 3258953
        ],
        5000 => [
            'diameter' => 1539,
            'height' => 2736,
            'wall' => 3,
            'weight' => 380,
            'power' => 17.5,
            'price' => 624577,
            'full_volume' => 5089595,
            'working_volume' => 4071676
        ],
        6000 => [
            'diameter' => 1635,
            'height' => 2907,
            'wall' => 3,
            'weight' => 429,
            'power' => 19.8,
            'price' => 726750,
            'full_volume' => 6103380,
            'working_volume' => 4882704
        ],
        6300 => [
            'diameter' => 1662,
            'height' => 2955,
            'wall' => 3,
            'weight' => 443,
            'power' => 20.5,
            'price' => 742900,
            'full_volume' => 6410758,
            'working_volume' => 5128606
        ],
        8000 => [
            'diameter' => 1800,
            'height' => 3200,
            'wall' => 3,
            'weight' => 520,
            'power' => 24.0,
            'price' => 888250,
            'full_volume' => 8143008,
            'working_volume' => 6514406
        ],
        10000 => [
            'diameter' => 1939,
            'height' => 3447,
            'wall' => 4,
            'weight' => 603,
            'power' => 27.8,
            'price' => 844322,
            'full_volume' => 10178570,
            'working_volume' => 8142856
        ],
        12500 => [
            'diameter' => 2089,
            'height' => 3713,
            'wall' => 4,
            'weight' => 700,
            'power' => 32.3,
            'price' => 995916,
            'full_volume' => 12725994,
            'working_volume' => 10180795
        ],
        15000 => [
            'diameter' => 2220,
            'height' => 3946,
            'wall' => 4,
            'weight' => 791,
            'power' => 36.5,
            'price' => 1130500,
            'full_volume' => 15274004,
            'working_volume' => 12219203
        ],
        16000 => [
            'diameter' => 2268,
            'height' => 4032,
            'wall' => 4,
            'weight' => 825,
            'power' => 38.1,
            'price' => 1195100,
            'full_volume' => 16289078,
            'working_volume' => 13031262
        ],
        20000 => [
            'diameter' => 2443,
            'height' => 4343,
            'wall' => 4,
            'weight' => 958,
            'power' => 44.2,
            'price' => 1453500,
            'full_volume' => 20357603,
            'working_volume' => 16286082
        ],
        25000 => [
            'diameter' => 2632,
            'height' => 4678,
            'wall' => 4,
            'weight' => 1111,
            'power' => 51.3,
            'price' => 1776500,
            'full_volume' => 25451997,
            'working_volume' => 20361598
        ],
        31500 => [
            'diameter' => 2842,
            'height' => 5053,
            'wall' => 4,
            'weight' => 1297,
            'power' => 59.8,
            'price' => 2357900,
            'full_volume' => 32054376,
            'working_volume' => 25643501
        ]
    ],
    ],
    'specs' => [
        1000 => [
            'diameter' => 900,
            'height' => 1500,
            'wall' => 2,
            'weight' => 105,
            'power' => 3.0,
            'price' => 240952,
            'full_volume' => 954259,
            'working_volume' => 763407
        ],
        1500 => [
            'diameter' => 1030,
            'height' => 1717,
            'wall' => 2,
            'weight' => 138,
            'power' => 3.9,
            'price' => 279300,
            'full_volume' => 1430654,
            'working_volume' => 1144523
        ],
        2000 => [
            'diameter' => 1134,
            'height' => 1890,
            'wall' => 2,
            'weight' => 167,
            'power' => 4.8,
            'price' => 318269,
            'full_volume' => 1908876,
            'working_volume' => 1527101
        ],
        3000 => [
            'diameter' => 1298,
            'height' => 2163,
            'wall' => 3,
            'weight' => 218,
            'power' => 6.2,
            'price' => 372832,
            'full_volume' => 2862172,
            'working_volume' => 2289738
        ],
        4000 => [
            'diameter' => 1429,
            'height' => 2381,
            'wall' => 3,
            'weight' => 265,
            'power' => 7.6,
            'price' => 432250,
            'full_volume' => 3818684,
            'working_volume' => 3054947
        ],
        5000 => [
            'diameter' => 1539,
            'height' => 2565,
            'wall' => 3,
            'weight' => 307,
            'power' => 8.8,
            'price' => 514357,
            'full_volume' => 4771495,
            'working_volume' => 3817196
        ],
        6000 => [
            'diameter' => 1635,
            'height' => 2726,
            'wall' => 3,
            'weight' => 347,
            'power' => 9.9,
            'price' => 598500,
            'full_volume' => 5723362,
            'working_volume' => 4578690
        ],
        6300 => [
            'diameter' => 1662,
            'height' => 2770,
            'wall' => 3,
            'weight' => 358,
            'power' => 10.2,
            'price' => 611800,
            'full_volume' => 6009408,
            'working_volume' => 4807526
        ],
        8000 => [
            'diameter' => 1800,
            'height' => 3000,
            'wall' => 3,
            'weight' => 420,
            'power' => 12.0,
            'price' => 731500,
            'full_volume' => 7634070,
            'working_volume' => 6107256
        ],
        10000 => [
            'diameter' => 1939,
            'height' => 3232,
            'wall' => 4,
            'weight' => 487,
            'power' => 13.9,
            'price' => 695324,
            'full_volume' => 9543702,
            'working_volume' => 7634962
        ],
        12500 => [
            'diameter' => 2089,
            'height' => 3481,
            'wall' => 4,
            'weight' => 566,
            'power' => 16.2,
            'price' => 820166,
            'full_volume' => 11930833,
            'working_volume' => 9544666
        ],
        15000 => [
            'diameter' => 2220,
            'height' => 3699,
            'wall' => 4,
            'weight' => 639,
            'power' => 18.2,
            'price' => 930999,
            'full_volume' => 14317928,
            'working_volume' => 11454342
        ],
        16000 => [
            'diameter' => 2268,
            'height' => 3780,
            'wall' => 4,
            'weight' => 667,
            'power' => 19.0,
            'price' => 984199,
            'full_volume' => 15271011,
            'working_volume' => 12216809
        ],
        20000 => [
            'diameter' => 2443,
            'height' => 4072,
            'wall' => 4,
            'weight' => 774,
            'power' => 22.1,
            'price' => 1197000,
            'full_volume' => 19087304,
            'working_volume' => 15269843
        ],
        25000 => [
            'diameter' => 2632,
            'height' => 4386,
            'wall' => 4,
            'weight' => 898,
            'power' => 25.6,
            'price' => 1463000,
            'full_volume' => 23863288,
            'working_volume' => 19090630
        ],
        30000 => [
            'diameter' => 2797,
            'height' => 4661,
            'wall' => 4,
            'weight' => 1014,
            'power' => 29.0,
            'price' => 1861999,
            'full_volume' => 28638741,
            'working_volume' => 22910993
        ],
        31500 => [
            'diameter' => 2842,
            'height' => 4737,
            'wall' => 4,
            'weight' => 1047,
            'power' => 29.9,
            'price' => 1941799,
            'full_volume' => 30049788,
            'working_volume' => 24039830
        ],
        40000 => [
            'diameter' => 3078,
            'height' => 5130,
            'wall' => 4,
            'weight' => 1228,
            'power' => 35.1,
            'price' => 2394000,
            'full_volume' => 38171962,
            'working_volume' => 30537570
        ],
        50000 => [
            'diameter' => 3316,
            'height' => 5526,
            'wall' => 5,
            'weight' => 1425,
            'power' => 40.7,
            'price' => 2992500,
            'full_volume' => 47723227,
            'working_volume' => 38178582
        ]
    ],
    ],
    'specs' => [
        500 => [
            'diameter' => 600,
            'height' => 1300,
            'wall' => 2,
            'weight' => 85,
            'power' => 0.0,
            'price' => 167580,
            'full_volume' => 367566,
            'working_volume' => 294053
        ],
        1000 => [
            'diameter' => 756,
            'height' => 1638,
            'wall' => 2,
            'weight' => 135,
            'power' => 0.0,
            'price' => 206530,
            'full_volume' => 735271,
            'working_volume' => 588217
        ],
        1500 => [
            'diameter' => 865,
            'height' => 1875,
            'wall' => 2,
            'weight' => 177,
            'power' => 0.0,
            'price' => 239400,
            'full_volume' => 1101852,
            'working_volume' => 881482
        ],
        2000 => [
            'diameter' => 952,
            'height' => 2064,
            'wall' => 2,
            'weight' => 214,
            'power' => 0.0,
            'price' => 272802,
            'full_volume' => 1469175,
            'working_volume' => 1175340
        ],
        3000 => [
            'diameter' => 1090,
            'height' => 2362,
            'wall' => 3,
            'weight' => 281,
            'power' => 0.0,
            'price' => 319570,
            'full_volume' => 2204057,
            'working_volume' => 1763246
        ],
        4000 => [
            'diameter' => 1200,
            'height' => 2600,
            'wall' => 3,
            'weight' => 340,
            'power' => 0.0,
            'price' => 370500,
            'full_volume' => 2940531,
            'working_volume' => 2352425
        ],
        5000 => [
            'diameter' => 1293,
            'height' => 2801,
            'wall' => 3,
            'weight' => 395,
            'power' => 0.0,
            'price' => 440877,
            'full_volume' => 3677901,
            'working_volume' => 2942321
        ],
        6000 => [
            'diameter' => 1374,
            'height' => 2976,
            'wall' => 3,
            'weight' => 446,
            'power' => 0.0,
            'price' => 513000,
            'full_volume' => 4412617,
            'working_volume' => 3530094
        ],
        6300 => [
            'diameter' => 1396,
            'height' => 3025,
            'wall' => 3,
            'weight' => 460,
            'power' => 0.0,
            'price' => 524400,
            'full_volume' => 4630054,
            'working_volume' => 3704043
        ],
        8000 => [
            'diameter' => 1512,
            'height' => 3276,
            'wall' => 3,
            'weight' => 540,
            'power' => 0.0,
            'price' => 627000,
            'full_volume' => 5882167,
            'working_volume' => 4705734
        ],
        10000 => [
            'diameter' => 1629,
            'height' => 3529,
            'wall' => 4,
            'weight' => 626,
            'power' => 0.0,
            'price' => 595992,
            'full_volume' => 7355017,
            'working_volume' => 5884014
        ],
        12500 => [
            'diameter' => 1754,
            'height' => 3801,
            'wall' => 4,
            'weight' => 727,
            'power' => 0.0,
            'price' => 702999,
            'full_volume' => 9184318,
            'working_volume' => 7347454
        ],
        15000 => [
            'diameter' => 1864,
            'height' => 4039,
            'wall' => 4,
            'weight' => 821,
            'power' => 0.0,
            'price' => 798000,
            'full_volume' => 11021877,
            'working_volume' => 8817502
        ],
        16000 => [
            'diameter' => 1905,
            'height' => 4127,
            'wall' => 4,
            'weight' => 857,
            'power' => 0.0,
            'price' => 843600,
            'full_volume' => 11762897,
            'working_volume' => 9410318
        ],
        20000 => [
            'diameter' => 2052,
            'height' => 4446,
            'wall' => 4,
            'weight' => 994,
            'power' => 0.0,
            'price' => 1026000,
            'full_volume' => 14703274,
            'working_volume' => 11762619
        ],
        25000 => [
            'diameter' => 2210,
            'height' => 4789,
            'wall' => 4,
            'weight' => 1154,
            'power' => 0.0,
            'price' => 1254000,
            'full_volume' => 18370428,
            'working_volume' => 14696342
        ],
        31500 => [
            'diameter' => 2387,
            'height' => 5173,
            'wall' => 4,
            'weight' => 1346,
            'power' => 0.0,
            'price' => 1664400,
            'full_volume' => 23149265,
            'working_volume' => 18519412
        ]
    ],
    ],
    'specs' => [
        500 => [
            'diameter' => 714,
            'height' => 1191,
            'wall' => 2,
            'weight' => 72,
            'power' => 3.1,
            'price' => 293265,
            'full_volume' => 476868,
            'working_volume' => 381494
        ],
        1000 => [
            'diameter' => 900,
            'height' => 1500,
            'wall' => 2,
            'weight' => 115,
            'power' => 5.0,
            'price' => 361429,
            'full_volume' => 954259,
            'working_volume' => 763407
        ],
        1500 => [
            'diameter' => 1030,
            'height' => 1717,
            'wall' => 2,
            'weight' => 151,
            'power' => 6.6,
            'price' => 418950,
            'full_volume' => 1430654,
            'working_volume' => 1144523
        ],
        2000 => [
            'diameter' => 1134,
            'height' => 1890,
            'wall' => 2,
            'weight' => 183,
            'power' => 7.9,
            'price' => 477403,
            'full_volume' => 1908876,
            'working_volume' => 1527101
        ],
        3000 => [
            'diameter' => 1298,
            'height' => 2163,
            'wall' => 3,
            'weight' => 239,
            'power' => 10.4,
            'price' => 559248,
            'full_volume' => 2862172,
            'working_volume' => 2289738
        ],
        4000 => [
            'diameter' => 1429,
            'height' => 2381,
            'wall' => 3,
            'weight' => 290,
            'power' => 12.6,
            'price' => 648375,
            'full_volume' => 3818684,
            'working_volume' => 3054947
        ],
        5000 => [
            'diameter' => 1539,
            'height' => 2565,
            'wall' => 3,
            'weight' => 336,
            'power' => 14.6,
            'price' => 771536,
            'full_volume' => 4771495,
            'working_volume' => 3817196
        ],
        6000 => [
            'diameter' => 1635,
            'height' => 2726,
            'wall' => 3,
            'weight' => 380,
            'power' => 16.5,
            'price' => 897750,
            'full_volume' => 5723362,
            'working_volume' => 4578690
        ],
        6300 => [
            'diameter' => 1662,
            'height' => 2770,
            'wall' => 3,
            'weight' => 392,
            'power' => 17.1,
            'price' => 917700,
            'full_volume' => 6009408,
            'working_volume' => 4807526
        ],
        8000 => [
            'diameter' => 1800,
            'height' => 3000,
            'wall' => 3,
            'weight' => 460,
            'power' => 20.0,
            'price' => 1097250,
            'full_volume' => 7634070,
            'working_volume' => 6107256
        ],
        10000 => [
            'diameter' => 1939,
            'height' => 3232,
            'wall' => 4,
            'weight' => 534,
            'power' => 23.2,
            'price' => 1042986,
            'full_volume' => 9543702,
            'working_volume' => 7634962
        ],
        12500 => [
            'diameter' => 2089,
            'height' => 3481,
            'wall' => 4,
            'weight' => 619,
            'power' => 26.9,
            'price' => 1230250,
            'full_volume' => 11930833,
            'working_volume' => 9544666
        ],
        15000 => [
            'diameter' => 2220,
            'height' => 3699,
            'wall' => 4,
            'weight' => 699,
            'power' => 30.4,
            'price' => 1396500,
            'full_volume' => 14317928,
            'working_volume' => 11454342
        ],
        16000 => [
            'diameter' => 2268,
            'height' => 3780,
            'wall' => 4,
            'weight' => 730,
            'power' => 31.7,
            'price' => 1476300,
            'full_volume' => 15271011,
            'working_volume' => 12216809
        ],
        20000 => [
            'diameter' => 2443,
            'height' => 4072,
            'wall' => 4,
            'weight' => 847,
            'power' => 36.8,
            'price' => 1795500,
            'full_volume' => 19087304,
            'working_volume' => 15269843
        ],
        25000 => [
            'diameter' => 2632,
            'height' => 4386,
            'wall' => 4,
            'weight' => 983,
            'power' => 42.7,
            'price' => 2194500,
            'full_volume' => 23863288,
            'working_volume' => 19090630
        ],
        30000 => [
            'diameter' => 2797,
            'height' => 4661,
            'wall' => 4,
            'weight' => 1110,
            'power' => 48.3,
            'price' => 2793000,
            'full_volume' => 28638741,
            'working_volume' => 22910993
        ],
        31500 => [
            'diameter' => 2842,
            'height' => 4737,
            'wall' => 4,
            'weight' => 1147,
            'power' => 49.9,
            'price' => 2912700,
            'full_volume' => 30049788,
            'working_volume' => 24039830
        ],
        40000 => [
            'diameter' => 3078,
            'height' => 5130,
            'wall' => 4,
            'weight' => 1345,
            'power' => 58.5,
            'price' => 3591000,
            'full_volume' => 38171962,
            'working_volume' => 30537570
        ],
        50000 => [
            'diameter' => 3316,
            'height' => 5526,
            'wall' => 5,
            'weight' => 1561,
            'power' => 67.9,
            'price' => 4488750,
            'full_volume' => 47723227,
            'working_volume' => 38178582
        ]
    ],
    ],
];


$wineCategory = [
    'title' => 'Винодельческое оборудование из нержавеющей стали — каталог',
    'desc' => 'Каталог винодельческого оборудования из нержавеющей стали AISI 304/316: ферментационные танки, ёмкости выдержки и хранения, криостаты, купажные и сульфитационные ёмкости, универсальные танки.',
    'h1' => 'Винодельческое оборудование',
];

