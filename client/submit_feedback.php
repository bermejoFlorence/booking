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
    "stunning", "breathtaking", "beautiful", "artistic", "vibrant", "creative", "professional", 
    "high-quality", "sharp", "impressive", "amazing", "well-composed", "aesthetic", "eye-catching", 
    "perfect lighting", "crystal clear", "expressive", "masterpiece", "flawless", "captivating", 
    "inspiring", "lively", "elegant", "natural", "picture-perfect", "detailed", "unique", "spectacular", 
    "mesmerizing", "outstanding", "gorgeous", "striking", "majestic", "realistic", "extraordinary", 
    "vivid", "timeless", "sophisticated", "radiant", "charismatic", "graceful", "exquisite", "sublime", 
    "polished", "refined", "heartwarming", "dramatic", "bold", "dreamy", "whimsical", "serene", 
    "harmonious", "poetic", "glowing", "natural light", "dynamic", "depth", "crisp", "engaging", 
    "emotional", "iconic", "powerful", "unforgettable", "magical", "storytelling", "silky", "cinematic", 
    "glamorous", "luxurious", "classy", "energetic", "soulful", "rich", "euphoric", "legendary", 
    "ethereal", "dazzling", "glossy", "sleek", "romantic", "moody", "epic", "peaceful", "sharp focus", 
    "well-lit", "high-resolution", "contrast", "panoramic", "pristine", "immaculate", "superb", 
    "mind-blowing", "phenomenal", "expressive eyes", "perfect angle", "artistic vision", "masterful", 
    "genius composition", "astounding", "unreal", "flattering", "natural expression"
];

$negative_words = [
    "blurry", "dull", "grainy", "pixelated", "overexposed", "underexposed", "washed out",
    "poor lighting", "noisy", "unsharp", "faded", "low-quality", "out of focus", "harsh shadows",
    "distorted", "flat", "uninspired", "boring", "unbalanced", "bad composition", "awkward angle",
    "too dark", "too bright", "unrealistic", "lack of contrast", "lifeless", "unflattering", "overedited",
    "unnatural", "forced", "shaky", "over-processed", "muddy", "cheap-looking", "lack of detail",
    "unprofessional", "too much filter", "washed-out colors", "distracting background", "inconsistent lighting",
    "poor framing", "lack of creativity", "fake-looking", "too much saturation", "bland", "distracting elements",
    "harsh lighting", "uneven focus", "low resolution", "unfocused subject", "awkward cropping", "no depth",
    "flat colors", "jagged edges", "loss of detail", "too much noise", "motion blur", "ghosting effect",
    "lacks emotion", "overexposed highlights", "bad shadows", "overuse of effects", "weak contrast",
    "wrong white balance", "too much vignette", "poor subject placement", "off-center subject",
    "lacks storytelling", "unnatural expressions", "weak composition", "not engaging", "random clutter",
    "too many distractions", "overexaggerated editing", "misaligned horizon", "overexposed background",
    "distracting reflections", "artificial colors", "harsh contrasts", "wrong focus point", "washed-out highlights",
    "inconsistent tones", "underwhelming", "no focal point", "bad proportions", "poor perspective",
    "awkward shadows", "low sharpness", "too static", "lacks originality", "overcomplicated", "excessive blur",
    "overpowering background", "wrong lens choice", "too symmetrical", "confusing composition",
    "no leading lines", "lack of visual impact", "unnecessary elements"
];

