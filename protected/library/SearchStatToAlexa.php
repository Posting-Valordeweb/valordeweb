<?php


class SearchStatToAlexa
{
    private $google_index;
    private $bing_index;
    private $yahoo_index;

    private $max;

    const ALEXA_MAX = 35000000;

    /**
     * SearchStatToAlexa constructor.
     * @param int $google_index
     * @param int $bing_index
     * @param int $yahoo_index
     */
    public function __construct($google_index, $bing_index, $yahoo_index)
    {
        $this->google_index = $google_index;
        $this->bing_index = $bing_index;
        $this->yahoo_index = $yahoo_index;
        $this->max = max($this->google_index, $this->yahoo_index, $this->bing_index);
    }

    public function convert()
    {
        $coefficients = $this->getCoefficients();
        if (empty($coefficients)) {
            return self::ALEXA_MAX;
        }
        $first = $coefficients['initial'];
        $alexa = 1;

        if ($this->max >= $first) {
            return $alexa;
        }

        $prev_alexa = $alexa;
        $prev_cnt = $first;

        $adjust = 0;

        foreach ($coefficients['coefficients'] as $record) {
            $cnt = $record['cnt'];
            $div = $record['divided_into'];
            if ($this->max < $cnt) {
                $prev_alexa += $div;
                $prev_cnt = $cnt;
                continue;
            }

            $next_alexa = $prev_alexa + $div;


            $page_range = $prev_cnt - $cnt;
            $page_pos = $this->max - $cnt;
            $percentage_page_pos = $page_pos * 100 / $page_range;
            $alexa_range = $next_alexa - $prev_alexa;
            $alexa_per_percent = $alexa_range / 100;
            if ($alexa_per_percent > 1) {
                $adjust = mt_rand(0, floor($alexa_per_percent));
            }
            $alexa_part = floor($alexa_per_percent * $percentage_page_pos);
            $alexa = $next_alexa - $alexa_part - $adjust;


            /*echo "<pre>\n";
            echo "Prev page count: ". number_format($prev_cnt) . "\n";
            echo "Next page count: ". number_format($cnt). "\n";
            echo "Page range: ". number_format($page_range) . "\n";
            echo "Page pos: ". number_format($page_pos) . "\n";
            echo "Percentage page pos: ". number_format($percentage_page_pos) . "\n\n";

            echo "Prev alexa count: ". number_format($prev_alexa) . "\n";
            echo "Next alexa count: ". number_format($next_alexa). "\n";
            echo "Alexa range: ". number_format($alexa_range). "\n";
            echo "Alexa per percent: ". number_format($alexa_per_percent). "\n";
            echo "Alexa adjust: ". number_format($adjust). "\n";
            echo "Alexa part: ". number_format($alexa_part). "\n";
            echo "Alexa: ". number_format($alexa). "\n";*/

            return (int) $alexa;
        }

        return self::ALEXA_MAX;
    }

    private function getCoefficients()
    {
        switch ($this->max) {
            case $this->google_index:
                return $this->getGoogleCoefficients();
            case $this->bing_index:
                return $this->getBingCoefficients();
            case $this->yahoo_index:
                return $this->getYahooCoefficients();
            default:
                return array();
        }
    }

