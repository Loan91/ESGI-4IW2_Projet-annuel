<?php


namespace App\Data;

/**
 * Class SearchData
 * Représente les données liés à la recherche.
 * @package App\Data
 */
class SearchData
{
    public $type = '';
    public $city = '';
    public $categories = [];
    public $minPrice;
    public $maxPrice;
}