$positive_words_tl = [
    "maganda", "kamangha-mangha", "malinaw", "malikhain", "propesyonal", "matingkad", 
    "kahanga-hanga", "detalyado", "perpektong ilaw", "kamangha-manghang kulay", "maliwanag",
    "malinaw ang subject", "malalim", "kamangha-manghang komposisyon", "perpektong anggulo", 
    "makulay", "hindi malabo", "matalino", "artistiko", "kahit anong anggulo ay maganda", 
    "hindi pilit", "magandang storytelling", "napaka-elegante", "malambot ang dating", 
    "buhay na buhay", "hindi matapang ang kulay", "hindi sobrang contrast", "parang totoo", 
    "perpektong focus", "maliwanag ang exposure", "dramatic", "romantic", "sobrang husay", 
    "pantay ang ilaw", "napaka-detalyado", "hindi sobra ang pag-edit", "mala-sine", "classy", 
    "hindi masakit sa mata", "perpektong saturation", "walang istorbo sa background", 
    "nagsasalita ang larawan", "napakalinis", "napaka-organisado", "elegante ang dating", 
    "parang obra maestra", "engaging", "swabe", "napaka-relaxing tingnan", "nagniningning", 
    "tamang contrast", "hindi pilit ang posing", "very natural", "walang distracting elements", 
    "hindi mukhang mura", "sobrang makatotohanan", "malakas ang dating", "astig", "bongga"
];
$negative_words_tl = [
    "malabo", "madilim", "hindi malinaw", "walang buhay", "sobrang edit", "sobrang contrast", 
    "walang dating", "pilit", "hindi pantay ang ilaw", "hindi magandang anggulo", "napaka-dull", 
    "hindi balanse", "masyadong saturated", "masakit sa mata", "hindi natural", "walang emosyon", 
    "hindi nakaka-enganyo", "napakalamig ng kulay", "parang hindi totoo", "hindi propesyonal", 
    "mukhang mura", "hindi pulido", "sobrang overexposed", "sobrang underexposed", 
    "may nakakaistorbong background", "walang focus", "hindi maayos ang framing", "blurred", 
    "hindi sharp", "masyadong madilim", "masyadong maliwanag", "may unwanted shadow", 
    "hindi flattering", "mukhang low quality", "sobrang filter", "hindi clear ang subject", 
    "hindi makuha ang tamang mood", "walang kwento", "random ang pagkaka-frame", 
    "hindi maayos ang pagkaka-compose", "mukhang ordinaryo", "hindi nakakahatak ng atensyon", 
    "masyadong flat", "sobrang soft", "sobrang harsh", "masyadong sharp", "hindi bagay ang kulay", 
    "mukhang outdated", "hindi consistent ang lighting", "hindi proporsyonado", 
    "nakakagulo ang background", "walang visual impact", "hindi mukhang creative"
];
$positive_words_bn = [
    "marhay", "magayon", "malinig", "maogma", "liwanag", "klaro", "matingkad", 
    "makusog an kulay", "tamang liwanag", "dakul an detalye", "mahamis", 
    "sakto an anggulo", "mabisto", "husto an focus", "masaya an dating", 
    "tunay", "nakaengganyo", "hustong komposisyon", "swabe an contrast", 
    "nagniningning", "matao an kahulugan", "relaxing", "natural", "artistiko", 
    "kaayaaya", "klarado an background", "malinamnam an kulay", "mala-sine", 
    "elegante", "misteryoso pero magayon", "husto an saturation", "balanse", 
    "mabuhay an emosyon", "payt", "dakulaon an impact", "hustong exposure", 
    "presko an dating", "inspirado", "hindi pilit", "may kwento", "swak an tono"
];
$negative_words_bn = [
    "dai marhay", "dai klaro", "maluya an kolor", "parang dai buhay", 
    "parang burong", "sobra an filter", "madilim", "sobra an contrast", 
    "dakul an shadow", "hustong overexposed", "hustong underexposed", 
    "dai balance", "grabe an saturation", "sobrang matao an background", 
    "dai propesyonal an dating", "maluya an dating", "dai impact", 
    "dai klarado an subject", "dai kahulugan", "flat an composition", 
    "dai magayon an frame", "dakul an istorbo", "parang ordinaryo", 
    "dakul an noise", "parang mura an kuha", "hustong maitim", "parang lampag", 
    "hustong makusog an highlights", "masyadong malabo", "sobrang edited", 
    "dakul an distraction", "garo dai pulos", "dai creative", "walang emosyon"
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