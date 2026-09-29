<?php

namespace App\Enums;

enum SectionEnum: string
{
    
    const BG = 'bg_image';

    case EXAMPLE = 'awaerness';
    case EXAMPLES = 'awaernesses';

    case INTRO = 'intro';
    case BANNER = 'banner';

    case ABOUT = 'about';

    //common sections
    case FOOTER = 'footer';
    case HEADER = 'header';

    // how it works sections
    case HEROBANNER = 'hero';

    case SIMPLESELLING = 'simple-selling';
    case SIMPLESELLINGS = 'simple-sellings';

    case SAFELYSHOP = 'safely-shop';
    case SAFELYSHOPS = 'safely-shops';

}
