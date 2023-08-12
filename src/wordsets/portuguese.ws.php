<?php
/**
 * Monero PHP Library - Portuguese Wordset for Mnemonic Generation
 *
 * This file contains the implementation of the Portuguese wordset
 * for generating mnemonics in the Monero PHP library.
 *
 * @package MoneroIntegrations\MoneroPhp\mnemonic
 */
namespace MoneroIntegrations\MoneroPhp\mnemonic;

/**
 * Portuguese Wordset Class
 *
 * This class defines the Portuguese wordset for generating mnemonics
 * in the Monero PHP library.
 *
 * @package MoneroIntegrations\MoneroPhp\mnemonic
 */
class portuguese implements wordset {
    /**
     * Returns name of wordset in the wordset's native language.
     * 
     * This is a human-readable string, and should be capitalized
     * if the language supports it.
     * 
     * @return string
     */
    static public function name() : string {
        return "Português";
    }

    /**
     * Returns name of wordset in English.
     * 
     * This is a human-readable string, and should be capitalized.
     * 
     * @return string
     */
    static public function english_name() : string {
        return "Portuguese";
    }


     /**
     * Returns integer indicating length of unique prefix,
     * such that each prefix of this length is unique across
     * the entire set of words.
     *
     * @return int
     */
    static public function prefix_length() : int {
        return 4;  // first 4 letters of each word in wordset is unique.
    }
    
