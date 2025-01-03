<?php

namespace App\DTO;

class SeriesCreateFromInput // é a classe que faz aparecer o form de criar a serie
{
    public function __construct(
        public string $seriesName = '',
        public int $seasonsQuantity = 0,
        public int $episodesPerSeason = 0,
    ) {
    }
}
