<?php

declare(strict_types=1);

namespace App\Enums;

enum Theme: int
{
    case Light = 0;
    case Galactic = 1;
    case DarkBlue = 2;
    case DarkGreen = 3;
    case DarkPink = 4;
    case DarkPurple = 5;
    case DarkRed = 6;
    case DarkTeal = 7;
    case DarkYellow = 8;
    case CosmicVoid = 9;
    case Nord = 10;
    case Revel = 11;
    case MaterialLight = 12;
    case MaterialDark = 13;
    case MaterialAmoled = 14;
    case MaterialNavy = 15;
    case VltavaDark = 16;
    case VltavaLight = 17;
    case GruvboxDark = 18;
    case GruvboxLight = 19;
    case Darcula = 20;
    case CatppuccinMocha = 21;
    case CatppuccinLatte = 22;
    case System = 23;

    /** @return array<int> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function fromStoredStyle(int $style): self
    {
        return self::tryFrom($style) ?? self::VltavaDark;
    }

    /** @return array<string, list<self>> */
    public static function grouped(): array
    {
        $themes = [];

        foreach (self::cases() as $theme) {
            $themes[$theme->group()][] = $theme;
        }

        return $themes;
    }

    public function group(): string
    {
        return match ($this) {
            self::System, self::VltavaDark, self::VltavaLight                                                              => trans('application-messages.theme.group-recommended'),
            self::GruvboxDark, self::GruvboxLight, self::Darcula, self::CatppuccinMocha, self::CatppuccinLatte, self::Nord => trans('application-messages.theme.group-classic'),
            default                                                                                                        => trans('application-messages.theme.group-other'),
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Light           => trans('application-messages.theme.label-light'),
            self::Galactic        => 'Galactic',
            self::DarkBlue        => trans('application-messages.theme.label-dark-blue'),
            self::DarkGreen       => trans('application-messages.theme.label-dark-green'),
            self::DarkPink        => trans('application-messages.theme.label-dark-pink'),
            self::DarkPurple      => trans('application-messages.theme.label-dark-purple'),
            self::DarkRed         => trans('application-messages.theme.label-dark-red'),
            self::DarkTeal        => trans('application-messages.theme.label-dark-teal'),
            self::DarkYellow      => trans('application-messages.theme.label-dark-yellow'),
            self::CosmicVoid      => trans('application-messages.theme.label-cosmic-void'),
            self::Nord            => 'Nord',
            self::Revel           => trans('application-messages.theme.label-revel'),
            self::MaterialLight   => trans('application-messages.theme.label-material-light'),
            self::MaterialDark    => trans('application-messages.theme.label-material-dark'),
            self::MaterialAmoled  => trans('application-messages.theme.label-material-amoled'),
            self::MaterialNavy    => trans('application-messages.theme.label-material-navy'),
            self::VltavaDark      => trans('application-messages.theme.label-vltava-dark'),
            self::VltavaLight     => trans('application-messages.theme.label-vltava-light'),
            self::GruvboxDark     => trans('application-messages.theme.label-gruvbox-dark'),
            self::GruvboxLight    => trans('application-messages.theme.label-gruvbox-light'),
            self::Darcula         => 'Darcula',
            self::CatppuccinMocha => 'Catppuccin Mocha',
            self::CatppuccinLatte => 'Catppuccin Latte',
            self::System          => trans('application-messages.theme.label-system'),
        };
    }

    /** @return list<string> */
    public function viteEntries(): array
    {
        return match ($this) {
            self::Light           => ['resources/sass/themes/_light.scss'],
            self::Galactic        => ['resources/sass/themes/_galactic.scss'],
            self::DarkBlue        => ['resources/sass/themes/_galactic.scss', 'resources/sass/themes/_dark-blue.scss'],
            self::DarkGreen       => ['resources/sass/themes/_galactic.scss', 'resources/sass/themes/_dark-green.scss'],
            self::DarkPink        => ['resources/sass/themes/_galactic.scss', 'resources/sass/themes/_dark-pink.scss'],
            self::DarkPurple      => ['resources/sass/themes/_galactic.scss', 'resources/sass/themes/_dark-purple.scss'],
            self::DarkRed         => ['resources/sass/themes/_galactic.scss', 'resources/sass/themes/_dark-red.scss'],
            self::DarkTeal        => ['resources/sass/themes/_galactic.scss', 'resources/sass/themes/_dark-teal.scss'],
            self::DarkYellow      => ['resources/sass/themes/_galactic.scss', 'resources/sass/themes/_dark-yellow.scss'],
            self::CosmicVoid      => ['resources/sass/themes/_galactic.scss', 'resources/sass/themes/_cosmic-void.scss'],
            self::Nord            => ['resources/sass/themes/_nord.scss'],
            self::Revel           => ['resources/sass/themes/_revel.scss'],
            self::MaterialLight   => ['resources/sass/themes/_material-design-v3-light.scss'],
            self::MaterialDark    => ['resources/sass/themes/_material-design-v3-dark.scss'],
            self::MaterialAmoled  => ['resources/sass/themes/_material-design-v3-amoled.scss'],
            self::MaterialNavy    => ['resources/sass/themes/_material-design-v3-navy.scss'],
            self::VltavaDark      => ['resources/sass/themes/_vltava-dark.scss'],
            self::VltavaLight     => ['resources/sass/themes/_vltava-light.scss'],
            self::GruvboxDark     => ['resources/sass/themes/_gruvbox-dark.scss'],
            self::GruvboxLight    => ['resources/sass/themes/_gruvbox-light.scss'],
            self::Darcula         => ['resources/sass/themes/_darcula.scss'],
            self::CatppuccinMocha => ['resources/sass/themes/_catppuccin-mocha.scss'],
            self::CatppuccinLatte => ['resources/sass/themes/_catppuccin-latte.scss'],
            self::System          => [],
        };
    }

    public function usesSystemPreference(): bool
    {
        return $this === self::System;
    }
}
