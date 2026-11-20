<?php 


$stateFile = __DIR__ . "/state.json";

function loadState(string $file): array
{
    if (!file_exists($file)) {
        exit("Error: state.json was not found.\n");
    }

    $json = file_get_contents($file);

    if ($json === false) {
        exit("Error: state.json could not be read.\n");
    }

    $state = json_decode($json, true);

    if (
        !is_array($state) ||
        !isset($state["hunger"], $state["thirst"]) ||
        !is_numeric($state["hunger"]) ||
        !is_numeric($state["thirst"])
    ) {
        exit("Error: state.json has an invalid format.\n");
    }

    return $state;
}

function saveState(string $file, array $state): void
{
    $json = json_encode($state);
    file_put_contents($file, $json);
}

$state = loadState($stateFile);

$state["hunger"] = max(0, $state["hunger"] - 5);
$state["thirst"] = max(0, $state["thirst"] - 5);

saveState($stateFile, $state);

$number_rounds = 0;
$tableContentWidth = 45;
$printTableBorder = function () use ($tableContentWidth): void {
    echo str_repeat("-", $tableContentWidth + 4) . "\n";
};
$printTableRow = function (string $text, int $padding = STR_PAD_RIGHT) use ($tableContentWidth): void {
    echo "| " . str_pad($text, $tableContentWidth, " ", $padding) . " |\n";
};

// start menu and start of game
sleep(1);
$printTableBorder();
$printTableRow("Welcome to survive 10 stay alive", STR_PAD_BOTH);
$printTableBorder();
$printTableRow("1. Start game", STR_PAD_BOTH);
$printTableBorder();
$printTableRow("2. Exit", STR_PAD_BOTH);
$printTableBorder();
$printTableRow("@reincarnation", STR_PAD_BOTH);
$printTableBorder();

// selecting an option 
do {
    $choice = trim(readline("Choose an option: "));

    if (!in_array($choice, ["1", "2"], true)) {
        echo "Invalid input! Please enter 1 or 2.\n";
    }
} while (!in_array($choice, ["1", "2"], true));

// logic of choising
switch ($choice) {
    case "1":
        // background of story
        echo "Let's play...\n";
        sleep(1);
        echo "You must stay alive within 10 days \n";
        sleep(1);
        $printTableBorder();
        echo "You were driving your car on your way to your vacation... \n";
        sleep(1);
        echo "But suddenly, zombies attacked your car...\n";
        sleep(1);
        echo "You broke away from them and hide yourself in some kind of bunker...\n";
        sleep(1);
        echo "The outside is full of zombies, and every foray could be fatal...\n";
        sleep(1);
        echo "Let's see if you can survive!\n";
        break;

    case "2":
        exit("Why did you even start it?\n");

    default:
        exit("Invalid option.\n");
}

// character creation
sleep(1);
$printTableBorder();
$printTableRow("Select your character's gender", STR_PAD_BOTH);
$printTableBorder();
$printTableRow("1. Female", STR_PAD_BOTH);
$printTableBorder();
$printTableRow("2. Male", STR_PAD_BOTH);
$printTableBorder();

do {
    $choice2 = trim(readline("Choose an option: "));

    if (!in_array($choice2, ["1", "2"], true)) {
        echo "Invalid input! Please enter 1 or 2.\n";
    }
} while (!in_array($choice2, ["1", "2"], true));

switch ($choice2) {
    case "1":
        // charachter
        $printTableBorder();
        $printTableRow("Your character will be Female", STR_PAD_BOTH);
        $printTableBorder();
        break;

    case "2":
        $printTableBorder();
        $printTableRow("Your character will be Male", STR_PAD_BOTH);
        $printTableBorder();
        break;

    default:
        exit("Invalid option.\n");
}

sleep(1);

do {
    $name = trim(readline("What's will be name of your character? "));

    // validation
    if ($name === "") {
        echo "Invalid input! Please enter your name.\n";
    }
} while ($name === "");
sleep(1);

$printTableBorder();
$printTableRow("Your character's name: $name!", STR_PAD_BOTH);
$printTableBorder();

$rounds = $number_rounds + 1;

while ($number_rounds < 10) {
    if ($number_rounds > 0) {
        // menu with choise of actions
        $text = "STARTING ROUND $rounds. BE CAREFULL";
        $printTableBorder();
        $printTableRow($text);
        $printTableBorder();
        $printTableRow("1. View my inventory", STR_PAD_BOTH);
        $printTableBorder();
        $printTableRow("2. View possible actions", STR_PAD_BOTH);
        $printTableBorder();
        $printTableRow("3. View my chance of survival", STR_PAD_BOTH);
        $printTableBorder();
        $printTableRow("4. Leave the game", STR_PAD_BOTH);
        $printTableBorder();

        // validation of input
        do {
            $choice3 = trim(readline("Choose an option: "));

            if (!in_array($choice3, ["1", "2", "3", "4"], true)) {
                echo "Invalid input! Please enter 1, 2, 3 or 4\n";
            }
        } while (!in_array($choice3, ["1", "2", "3", "4"], true));
        
        // choice finish the test
        if ($choice3 === "4") {
            echo "You chose to leave!\n";
            break;
        }
    }
}