<?php
session_start();
include("../connection.php");

if (!isset($_SESSION["user"]) || empty($_SESSION["user"]) || $_SESSION['usertype'] != 'p') {
    echo json_encode(["status" => "error", "message" => "Unauthorized access."]);
    exit();
}

$useremail = $_SESSION["user"];

// Kunin ang client_id ng user
$userrow = $database->query("SELECT * FROM client WHERE c_email='$useremail'");

if ($userrow && $userrow->num_rows > 0) {
    $userfetch = $userrow->fetch_assoc();
    $userid = $userfetch["client_id"];
} else {
    echo json_encode(["status" => "error", "message" => "User not found."]);
    exit();
}

// Mapping ng rating numbers sa word equivalent
$rating_words = [
    1 => "Very Dissatisfied",
    2 => "Dissatisfied",
    3 => "Neutral",
    4 => "Satisfied",
    5 => "Very Satisfied"
];

$positive_words = [
    "amazing", "awesome", "beautiful", "breathtaking", "brilliant", "captivating", "charismatic", "classy", 
    "clean", "colorful", "creative", "crisp", "dazzling", "delightful", "detailed", "dreamy", "elegant", 
    "emotional", "engaging", "enjoyable", "epic", "ethereal", "excellent", "exceptional", "exquisite", 
    "eye-catching", "fascinating", "flawless", "fresh", "genius", "genuine", "glamorous", "glowing", 
    "gorgeous", "graceful", "heartwarming", "high-quality", "iconic", "impactful", "impressive", "incredible", 
    "inspired", "inspiring", "joyful", "legendary", "lively", "lovely", "luxurious", "magnificent", 
    "majestic", "masterpiece", "mesmerizing", "mind-blowing", "modern", "natural", "neat", "perfect", 
    "phenomenal", "picture-perfect", "polished", "positive", "powerful", "pretty", "pristine", "professional", 
    "radiant", "refined", "remarkable", "rich", "romantic", "satisfying", "serene", "sharp", "sleek", 
    "smooth", "sophisticated", "spectacular", "stunning", "stylish", "sublime", "superb", "supportive", 
    "tasteful", "timeless", "top-notch", "unique", "unreal", "unforgettable", "vibrant", "vivid", 
    "well-balanced", "well-composed", "well-crafted", "well-lit", "well-made", "wonderful", "wow", "yes", 
    "youthful", "zen", "zestful"
];


$negative_words = [
    "blurry", "boring", "bad", "poor", "grainy", "dull", "overexposed", "underexposed", "noisy", "washed out",
    "flat", "dark", "harsh", "unbalanced", "awkward", "distorted", "fake", "cheap", "low-quality", "oversaturated",
    "undersaturated", "colorless", "unsharp", "lifeless", "forced", "rushed", "flawed", "shaky", "distracting",
    "inconsistent", "overedited", "muddy", "cluttered", "off-center", "bad framing", "wrong focus", "random",
    "unfocused", "motion blur", "pixelated", "underwhelming", "ghosting", "weak", "low-resolution", "unoriginal",
    "redundant", "misaligned", "awkward shadows", "poor lighting", "too bright", "too dark", "bad angle",
    "too soft", "too sharp", "overprocessed", "overdone", "incomplete", "monotonous", "cheap-looking", 
    "amateur", "inappropriate", "awkward crop", "jagged", "distracting elements", "unprofessional", "unpleasant", 
    "pointless", "unflattering", "wrong colors", "color mismatch", "bad exposure", "overexposed highlights",
    "underexposed shadows", "ghosting effect", "washed out colors", "too much vignette", "confusing", 
    "flat lighting", "bad composition", "unreadable", "unclear", "no depth", "not engaging", "stiff", 
    "uninteresting", "chaotic", "disorganized", "off tone", "overkill", "unwanted glare", "unfocused subject",
    "no story", "no emotion", "visual noise", "messy", "plain", "outdated", "out of focus", "meaningless"
];



