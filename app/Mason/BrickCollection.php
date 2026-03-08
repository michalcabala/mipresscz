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
            Text::class,
            Heading::class,
            Image::class,
            Gallery::class,
            Quote::class,
            Video::class,
            Columns::class,
            Hero::class,
            Button::class,
            Divider::class,
            Html::class,
            Testimonial::class,
        ];
    }
}
