<?php

namespace App\Mason;

class BrickCollection
{
    /**
     * Returns all registered Mason bricks.
     *
     * @return array<int, class-string<\Awcodes\Mason\Brick>>
     */
    public static function all(): array
    {
        return [
            Hero::class,
            Features::class,
            Stats::class,
            Cta::class,
            Cards::class,
            LatestEntries::class,
            Text::class,
            Heading::class,
            Image::class,
            Gallery::class,
            Quote::class,
            Testimonial::class,
            Columns::class,
            Video::class,
            Button::class,
            Divider::class,
            Html::class,
        ];
    }
}
