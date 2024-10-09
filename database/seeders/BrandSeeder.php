<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Brand; // Ensure this line is present

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

     public function run()
     {
         // JSON data
         $jsonData = '
         [
    {
        "brand_name": "HILLSON",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "KINGTONY",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "MAVRIC",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "PENGUIN (UAE)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "DORMER (UK)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "MEGA (SPAIN)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "AAA",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "ABRO",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "WD-40",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "COCACO",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "DIAMOND",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "TEKIRO",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "WALKLONG",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "PERFECT TOOLS",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "LAXMI",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "ARALDITE",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "DECA",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "PANYI",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "IRWIN UK",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "INDIA",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "RECORD UK",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "LICOTA",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "BLAZER",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "PP",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "GLOBEX",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "HPIC",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "ATC",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "GLOBAL",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "AWARD",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "MEGA",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "KINGFORCE",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "OXFORD",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "YALE",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "HAFFLE (GERMANY)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "FERRARI",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "KRINO",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "IRWIN",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "DORMER",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "WORKZONE",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "CHINA",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "ALPINOX",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "WORKMAN",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "IRWIN/RECORD (UK)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "MEGA TOOLS",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "IRWIN (UK)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "STARRET (USA)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "ECLIPSE (UK)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "JUNIOR",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "STANLEY (UK)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "ECLIPSE",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "LATEX",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "KISTENMACHER",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "JUBILEE (UK)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "CLARKE EU",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "KESON",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "TAHA",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "WORKMEN",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "RECORD (UK)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "IRWIN -UK",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "NAMSON",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "HERO",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "G LINE",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "JK INDIA",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "SAMURAI (JAPAN)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "CROCODILE",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "ESSAR",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "ORLANDO",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "DELTAPLUS",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "TUFF",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "COMFORT",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "HITACHI",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "SYNERGY",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "JAPAN",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "JACKO",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "PIONEER",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "DOLPHIN",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "JEAN",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "KINGFORCE (TAIWAN)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": " ",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "3M",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "SUPERGRIP",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "SUMAKE",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "TAIWAN",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "SUNTECH",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "FG (ITALY)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "SIRL",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "DIVWE",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "LUKIA",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "TELWIN",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "WOLF",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "CAPITAL",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "MASTER",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "LION",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "MOVIT (HOLLAND)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "P.M",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "PORTOTECNICA",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "PREFECT TOOLS",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "TOYO",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "SPEED",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "VITAL",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "OREGON",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "OLEOMAC",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "SHINDAIWA",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "NORA",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "MAX (INDIA)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "SHUMACHER",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "STAHLBERG",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "BOSCH",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "GERMANY",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "CIDAT (ITALY)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "AIRMAX",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "VICTORY ",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "FIAC (ITALY)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "TAIWAN/CHINA",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "SENSEMAT",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "ROBINHOOD",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "SIRL (PORTUGAL)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "SHINDAWA",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "PIONEER (TAIWAN)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "SPLASH",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "BOSWELL",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "BMI",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "STANLEY",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "SHIELD",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "ROBTOL",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "INSIZE",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "DUPRO (UK)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "EDISON (UK)",
        "created_by": 1,
        "updated_by": 1
    },

    {
        "brand_name": "SIGNOGRAPH (GERMANY)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "DRONCO",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "MECLUBE (ITALY)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "PLUISI",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "PIUSI",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "PRESSOL",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "MECLUBE",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "PRESSOL (GERMANY)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "PROXXON (GERMANY)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "STEINEL (GERMANY)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "BLUMOL",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "STARRET",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "MAX",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "DEWALT",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "PROLATE (INDIA)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "VIITE (GERMANY)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "GTT",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "WALKL",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "UNI-T",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "FLUKE",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "KYORITSU",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "CLARKE",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "POWERBUILT",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "SAFELIFT ",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "MONOLIT (ITALY)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "12 TON CHINA",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "BMI/WEISS",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "EMPIRE",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "ASTURO",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "EGA MASTER (SPAIN)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "INDER (INDIA)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "METRO [INDIA]",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "METRO",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "VAROX GBMH",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "NORBAR (UK)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "ELEPHANT",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "VERRK",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "MOVEIT",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "TRISCO",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "CLARKE (UK)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "CITEX (HOLLAND)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "BOSSWELL",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "MUBEX",
        "created_by": 1,
        "updated_by": 1
    },

    {
        "brand_name": "KUWES",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "ALM",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "WELDMAN",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "TECHWELD",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "SAMSON",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "BOSSWELL/HURTHAUF",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "KOKON",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "SIHIO",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "TELWIN (ITALY)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "OTTO (GERMANY)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "HAWK",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "CHINA / JAPAN",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "HONDA (JAPAN)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "SUNMATCH - CHINA",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "FAIZ",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "CRYSTALINE",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "MEGADORA",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "KINKI",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "ALPINE / KRINO",
        "created_by": 1,
        "updated_by": 1
    },

    {
        "brand_name": "REFLEX",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "MAKITA",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "BLACK & DECKER",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "ABRA",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "ABRASIFLEX (USA)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "DRONCO (GDR)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "JK (INDIA)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "BODMAN (GERMANY)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "ULTRA FLEX",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "HOFFMAN",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "HEWARA",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "SATA ",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "CASTLE GARDEN",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "OLEOMAC (ITALY)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "EMAK",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "MITUTOYO",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "BEW - GERMANY",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "HIKOKI",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "PEGLER (UK)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "VIEGA (GERMANY)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "AQUASAN",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "MAGIC MC-ALPINE",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "COBRA",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "ALPINE",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "ARMAMIX",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "QUADRENT",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "RUDEX",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "BONUS ",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "FLORA",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "DIABLO",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "ACQUA",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "VILLA",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "ARAMIX",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "BOSS",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "KOSTA",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "KRISTENMACHER",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "SBM",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "RAYA",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "GS ESCO",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "SHAMAL",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "EXOMIX",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "HAN-SANITAR",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "VIEGA ",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "VADO / EUROBATH",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "SOUTH AFRICA",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "VADO (UK)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "ARISTON",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "CENTON",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "BB (ITALY)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "PEDROLLO (ITALY)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "CARTOON (INDIA)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "MEISA (TURKEY)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "UAE",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "PENGUINE",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "MALAYSIA",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "ALBRO",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "ALBRO (UAE)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "REACH",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "CENTAURE",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "TUBESCA",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "GROZ (INDIA)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "SUMO [JAPAN]",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "BOSSINI",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "YORK",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "ASTRAL (UK)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "EURO BATH",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "CLABER",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "50mm",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "MEM - UK",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "#60",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "#80",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "#150",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "#120",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "#200",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "#240",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "#320",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "#360",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "COLT",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "KINGFORCE TAIWAN",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "ORLANDO / SOLEX",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "ITALY",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "ORLANDO (ITALY)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "VENITEX",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "ECLIPSE UK",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "CITEX",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "VONDER",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "LOCOTA",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "TECOMECH",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "VACKSON",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "STEINIL",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "SUMEKE",
        "created_by": 1,
        "updated_by": 1
    },

    {
        "brand_name": "KYOWA (JAPAN)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "MONTOLIT",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "HARRES (USA)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "VICTOR UK",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "BOSSINI (ITALY)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "SILVINIA (SPAIN)",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "VIRTUO",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "brand_name": "GTT (TAIWAN)",
        "created_by": 1,
        "updated_by": 1
    }
]
         ';

         // Decode the JSON data
         $items = json_decode($jsonData, true);

         // Insert each item into the database with created_by and updated_by set to null
         foreach ($items as $item) {
            Brand::create([
                 'brand_name' => $item['brand_name'],
                 'created_by' => 1,
                 'updated_by' => 1,
             ]);
         }
     }
}
