<?php

namespace App\ChartBuilder;

use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class LineChartByMonthBuilder
{
    /** @var ChartBuilderInterface $chartBuilder A chart.js builder */
    protected $chartBuilder = null;

    /** @var string[] $data Data to show */
    protected $data = [0, 10, 5, 2, 20, 30, 45];

    /** @var string[] $labels Labels for the X axis */
    protected $labels = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
    
    /** @var string $dataLabel The label for the data showed */
    protected $dataLabel = 'Nouveaux utilisateurs';

    public function __construct(ChartBuilderInterface $chartBuilder)
    {
        $this->chartBuilder = $chartBuilder;
    }

    /**
     * Build a LineChart object
     * 
     * @param $option Can use data, labels, dataLabel
     */
    public function build(array $options = [])
    {
        if(key_exists('data', $options)) {
            $this->data = $options['data'];
        }
        if(key_exists('labels', $options)) {
            $this->labels = $options['labels'];
        }
        if(key_exists('dataLabel', $options)) {
            $this->dataLabel = $options['dataLabel'];
        }

        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);
        $chart->setData([
            'labels' => $this->labels,
            'datasets' => [
                [
                    'label' => $this->dataLabel,
                    'backgroundColor' => 'rgb(132, 99, 255)',
                    'borderColor' => 'rgb(170, 99, 255)',
                    'data' => $this->data,
                ],
            ],
        ]);

        $chart->setOptions([
            'scales' => [
                'yAxes' => [
                    ['ticks' => ['min' => 0]],
                ],
            ],
        ]);

        return $chart;
    }
}