$positive_words_tl = [
    "maganda", "maayos", "malinaw", "masaya", "matalino", "mabait", "mahusay", "makulay", "matino", "maayos ang lighting",
    "malambot ang kulay", "natural", "malikhain", "propesyonal", "perpekto", "kamangha-mangha", "masarap sa mata", 
    "pantay ang kulay", "bongga", "swabe", "elegante", "maaliwalas", "relaxing", "detalyado", "malinis", "naka-engganyo", 
    "makabuluhan", "artistiko", "kakaiba", "organisado", "maamo", "magiliw", "kaakit-akit", "tunay", "mabango", "sariwa", 
    "kaaya-aya", "makisig", "matalas", "makinis", "makintab", "maaliwalas", "banayad", "pino", "kakaiba ang kuha", 
    "tumpak ang framing", "magandang pagkakalagay", "mahusay ang composition", "creative ang anggulo", "hindi bastos", 
    "mapagkakatiwalaan", "may dating", "mabenta", "palaban", "may class", "hindi boring", "nakakatuwa", "nagtatampok ng husay", 
    "bihasa", "astig", "maabilidad", "ganda ng timing", "nakakabilib", "napapanahon", "pampamilya", "pambida", 
    "hindi makakalimutan", "nagniningning", "mapaglarawan", "napaka-natural", "komportable", "light sa mata", 
    "walang sablay", "hindi nakakasawa", "swak sa lahat", "ok sa lahat", "pangmalakasan", "panalo", "malamig sa mata", 
    "magandang vibes", "good vibes", "siguradong babalik", "hindi bitin", "tamang-tama", "magaan ang loob", 
    "may malasakit", "binigyan ng effort", "hindi tinipid", "mapagkalinga", "pantay-pantay", "may respeto", "maayos ang kuha", 
    "malinis ang background", "maganda ang pagkaka-compose", "aesthetically pleasing", "may emotion", "solid", "ganado", 
    "inspired", "motivating"
];

$negative_words_tl = [
    "malabo", "madilim", "maingay ang background", "sobrang edit", "hindi malinaw", "walang contrast",
    "mababa ang kalidad", "pilit", "pangit ang anggulo", "masakit sa mata", "sobrang saturated",
    "napaka-dull", "hindi natural", "kulang sa detalye", "masyadong madilim", "masyadong maliwanag",
    "hindi balanse", "masyadong matapang ang kulay", "matamlay", "walang emosyon", "walang dating",
    "paulit-ulit", "kulang sa kwento", "sobrang plain", "mukhang minadali", "hindi pantay ang ilaw",
    "bastos", "hindi flattering", "panget ang framing", "random", "hindi pulido", "mukhang mura",
    "masyadong malambot", "masyadong matalas", "naka-disturb", "masyadong chaotic", "hindi kaaya-aya",
    "nakakalito", "nakakapagod sa mata", "bad crop", "hindi malinaw ang subject", "parang walang saysay",
    "napakalabo", "nakakasawa", "naka-bore", "masyadong filtered", "kulang sa buhay", "sobrang contrast",
    "sobrang sharp", "blurred", "hindi center", "hindi klaro", "masyadong flat", "puro noise",
    "panget ang kulay", "hindi maayos ang subject", "walang impact", "pangit ang framing",
    "sobrang vignette", "mukhang generic", "too safe", "luma ang itsura", "hindi consistent",
    "malabo ang message", "bad lighting", "sobrang lamig", "sobrang init", "hindi aligned",
    "distracting", "mataas ang shadow", "masyadong bright", "masyadong dark", "hindi proporsyonado",
    "walang direction", "masyadong curve", "overexposed", "walang identity", "masyadong busy",
    "too minimal", "hindi tapos", "too messy", "hindi naayos", "may sabit", "kulang sa style",
    "panget ang layout", "kulang sa kwento", "walang focus", "kulang sa galing", "di maganda ang concept",
    "matigas ang dating", "hindi creative", "pa-cute pero fail", "pabigla-bigla", "inconsistent",
    "nagdudulot ng confusion", "mukhang luma", "hindi kapanipaniwala", "di bagay", "nakakairita",
    "masyadong technical", "walang artistry", "kulang sa visual appeal"
];

$positive_words_bn = [
    "magayon", "marhay", "klaro", "makusog an kolor", "maogma", "mabisto", "matingkad", "malinig", "presko", 
    "natural", "makusog an dating", "liwanag", "mainit na welcome", "magayunon an anggulo", "maayos an framing", 
    "baga obra", "maugmang komposisyon", "kabighani", "maalwan sa mata", "malinaw an subject", "maayos an contrast", 
    "basta payt", "madetalyado", "garong propesyonal", "maraot pero artistic", "malimpyo", "maaliwalas", 
    "masaya an vibe", "tama an saturation", "natural an lighting", "mahirigos", "hustong tono", "perfecto", 
    "payt an kulay", "hustong exposure", "hustong framing", "swak sa mood", "di pilit", "kaiba", "nagrerepresenta", 
    "may emosyon", "may kasaysayan", "tama an moment", "malikhain", "bagay an lens", "suabe", "klarado an subject", 
    "sakto an cropping", "naka-engganyo", "malinaw an texture", "tama an timing", "tama an angle", "parang sine", 
    "hustong composition", "bongga", "maraot sa huna-huna", "inspirado", "matao an impact", "magayunon an background", 
    "may kwento", "hustong shot", "marhay an pagka-frame", "magayon an lighting", "napaka-engaging", 
    "garong pang-exhibit", "makisig an dating", "dakul an detalye", "natural an posing", "maayo an execution", 
    "klaro an emotion", "malining pagkahuli", "maray sa mata", "nakakatuwa", "payt sa layout", "perfect sa color grading", 
    "swak sa subject", "garong masterpiece", "swak sa detalye", "minimal pero powerful", "strong presence", 
    "positive energy", "payt sa creativity", "payt an elements", "makasurprise", "pangsocial media", 
    "klarado an highlights", "clean an shadows", "defined an lines", "tama an focus", "bagay sa feed", 
    "clean aesthetics", "vibrant an dating", "professional an dating", "dakul an character", "alive an subject", 
    "tama an layout", "refreshing", "nakaka-goodvibes"
];