    private function getGoogleCoefficients()
    {
        return array(
            'initial'=>6000000000,
            'coefficients'=>array(
                array(
                    'cnt'=>3000000000,
                    'divided_into'=>1,
                ),
                array(
                    'cnt'=>2000000000,
                    'divided_into'=>1,
                ),
                array(
                    'cnt'=>1000000000,
                    'divided_into'=>3,
                ),
                array(
                    'cnt'=>300000000,
                    'divided_into'=>10,
                ),
                array(
                    'cnt'=>50000000,
                    'divided_into'=>17,
                ),
                array(
                    'cnt'=>20000000,
                    'divided_into'=>50,
                ),
                array(
                    'cnt'=>10000000,
                    'divided_into'=>400,
                ),
                array(
                    'cnt'=>5000000,
                    'divided_into'=>500,
                ),
                array(
                    'cnt'=>2000000,
                    'divided_into'=>2000,
                ),
                array(
                    'cnt'=>900000,
                    'divided_into'=>5000,
                ),
                array(
                    'cnt'=>800000,
                    'divided_into'=>10000,
                ),
                array(
                    'cnt'=>600000,
                    'divided_into'=>12000,
                ),
                array(
                    'cnt'=>500000,
                    'divided_into'=>20000,
                ),
                array(
                    'cnt'=>100000,
                    'divided_into'=>50000,
                ),
                array(
                    'cnt'=>80000,
                    'divided_into'=>100000,
                ),
                array(
                    'cnt'=>60000,
                    'divided_into'=>100000,
                ),
                array(
                    'cnt'=>30000,
                    'divided_into'=>100000,
                ),
                array(
                    'cnt'=>10000,
                    'divided_into'=>200000,
                ),
                array(
                    'cnt'=>8000,
                    'divided_into'=>200000,
                ),
                array(
                    'cnt'=>5000,
                    'divided_into'=>200000,
                ),
                array(
                    'cnt'=>4000,
                    'divided_into'=>500000,
                ),
                array(
                    'cnt'=>3000,
                    'divided_into'=>500000,
                ),
                array(
                    'cnt'=>2000,
                    'divided_into'=>500000,
                ),
                array(
                    'cnt'=>1000,
                    'divided_into'=>500000,
                ),
                array(
                    'cnt'=>900,
                    'divided_into'=>1000000,
                ),
                array(
                    'cnt'=>800,
                    'divided_into'=>1000000,
                ),
                array(
                    'cnt'=>700,
                    'divided_into'=>1000000,
                ),
                array(
                    'cnt'=>600,
                    'divided_into'=>1000000,
                ),
                array(
                    'cnt'=>500,
                    'divided_into'=>1000000,
                ),
                array(
                    'cnt'=>400,
                    'divided_into'=>1000000,
                ),
                array(
                    'cnt'=>300,
                    'divided_into'=>1000000,
                ),
                array(
                    'cnt'=>200,
                    'divided_into'=>1000000,
                ),
                array(
                    'cnt'=>100,
                    'divided_into'=>4000000,
                ),
                array(
                    'cnt'=>50,
                    'divided_into'=>5000000,
                ),
                array(
                    'cnt'=>1,
                    'divided_into'=>15000000,
                ),
            ),
        );
    }

    private function getYahooCoefficients()
    {
        return array(
            'initial'=>3000000000,
            'coefficients'=>array(
                array(
                    'cnt'=>1500000000,
                    'divided_into'=>1,
                ),
                array(
                    'cnt'=>1000000000,
                    'divided_into'=>1,
                ),
                array(
                    'cnt'=>700000000,
                    'divided_into'=>20,
                ),
                array(
                    'cnt'=>100000000,
                    'divided_into'=>10,
                ),
                array(
                    'cnt'=>50000000,
                    'divided_into'=>17,
                ),
                array(
                    'cnt'=>20000000,
                    'divided_into'=>50,
                ),
                array(
                    'cnt'=>10000000,
                    'divided_into'=>400,
                ),
                array(
                    'cnt'=>5000000,
                    'divided_into'=>500,
                ),
                array(
                    'cnt'=>2000000,
                    'divided_into'=>2000,
                ),
                array(
                    'cnt'=>900000,
                    'divided_into'=>5000,
                ),
                array(
                    'cnt'=>800000,
                    'divided_into'=>12000,
                ),
                array(
                    'cnt'=>600000,
                    'divided_into'=>10000,
                ),
                array(
                    'cnt'=>500000,
                    'divided_into'=>20000,
                ),
                array(
                    'cnt'=>100000,
                    'divided_into'=>50000,
                ),
                array(
                    'cnt'=>80000,
                    'divided_into'=>100000,
                ),
                array(
                    'cnt'=>60000,
                    'divided_into'=>100000,
                ),
                array(
                    'cnt'=>30000,
                    'divided_into'=>100000,
                ),
                array(
                    'cnt'=>10000,
                    'divided_into'=>200000,
                ),
                array(
                    'cnt'=>8000,
                    'divided_into'=>200000,
                ),
                array(
                    'cnt'=>5000,
                    'divided_into'=>200000,
                ),
                array(
                    'cnt'=>4000,
                    'divided_into'=>500000,
                ),
                array(
                    'cnt'=>3000,
                    'divided_into'=>500000,
                ),
                array(
                    'cnt'=>2000,
                    'divided_into'=>500000,
                ),
                array(
                    'cnt'=>1000,
                    'divided_into'=>500000,
                ),
                array(
                    'cnt'=>900,
                    'divided_into'=>1000000,
                ),
                array(
                    'cnt'=>800,
                    'divided_into'=>1000000,
                ),
                array(
                    'cnt'=>700,
                    'divided_into'=>1000000,
                ),
                array(
                    'cnt'=>600,
                    'divided_into'=>1000000,
                ),
                array(
                    'cnt'=>500,
                    'divided_into'=>1000000,
                ),
                array(
                    'cnt'=>400,
                    'divided_into'=>1000000,
                ),
                array(
                    'cnt'=>300,
                    'divided_into'=>1000000,
                ),
                array(
                    'cnt'=>200,
                    'divided_into'=>1000000,
                ),
                array(
                    'cnt'=>100,
                    'divided_into'=>4000000,
                ),
                array(
                    'cnt'=>50,
                    'divided_into'=>5000000,
                ),
                array(
                    'cnt'=>1,
                    'divided_into'=>15000000,
                ),
            ),
        );
    }