    /**
     * Returns the array of all words in the wordset.
     *
     * @return array
     */ 
    static public function words() : array {
        return [
            "abaular",
            "abdominal",
            "abeto",
            "abissinio",
            "abjeto",
            "ablucao",
            "abnegar",
            "abotoar",
            "abrutalhar",
            "absurdo",
            "abutre",
            "acautelar",
            "accessorios",
            "acetona",
            "achocolatado",
            "acirrar",
            "acne",
            "acovardar",
            "acrostico",
            "actinomicete",
            "acustico",
            "adaptavel",
            "adeus",
            "adivinho",
            "adjunto",
            "admoestar",
            "adnominal",
            "adotivo",
            "adquirir",
            "adriatico",
            "adsorcao",
            "adutora",
            "advogar",
            "aerossol",
            "afazeres",
            "afetuoso",
            "afixo",
            "afluir",
            "afortunar",
            "afrouxar",
            "aftosa",
            "afunilar",
            "agentes",
            "agito",
            "aglutinar",
            "aiatola",
            "aimore",
            "aino",
            "aipo",
            "airoso",
            "ajeitar",
            "ajoelhar",
            "ajudante",
            "ajuste",
            "alazao",
            "albumina",
            "alcunha",
            "alegria",
            "alexandre",
            "alforriar",
            "alguns",
            "alhures",
            "alivio",
            "almoxarife",
            "alotropico",
            "alpiste",
            "alquimista",
            "alsaciano",
            "altura",
            "aluviao",
            "alvura",
            "amazonico",
            "ambulatorio",
            "ametodico",
            "amizades",
            "amniotico",
            "amovivel",
            "amurada",
            "anatomico",
            "ancorar",
            "anexo",
            "anfora",
            "aniversario",
            "anjo",
            "anotar",
            "ansioso",
            "anturio",
            "anuviar",
            "anverso",
            "anzol",
            "aonde",
            "apaziguar",
            "apito",
            "aplicavel",
            "apoteotico",
            "aprimorar",
            "aprumo",
            "apto",
            "apuros",
            "aquoso",
            "arauto",
            "arbusto",
            "arduo",
            "aresta",
            "arfar",
            "arguto",
            "aritmetico",
            "arlequim",
            "armisticio",
            "aromatizar",
            "arpoar",
            "arquivo",
            "arrumar",
            "arsenio",
            "arturiano",
            "aruaque",
            "arvores",
            "asbesto",
            "ascorbico",
            "aspirina",
            "asqueroso",
            "assustar",
            "astuto",
            "atazanar",
            "ativo",
            "atletismo",
            "atmosferico",
            "atormentar",
            "atroz",
            "aturdir",
            "audivel",
            "auferir",
            "augusto",
            "aula",
            "aumento",
            "aurora",
            "autuar",
            "avatar",
            "avexar",
            "avizinhar",
            "avolumar",
            "avulso",
            "axiomatico",
            "azerbaijano",
            "azimute",
            "azoto",
            "azulejo",
            "bacteriologista",
            "badulaque",
            "baforada",
            "baixote",
            "bajular",
            "balzaquiana",
            "bambuzal",
            "banzo",
            "baoba",
            "baqueta",
            "barulho",
            "bastonete",
            "batuta",
            "bauxita",
            "bavaro",
            "bazuca",
            "bcrepuscular",
            "beato",
            "beduino",
            "begonia",
            "behaviorista",
            "beisebol",
            "belzebu",
            "bemol",
            "benzido",
            "beocio",
            "bequer",
            "berro",
            "besuntar",
            "betume",
            "bexiga",
            "bezerro",
            "biatlon",
            "biboca",
            "bicuspide",
            "bidirecional",
            "bienio",
            "bifurcar",
            "bigorna",
            "bijuteria",
            "bimotor",
            "binormal",
            "bioxido",
            "bipolarizacao",
            "biquini",
            "birutice",
            "bisturi",
            "bituca",
            "biunivoco",
            "bivalve",
            "bizarro",
            "blasfemo",
            "blenorreia",
            "blindar",
            "bloqueio",
            "blusao",
            "boazuda",
            "bofete",
            "bojudo",
            "bolso",
            "bombordo",
            "bonzo",
            "botina",
            "boquiaberto",
            "bostoniano",
            "botulismo",
            "bourbon",
            "bovino",
            "boximane",
            "bravura",
            "brevidade",
            "britar",
            "broxar",
            "bruno",
            "bruxuleio",
            "bubonico",
            "bucolico",
            "buda",
            "budista",
            "bueiro",
            "buffer",
            "bugre",
            "bujao",
            "bumerangue",
            "burundines",
            "busto",
            "butique",
            "buzios",
            "caatinga",
            "cabuqui",
            "cacunda",
            "cafuzo",
            "cajueiro",
            "camurca",
            "canudo",
            "caquizeiro",
            "carvoeiro",
            "casulo",
            "catuaba",
            "cauterizar",
            "cebolinha",
            "cedula",
            "ceifeiro",
            "celulose",
            "cerzir",
            "cesto",
            "cetro",
            "ceus",
            "cevar",
            "chavena",
            "cheroqui",
            "chita",
            "chovido",
            "chuvoso",
            "ciatico",
            "cibernetico",
            "cicuta",
            "cidreira",
            "cientistas",
            "cifrar",
            "cigarro",
            "cilio",
            "cimo",
            "cinzento",
            "cioso",
            "cipriota",
            "cirurgico",
            "cisto",
            "citrico",
            "ciumento",
            "civismo",
            "clavicula",
            "clero",
            "clitoris",
            "cluster",
            "coaxial",
            "cobrir",
            "cocota",
            "codorniz",
            "coexistir",
            "cogumelo",
            "coito",
            "colusao",
            "compaixao",
            "comutativo",
            "contentamento",
            "convulsivo",
            "coordenativa",
            "coquetel",
            "correto",
            "corvo",
            "costureiro",
            "cotovia",
            "covil",
            "cozinheiro",
            "cretino",
            "cristo",
            "crivo",
            "crotalo",
            "cruzes",
            "cubo",
            "cucuia",
            "cueiro",
            "cuidar",
            "cujo",
            "cultural",
            "cunilingua",
            "cupula",
            "curvo",
            "custoso",
            "cutucar",
            "czarismo",
            "dablio",
            "dacota",
            "dados",
            "daguerreotipo",
            "daiquiri",
            "daltonismo",
            "damista",
            "dantesco",
            "daquilo",
            "darwinista",
            "dasein",
            "dativo",
            "deao",
            "debutantes",
            "decurso",
            "deduzir",
            "defunto",
            "degustar",
            "dejeto",
            "deltoide",
            "demover",
            "denunciar",
            "deputado",
            "deque",
            "dervixe",
            "desvirtuar",
            "deturpar",
            "deuteronomio",
            "devoto",
            "dextrose",
            "dezoito",
            "diatribe",
            "dicotomico",
            "didatico",
            "dietista",
            "difuso",
            "digressao",
            "diluvio",
            "diminuto",
            "dinheiro",
            "dinossauro",
            "dioxido",
            "diplomatico",
            "dique",
            "dirimivel",
            "disturbio",
            "diurno",
            "divulgar",
            "dizivel",
            "doar",
            "dobro",
            "docura",
            "dodoi",
            "doer",
            "dogue",
            "doloso",
            "domo",
            "donzela",
            "doping",
            "dorsal",
            "dossie",
            "dote",
            "doutro",
            "doze",
            "dravidico",
            "dreno",
            "driver",
            "dropes",
            "druso",
            "dubnio",
            "ducto",
            "dueto",
            "dulija",
            "dundum",
            "duodeno",
            "duquesa",
            "durou",
            "duvidoso",
            "duzia",
            "ebano",
            "ebrio",
            "eburneo",
            "echarpe",
            "eclusa",
            "ecossistema",
            "ectoplasma",
            "ecumenismo",
            "eczema",
            "eden",
            "editorial",
            "edredom",
            "edulcorar",
            "efetuar",
            "efigie",
            "efluvio",
            "egiptologo",
            "egresso",
            "egua",
            "einsteiniano",
            "eira",
            "eivar",
            "eixos",
            "ejetar",
            "elastomero",
            "eldorado",
            "elixir",
            "elmo",
            "eloquente",
            "elucidativo",
            "emaranhar",
            "embutir",
            "emerito",
            "emfa",
            "emitir",
            "emotivo",
            "empuxo",
            "emulsao",
            "enamorar",
            "encurvar",
            "enduro",
            "enevoar",
            "enfurnar",
            "enguico",
            "enho",
            "enigmista",
            "enlutar",
            "enormidade",
            "enpreendimento",
            "enquanto",
            "enriquecer",
            "enrugar",
            "entusiastico",
            "enunciar",
            "envolvimento",
            "enxuto",
            "enzimatico",
            "eolico",
            "epiteto",
            "epoxi",
            "epura",
            "equivoco",
            "erario",
            "erbio",
            "ereto",
            "erguido",
            "erisipela",
            "ermo",
            "erotizar",
            "erros",
            "erupcao",
            "ervilha",
            "esburacar",
            "escutar",
            "esfuziante",
            "esguio",
            "esloveno",
            "esmurrar",
            "esoterismo",
            "esperanca",
            "espirito",
            "espurio",
            "essencialmente",
            "esturricar",
            "esvoacar",
            "etario",
            "eterno",
            "etiquetar",
            "etnologo",
            "etos",
            "etrusco",
            "euclidiano",
            "euforico",
            "eugenico",
            "eunuco",
            "europio",
            "eustaquio",
            "eutanasia",
            "evasivo",
            "eventualidade",
            "evitavel",
            "evoluir",
            "exaustor",
            "excursionista",
            "exercito",
            "exfoliado",
            "exito",
            "exotico",
            "expurgo",
            "exsudar",
            "extrusora",
            "exumar",
            "fabuloso",
            "facultativo",
            "fado",
            "fagulha",
            "faixas",
            "fajuto",
            "faltoso",
            "famoso",
            "fanzine",
            "fapesp",
            "faquir",
            "fartura",
            "fastio",
            "faturista",
            "fausto",
            "favorito",
            "faxineira",
            "fazer",
            "fealdade",
            "febril",
            "fecundo",
            "fedorento",
            "feerico",
            "feixe",
            "felicidade",
            "felpudo",
            "feltro",
            "femur",
            "fenotipo",
            "fervura",
            "festivo",
            "feto",
            "feudo",
            "fevereiro",
            "fezinha",
            "fiasco",
            "fibra",
            "ficticio",
            "fiduciario",
            "fiesp",
            "fifa",
            "figurino",
            "fijiano",
            "filtro",
            "finura",
            "fiorde",
            "fiquei",
            "firula",
            "fissurar",
            "fitoteca",
            "fivela",
            "fixo",
            "flavio",
            "flexor",
            "flibusteiro",
            "flotilha",
            "fluxograma",
            "fobos",
            "foco",
            "fofura",
            "foguista",
            "foie",
            "foliculo",
            "fominha",
            "fonte",
            "forum",
            "fosso",
            "fotossintese",
            "foxtrote",
            "fraudulento",
            "frevo",
            "frivolo",
            "frouxo",
            "frutose",
            "fuba",
            "fucsia",
            "fugitivo",
            "fuinha",
            "fujao",
            "fulustreco",
            "fumo",
            "funileiro",
            "furunculo",
            "fustigar",
            "futurologo",
            "fuxico",
            "fuzue",
            "gabriel",
            "gado",
            "gaelico",
            "gafieira",
            "gaguejo",
            "gaivota",
            "gajo",
            "galvanoplastico",
            "gamo",
            "ganso",
            "garrucha",
            "gastronomo",
            "gatuno",
            "gaussiano",
            "gaviao",
            "gaxeta",
            "gazeteiro",
            "gear",
            "geiser",
            "geminiano",
            "generoso",
            "genuino",
            "geossinclinal",
            "gerundio",
            "gestual",
            "getulista",
            "gibi",
            "gigolo",
            "gilete",
            "ginseng",
            "giroscopio",
            "glaucio",
            "glacial",
            "gleba",
            "glifo",
            "glote",
            "glutonia",
            "gnostico",
            "goela",
            "gogo",
            "goitaca",
            "golpista",
            "gomo",
            "gonzo",
            "gorro",
            "gostou",
            "goticula",
            "gourmet",
            "governo",
            "gozo",
            "graxo",
            "grevista",
            "grito",
            "grotesco",
            "gruta",
            "guaxinim",
            "gude",
            "gueto",
            "guizo",
            "guloso",
            "gume",
            "guru",
            "gustativo",
            "grelhado",
            "gutural",
            "habitue",
            "haitiano",
            "halterofilista",
            "hamburguer",
            "hanseniase",
            "happening",
            "harpista",
            "hastear",
            "haveres",
            "hebreu",
            "hectometro",
            "hedonista",
            "hegira",
            "helena",
            "helminto",
            "hemorroidas",
            "henrique",
            "heptassilabo",
            "hertziano",
            "hesitar",
            "heterossexual",
            "heuristico",
            "hexagono",
            "hiato",
            "hibrido",
            "hidrostatico",
            "hieroglifo",
            "hifenizar",
            "higienizar",
            "hilario",
            "himen",
            "hino",
            "hippie",
            "hirsuto",
            "historiografia",
            "hitlerista",
            "hodometro",
            "hoje",
            "holograma",
            "homus",
            "honroso",
            "hoquei",
            "horto",
            "hostilizar",
            "hotentote",
            "huguenote",
            "humilde",
            "huno",
            "hurra",
            "hutu",
            "iaia",
            "ialorixa",
            "iambico",
            "iansa",
            "iaque",
            "iara",
            "iatista",
            "iberico",
            "ibis",
            "icar",
            "iceberg",
            "icosagono",
            "idade",
            "ideologo",
            "idiotice",
            "idoso",
            "iemenita",
            "iene",
            "igarape",
            "iglu",
            "ignorar",
            "igreja",
            "iguaria",
            "iidiche",
            "ilativo",
            "iletrado",
            "ilharga",
            "ilimitado",
            "ilogismo",
            "ilustrissimo",
            "imaturo",
            "imbuzeiro",
            "imerso",
            "imitavel",
            "imovel",
            "imputar",
            "imutavel",
            "inaveriguavel",
            "incutir",
            "induzir",
            "inextricavel",
            "infusao",
            "ingua",
            "inhame",
            "iniquo",
            "injusto",
            "inning",
            "inoxidavel",
            "inquisitorial",
            "insustentavel",
            "intumescimento",
            "inutilizavel",
            "invulneravel",
            "inzoneiro",
            "iodo",
            "iogurte",
            "ioio",
            "ionosfera",
            "ioruba",
            "iota",
            "ipsilon",
            "irascivel",
            "iris",
            "irlandes",
            "irmaos",
            "iroques",
            "irrupcao",
            "isca",
            "isento",
            "islandes",
            "isotopo",
            "isqueiro",
            "israelita",
            "isso",
            "isto",
            "iterbio",
            "itinerario",
            "itrio",
            "iuane",
            "iugoslavo",
            "jabuticabeira",
            "jacutinga",
            "jade",
            "jagunco",
            "jainista",
            "jaleco",
            "jambo",
            "jantarada",
            "japones",
            "jaqueta",
            "jarro",
            "jasmim",
            "jato",
            "jaula",
            "javel",
            "jazz",
            "jegue",
            "jeitoso",
            "jejum",
            "jenipapo",
            "jeova",
            "jequitiba",
            "jersei",
            "jesus",
            "jetom",
            "jiboia",
            "jihad",
            "jilo",
            "jingle",
            "jipe",
            "jocoso",
            "joelho",
            "joguete",
            "joio",
            "jojoba",
            "jorro",
            "jota",
            "joule",
            "joviano",
            "jubiloso",
            "judoca",
            "jugular",
            "juizo",
            "jujuba",
            "juliano",
            "jumento",
            "junto",
            "jururu",
            "justo",
            "juta",
            "juventude",
            "labutar",
            "laguna",
            "laico",
            "lajota",
            "lanterninha",
            "lapso",
            "laquear",
            "lastro",
            "lauto",
            "lavrar",
            "laxativo",
            "lazer",
            "leasing",
            "lebre",
            "lecionar",
            "ledo",
            "leguminoso",
            "leitura",
            "lele",
            "lemure",
            "lento",
            "leonardo",
            "leopardo",
            "lepton",
            "leque",
            "leste",
            "letreiro",
            "leucocito",
            "levitico",
            "lexicologo",
            "lhama",
            "lhufas",
            "liame",
            "licoroso",
            "lidocaina",
            "liliputiano",
            "limusine",
            "linotipo",
            "lipoproteina",
            "liquidos",
            "lirismo",
            "lisura",
            "liturgico",
            "livros",
            "lixo",
            "lobulo",
            "locutor",
            "lodo",
            "logro",
            "lojista",
            "lombriga",
            "lontra",
            "loop",
            "loquaz",
            "lorota",
            "losango",
            "lotus",
            "louvor",
            "luar",
            "lubrificavel",
            "lucros",
            "lugubre",
            "luis",
            "luminoso",
            "luneta",
            "lustroso",
            "luto",
            "luvas",
            "luxuriante",
            "luzeiro",
            "maduro",
            "maestro",
            "mafioso",
            "magro",
            "maiuscula",
            "majoritario",
            "malvisto",
            "mamute",
            "manutencao",
            "mapoteca",
            "maquinista",
            "marzipa",
            "masturbar",
            "matuto",
            "mausoleu",
            "mavioso",
            "maxixe",
            "mazurca",
            "meandro",
            "mecha",
            "medusa",
            "mefistofelico",
            "megera",
            "meirinho",
            "melro",
            "memorizar",
            "menu",
            "mequetrefe",
            "mertiolate",
            "mestria",
            "metroviario",
            "mexilhao",
            "mezanino",
            "miau",
            "microssegundo",
            "midia",
            "migratorio",
            "mimosa",
            "minuto",
            "miosotis",
            "mirtilo",
            "misturar",
            "mitzvah",
            "miudos",
            "mixuruca",
            "mnemonico",
            "moagem",
            "mobilizar",
            "modulo",
            "moer",
            "mofo",
            "mogno",
            "moita",
            "molusco",
            "monumento",
            "moqueca",
            "morubixaba",
            "mostruario",
            "motriz",
            "mouse",
            "movivel",
            "mozarela",
            "muarra",
            "muculmano",
            "mudo",
            "mugir",
            "muitos",
            "mumunha",
            "munir",
            "muon",
            "muquira",
            "murros",
            "musselina",
            "nacoes",
            "nado",
            "naftalina",
            "nago",
            "naipe",
            "naja",
            "nalgum",
            "namoro",
            "nanquim",
            "napolitano",
            "naquilo",
            "nascimento",
            "nautilo",
            "navios",
            "nazista",
            "nebuloso",
            "nectarina",
            "nefrologo",
            "negus",
            "nelore",
            "nenufar",
            "nepotismo",
            "nervura",
            "neste",
            "netuno",
            "neutron",
            "nevoeiro",
            "newtoniano",
            "nexo",
            "nhenhenhem",
            "nhoque",
            "nigeriano",
            "niilista",
            "ninho",
            "niobio",
            "niponico",
            "niquelar",
            "nirvana",
            "nisto",
            "nitroglicerina",
            "nivoso",
            "nobreza",
            "nocivo",
            "noel",
            "nogueira",
            "noivo",
            "nojo",
            "nominativo",
            "nonuplo",
            "noruegues",
            "nostalgico",
            "noturno",
            "nouveau",
            "nuanca",
            "nublar",
            "nucleotideo",
            "nudista",
            "nulo",
            "numismatico",
            "nunquinha",
            "nupcias",
            "nutritivo",
            "nuvens",
            "oasis",
            "obcecar",
            "obeso",
            "obituario",
            "objetos",
            "oblongo",
            "obnoxio",
            "obrigatorio",
            "obstruir",
            "obtuso",
            "obus",
            "obvio",
            "ocaso",
            "occipital",
            "oceanografo",
            "ocioso",
            "oclusivo",
            "ocorrer",
            "ocre",
            "octogono",
            "odalisca",
            "odisseia",
            "odorifico",
            "oersted",
            "oeste",
            "ofertar",
            "ofidio",
            "oftalmologo",
            "ogiva",
            "ogum",
            "oigale",
            "oitavo",
            "oitocentos",
            "ojeriza",
            "olaria",
            "oleoso",
            "olfato",
            "olhos",
            "oliveira",
            "olmo",
            "olor",
            "olvidavel",
            "ombudsman",
            "omeleteira",
            "omitir",
            "omoplata",
            "onanismo",
            "ondular",
            "oneroso",
            "onomatopeico",
            "ontologico",
            "onus",
            "onze",
            "opalescente",
            "opcional",
            "operistico",
            "opio",
            "oposto",
            "oprobrio",
            "optometrista",
            "opusculo",
            "oratorio",
            "orbital",
            "orcar",
            "orfao",
            "orixa",
            "orla",
            "ornitologo",
            "orquidea",
            "ortorrombico",
            "orvalho",
            "osculo",
            "osmotico",
            "ossudo",
            "ostrogodo",
            "otario",
            "otite",
            "ouro",
            "ousar",
            "outubro",
            "ouvir",
            "ovario",
            "overnight",
            "oviparo",
            "ovni",
            "ovoviviparo",
            "ovulo",
            "oxala",
            "oxente",
            "oxiuro",
            "oxossi",
            "ozonizar",
            "paciente",
            "pactuar",
            "padronizar",
            "paete",
            "pagodeiro",
            "paixao",
            "pajem",
            "paludismo",
            "pampas",
            "panturrilha",
            "papudo",
            "paquistanes",
            "pastoso",
            "patua",
            "paulo",
            "pauzinhos",
            "pavoroso",
            "paxa",
            "pazes",
            "peao",
            "pecuniario",
            "pedunculo",
            "pegaso",
            "peixinho",
            "pejorativo",
            "pelvis",
            "penuria",
            "pequno",
            "petunia",
            "pezada",
            "piauiense",
            "pictorico",
            "pierro",
            "pigmeu",
            "pijama",
            "pilulas",
            "pimpolho",
            "pintura",
            "piorar",
            "pipocar",
            "piqueteiro",
            "pirulito",
            "pistoleiro",
            "pituitaria",
            "pivotar",
            "pixote",
            "pizzaria",
            "plistoceno",
            "plotar",
            "pluviometrico",
            "pneumonico",
            "poco",
            "podridao",
            "poetisa",
            "pogrom",
            "pois",
            "polvorosa",
            "pomposo",
            "ponderado",
            "pontudo",
            "populoso",
            "poquer",
            "porvir",
            "posudo",
            "potro",
            "pouso",
            "povoar",
            "prazo",
            "prezar",
            "privilegios",
            "proximo",
            "prussiano",
            "pseudopode",
            "psoriase",
            "pterossauros",
            "ptialina",
            "ptolemaico",
            "pudor",
            "pueril",
            "pufe",
            "pugilista",
            "puir",
            "pujante",
            "pulverizar",
            "pumba",
            "punk",
            "purulento",
            "pustula",
            "putsch",
            "puxe",
            "quatrocentos",
            "quetzal",
            "quixotesco",
            "quotizavel",
            "rabujice",
            "racista",
            "radonio",
            "rafia",
            "ragu",
            "rajado",
            "ralo",
            "rampeiro",
            "ranzinza",
            "raptor",
            "raquitismo",
            "raro",
            "rasurar",
            "ratoeira",
            "ravioli",
            "razoavel",
            "reavivar",
            "rebuscar",
            "recusavel",
            "reduzivel",
            "reexposicao",
            "refutavel",
            "regurgitar",
            "reivindicavel",
            "rejuvenescimento",
            "relva",
            "remuneravel",
            "renunciar",
            "reorientar",
            "repuxo",
            "requisito",
            "resumo",
            "returno",
            "reutilizar",
            "revolvido",
            "rezonear",
            "riacho",
            "ribossomo",
            "ricota",
            "ridiculo",
            "rifle",
            "rigoroso",
            "rijo",
            "rimel",
            "rins",
            "rios",
            "riqueza",
            "respeito",
            "rissole",
            "ritualistico",
            "rivalizar",
            "rixa",
            "robusto",
            "rococo",
            "rodoviario",
            "roer",
            "rogo",
            "rojao",
            "rolo",
            "rompimento",
            "ronronar",
            "roqueiro",
            "rorqual",
            "rosto",
            "rotundo",
            "rouxinol",
            "roxo",
            "royal",
            "ruas",
            "rucula",
            "rudimentos",
            "ruela",
            "rufo",
            "rugoso",
            "ruivo",
            "rule",
            "rumoroso",
            "runico",
            "ruptura",
            "rural",
            "rustico",
            "rutilar",
            "saariano",
            "sabujo",
            "sacudir",
            "sadomasoquista",
            "safra",
            "sagui",
            "sais",
            "samurai",
            "santuario",
            "sapo",
            "saquear",
            "sartriano",
            "saturno",
            "saude",
            "sauva",
            "saveiro",
            "saxofonista",
            "sazonal",
            "scherzo",
            "script",
            "seara",
            "seborreia",
            "secura",
            "seduzir",
            "sefardim",
            "seguro",
            "seja",
            "selvas",
            "sempre",
            "senzala",
            "sepultura",
            "sequoia",
            "sestercio",
            "setuplo",
            "seus",
            "seviciar",
            "sezonismo",
            "shalom",
            "siames",
            "sibilante",
            "sicrano",
            "sidra",
            "sifilitico",
            "signos",
            "silvo",
            "simultaneo",
            "sinusite",
            "sionista",
            "sirio",
            "sisudo",
            "situar",
            "sivan",
            "slide",
            "slogan",
            "soar",
            "sobrio",
            "socratico",
            "sodomizar",
            "soerguer",
            "software",
            "sogro",
            "soja",
            "solver",
            "somente",
            "sonso",
            "sopro",
            "soquete",
            "sorveteiro",
            "sossego",
            "soturno",
            "sousafone",
            "sovinice",
            "sozinho",
            "suavizar",
            "subverter",
            "sucursal",
            "sudoriparo",
            "sufragio",
            "sugestoes",
            "suite",
            "sujo",
            "sultao",
            "sumula",
            "suntuoso",
            "suor",
            "supurar",
            "suruba",
            "susto",
            "suturar",
            "suvenir",
            "tabuleta",
            "taco",
            "tadjique",
            "tafeta",
            "tagarelice",
            "taitiano",
            "talvez",
            "tampouco",
            "tanzaniano",
            "taoista",
            "tapume",
            "taquion",
            "tarugo",
            "tascar",
            "tatuar",
            "tautologico",
            "tavola",
            "taxionomista",
            "tchecoslovaco",
            "teatrologo",
            "tectonismo",
            "tedioso",
            "teflon",
            "tegumento",
            "teixo",
            "telurio",
            "temporas",
            "tenue",
            "teosofico",
            "tepido",
            "tequila",
            "terrorista",
            "testosterona",
            "tetrico",
            "teutonico",
            "teve",
            "texugo",
            "tiara",
            "tibia",
            "tiete",
            "tifoide",
            "tigresa",
            "tijolo",
            "tilintar",
            "timpano",
            "tintureiro",
            "tiquete",
            "tiroteio",
            "tisico",
            "titulos",
            "tive",
            "toar",
            "toboga",
            "tofu",
            "togoles",
            "toicinho",
            "tolueno",
            "tomografo",
            "tontura",
            "toponimo",
            "toquio",
            "torvelinho",
            "tostar",
            "toto",
            "touro",
            "toxina",
            "trazer",
            "trezentos",
            "trivialidade",
            "trovoar",
            "truta",
            "tuaregue",
            "tubular",
            "tucano",
            "tudo",
            "tufo",
            "tuiste",
            "tulipa",
            "tumultuoso",
            "tunisino",
            "tupiniquim",
            "turvo",
            "tutu",
            "ucraniano",
            "udenista",
            "ufanista",
            "ufologo",
            "ugaritico",
            "uiste",
            "uivo",
            "ulceroso",
            "ulema",
            "ultravioleta",
            "umbilical",
            "umero",
            "umido",
            "umlaut",
            "unanimidade",
            "unesco",
            "ungulado",
            "unheiro",
            "univoco",
            "untuoso",
            "urano",
            "urbano",
            "urdir",
            "uretra",
            "urgente",
            "urinol",
            "urna",
            "urologo",
            "urro",
            "ursulina",
            "urtiga",
            "urupe",
            "usavel",
            "usbeque",
            "usei",
            "usineiro",
            "usurpar",
            "utero",
            "utilizar",
            "utopico",
            "uvular",
            "uxoricidio",
            "vacuo",
            "vadio",
            "vaguear",
            "vaivem",
            "valvula",
            "vampiro",
            "vantajoso",
            "vaporoso",
            "vaquinha",
            "varziano",
            "vasto",
            "vaticinio",
            "vaudeville",
            "vazio",
            "veado",
            "vedico",
            "veemente",
            "vegetativo",
            "veio",
            "veja",
            "veludo",
            "venusiano",
            "verdade",
            "verve",
            "vestuario",
            "vetusto",
            "vexatorio",
            "vezes",
            "viavel",
            "vibratorio",
            "victor",
            "vicunha",
            "vidros",
            "vietnamita",
            "vigoroso",
            "vilipendiar",
            "vime",
            "vintem",
            "violoncelo",
            "viquingue",
            "virus",
            "visualizar",
            "vituperio",
            "viuvo",
            "vivo",
            "vizir",
            "voar",
            "vociferar",
            "vodu",
            "vogar",
            "voile",
            "volver",
            "vomito",
            "vontade",
            "vortice",
            "vosso",
            "voto",
            "vovozinha",
            "voyeuse",
            "vozes",
            "vulva",
            "vupt",
            "western",
            "xadrez",
            "xale",
            "xampu",
            "xango",
            "xarope",
            "xaual",
            "xavante",
            "xaxim",
            "xenonio",
            "xepa",
            "xerox",
            "xicara",
            "xifopago",
            "xiita",
            "xilogravura",
            "xinxim",
            "xistoso",
            "xixi",
            "xodo",
            "xogum",
            "xucro",
            "zabumba",
            "zagueiro",
            "zambiano",
            "zanzar",
            "zarpar",
            "zebu",
            "zefiro",
            "zeloso",
            "zenite",
            "zumbi"
       ];
    }
}