$negative_words_bn = [
    "dai klaro", "dai marhay", "madulom", "maluya an kolor", "dai makusog", "dakul an noise",
    "dakul an shadow", "sobra an filter", "hustong overexposed", "hustong underexposed", 
    "garong di propesyonal", "dai kaaya-aya", "sobra an saturation", "dai balance", "dai emotion",
    "dai focus", "layo an subject", "dai engaging", "maluya an impact", "baga lampag", 
    "garong minadali", "di payt an angle", "garong sobra an contrast", "naglalangaw", "dakul an clutter",
    "sobrang makusog", "sobrang maluya", "maraot an background", "garong bastos an tono",
    "maluya an framing", "di bagay an tono", "dakul an disturbo", "di aesthetic", "dai klarado an image", 
    "blurred", "garong mabangis an effect", "garong putok an light", "dai malinig", "dai na-sentro",
    "dai aligned", "garong wala sa ayos", "maraot an layout", "bad framing", "matao an vignette",
    "garong pirang klase an tono", "wala sa lugar", "garong daing direksyon", "sobrang dull", 
    "dai consistent", "garong barat an lens", "barat an camera", "layo sa propesyonal", 
    "hindi creative", "garong dai pulos", "masyado an blur", "masyado an sharp", 
    "parang laog", "dai engaging", "mukhang cheap", "dai organized", "dakul an kabuang", 
    "labo an message", "garong dai kwenta", "dakul an istorbo", "dai focus", "bad composition",
    "maluya an storya", "di makua an point", "di klarado an subject", "masakit sa mata", 
    "garong paukaw", "garong kulang an creativity", "sobra an detalye", "di bagay an effect", 
    "mukhang robotic", "dakul an sabaw", "dakul an babaw", "dai visual appeal", "hustong bad lighting",
    "sobra an glare", "masyado an reflection", "masyadong hilaw", "garong dai aesthetic", 
    "sobra an edit", "dakul an shadow", "garong naiipit an subject", "layo an komposisyon", 
    "garong kapos an framing", "panget an crop", "di sakto an exposure", "walang balance", 
    "wala sa tono", "sobrang detalyado", "garong misleading", "masyadong malamig", "masyadong mainit",
    "hustong mabangis an shadow", "matao an pagkaputok", "dakul an off-tone", "dakul an sablay"
];



// Function to determine sentiment
function analyzeSentiment($comment) {
    global $positive_words, $negative_words;
    global $positive_words_tl, $negative_words_tl;
    global $positive_words_bn, $negative_words_bn;

    $comment = strtolower($comment);
    $positive_count = 0;
    $negative_count = 0;

    // Check positive words
    foreach (array_merge($positive_words, $positive_words_tl, $positive_words_bn) as $word) {
        if (strpos($comment, $word) !== false) {
            $positive_count++;
        }
    }

    // Check negative words
    foreach (array_merge($negative_words, $negative_words_tl, $negative_words_bn) as $word) {
        if (strpos($comment, $word) !== false) {
            $negative_count++;
        }
    }

    // Determine sentiment
    if ($positive_count > $negative_count) {
        return "good";
    } elseif ($negative_count > $positive_count) {
        return "bad";
    } else {
        return "neutral";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rating_number = isset($_POST["rating"]) ? intval($_POST["rating"]) : 0;
    $comment = isset($_POST["feedback"]) ? trim($_POST["feedback"]) : "";

    // Convert rating number to text
    $rating_word = isset($rating_words[$rating_number]) ? $rating_words[$rating_number] : "Unknown";

    // Perform sentiment analysis
    $sentiment = analyzeSentiment($comment);

    if ($rating_number > 0 && !empty($comment)) {
        $stmt = $database->prepare("INSERT INTO feedback (client_id, rating, comment, sentiment, date_created) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("isss", $userid, $rating_word, $comment, $sentiment);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Thank you for your feedback!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Something went wrong. Please try again."]);
        }
        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Please provide a rating and comment."]);
    }
}
?>