    private function getBingCoefficients()
    {
        return array(
            'initial'=>800000000,
            'coefficients'=>array(
                array(
                    'cnt'=>700000000,
                    'divided_into'=>1,
                ),
                array(
                    'cnt'=>600000000,
                    'divided_into'=>1,
                ),
                array(
                    'cnt'=>100000000,
                    'divided_into'=>3,
                ),
                array(
                    'cnt'=>80000000,
                    'divided_into'=>10,
                ),
                array(
                    'cnt'=>60000000,
                    'divided_into'=>17,
                ),
                array(
                    'cnt'=>20000000,
                    'divided_into'=>50,
                ),
                array(
                    'cnt'=>10000000,
                    'divided_into'=>400,
                ),
                array(
                    'cnt'=>8000000,
                    'divided_into'=>500,
                ),
                array(
                    'cnt'=>5000000,
                    'divided_into'=>2000,
                ),
                array(
                    'cnt'=>1000000,
                    'divided_into'=>5000,
                ),
                array(
                    'cnt'=>800000,
                    'divided_into'=>10000,
                ),
                array(
                    'cnt'=>600000,
                    'divided_into'=>12000,
                ),
                array(
                    'cnt'=>500000,
                    'divided_into'=>20000,
                ),
                array(
                    'cnt'=>100000,
                    'divided_into'=>50000,
                ),
                array(
                    'cnt'=>80000,
                    'divided_into'=>100000,
                ),
                array(
                    'cnt'=>60000,
                    'divided_into'=>100000,
                ),
                array(
                    'cnt'=>30000,
                    'divided_into'=>100000,
                ),
                array(
                    'cnt'=>10000,
                    'divided_into'=>200000,
                ),
                array(
                    'cnt'=>8000,
                    'divided_into'=>200000,
                ),
                array(
                    'cnt'=>5000,
                    'divided_into'=>200000,
                ),
                array(
                    'cnt'=>4000,
                    'divided_into'=>500000,
                ),
                array(
                    'cnt'=>3000,
                    'divided_into'=>500000,
                ),
                array(
                    'cnt'=>2000,
                    'divided_into'=>500000,
                ),
                array(
                    'cnt'=>1000,
                    'divided_into'=>500000,
                ),
                array(
                    'cnt'=>900,
                    'divided_into'=>1000000,
                ),
                array(
                    'cnt'=>800,
                    'divided_into'=>1000000,
                ),
                array(
                    'cnt'=>700,
                    'divided_into'=>1000000,
                ),
                array(
                    'cnt'=>600,
                    'divided_into'=>1000000,
                ),
                array(
                    'cnt'=>500,
                    'divided_into'=>1000000,
                ),
                array(
                    'cnt'=>400,
                    'divided_into'=>1000000,
                ),
                array(
                    'cnt'=>300,
                    'divided_into'=>1000000,
                ),
                array(
                    'cnt'=>200,
                    'divided_into'=>1000000,
                ),
                array(
                    'cnt'=>100,
                    'divided_into'=>4000000,
                ),
                array(
                    'cnt'=>50,
                    'divided_into'=>5000000,
                ),
                array(
                    'cnt'=>1,
                    'divided_into'=>15000000,
                ),
            ),
        );
    }
}