<?php

$stateFile = __DIR__ . "/state.json";

$tableContentWidth = 45;
$printTableBorder = function () use ($tableContentWidth): void {
    echo str_repeat("-", $tableContentWidth + 4) . "\n";
};
$printTableRow = function (string $text, int $padding = STR_PAD_RIGHT) use ($tableContentWidth): void {
    echo "| " . str_pad($text, $tableContentWidth, " ", $padding) . " |\n";
};
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
        !isset($state["hunger"], $state["thirst"], $state["health"], $state["survival_chance"]) ||
        !is_numeric($state["hunger"]) ||
        !is_numeric($state["thirst"]) ||
        !is_numeric($state["health"]) ||
        !is_numeric($state["survival_chance"])
    ) {
        exit("Error: state.json has an invalid format.\n");
    }

    return $state;
}

function saveState(string $file, array $state): void
{
    $json = json_encode($state);

    if ($json === false) {
        exit("Error: state could not be converted to JSON.\n");
    }

    if (file_put_contents($file, $json) === false) {
        exit("Error: state.json could not be saved.\n");
    }
}

function askChoice(string $question, array $options): string
{
    do {
        $choice = trim(readline($question));

        if (!in_array($choice, $options, true)) {
            echo "Invalid input! Please enter " . implode(", ", $options) . ".\n";
        }
    } while (!in_array($choice, $options, true));

    return $choice;
}

$state = loadState($stateFile);

foreach (["hunger", "thirst", "health", "survival_chance"] as $name) {
    $state[$name] = max(0, min(100, (int) $state[$name]));
}

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

$choice = askChoice("Choose an option: ", ["1", "2"]);

if ($choice === "2") {
    exit("Why did you even start it?\n");
}

$state["hunger"] = 100;
$state["thirst"] = 100;
$state["health"] = 100;
$state["survival_chance"] = 100;
$state["food"] = 1;
$state["water"] = 1;

saveState($stateFile, $state);

echo "Let's play...\n";
sleep(1);
echo "You must stay alive within 10 days\n";
sleep(1);
$printTableBorder();
echo "You were driving your car on your way to your vacation...\n";
sleep(1);
echo "But suddenly, zombies attacked your car...\n";
sleep(1);
echo "You broke away from them and hid yourself in a bunker...\n";
sleep(1);
echo "The outside is full of zombies, and every foray could be fatal...\n";
sleep(1);
echo "Let's see if you can survive!\n";

$printTableBorder();
$printTableRow("Select your character's gender", STR_PAD_BOTH);
$printTableBorder();
$printTableRow("1. Female", STR_PAD_BOTH);
$printTableBorder();
$printTableRow("2. Male", STR_PAD_BOTH);
$printTableBorder();

$genderChoice = askChoice("Choose an option: ", ["1", "2"]);

$gender = $genderChoice === "1" ? "Female" : "Male";

$printTableBorder();
$printTableRow("Your character will be " . $gender, STR_PAD_BOTH);
$printTableBorder();

do {
    $name = trim(readline("What's the name of your character? "));

    if ($name === "") {
        echo "Invalid input! Please enter your name.\n";
    }
} while ($name === "");

sleep(1);
$printTableBorder();
$printTableRow("Your character's name: " . $name . "!", STR_PAD_BOTH);
$printTableBorder();

$numberRounds = 0;
$leftGame = false;

while ($numberRounds < 10) {
    $numberRounds++;

    $state["hunger"] = max(0, $state["hunger"] - 8);
    $state["thirst"] = max(0, $state["thirst"] - 12);

    if ($state["hunger"] === 0 || $state["thirst"] === 0) {
        $state["health"] = max(0, $state["health"] - 15);
    }
    $state["survival_chance"] = (int) (($state["hunger"] + $state["thirst"] + $state["health"]) / 3);

    $printTableBorder();
    $printTableRow("ROUND " . $numberRounds . " - CHARACTER STATUS", STR_PAD_BOTH);
    $printTableBorder();
    $printTableRow("Health: " . $state["health"] . "%");
    $printTableRow("Hunger: " . $state["hunger"] . "%");
    $printTableRow("Thirst: " . $state["thirst"] . "%");
    $printTableRow("Survival chance: " . $state["survival_chance"] . "%");
    $printTableBorder();

    $printTableRow("1. Eat something", STR_PAD_BOTH);
    $printTableBorder();
    $printTableRow("2. Drink water", STR_PAD_BOTH);
    $printTableBorder();
    $printTableRow("3. Search the bunker", STR_PAD_BOTH);
    $printTableBorder();
    $printTableRow("4. Rest", STR_PAD_BOTH);
    $printTableBorder();
    $printTableRow("5. Leave the game", STR_PAD_BOTH);
    $printTableBorder();

    $choice = askChoice("Choose an option: ", ["1", "2", "3", "4", "5"]);

    if ($choice === "5") {
        saveState($stateFile, $state);
        $leftGame = true;
        echo "You chose to leave!\n";
        break;
    }

    if ($choice === "1") {
        if ($state["food"] > 0) {
            $state["food"]--;
            $state["hunger"] = max(0, min(100, $state["hunger"] + 25));
            echo "You ate some food. Hunger is now " . $state["hunger"] . "%.\n";
        } else {
            echo "You have no food left.\n";
        }
    } elseif ($choice === "2") {
        if ($state["water"] > 0) {
            $state["water"]--;
            $state["thirst"] = max(0, min(100, $state["thirst"] + 30));
            echo "You drank some water. Thirst is now " . $state["thirst"] . "%.\n";
        } else {
            echo "You have no water left.\n";
        }
    } elseif ($choice === "3") {
        $result = rand(1, 100);

        if ($result <= 50) {
            $state["food"]++;
            echo "You found one unit of food.\n";
        } elseif ($result <= 80) {
            $state["water"]++;
            echo "You found one unit of water.\n";
        } else {
            $state["health"] = max(0, $state["health"] - 50);
            echo "Zombies surprised you. Health decreased.\n";
        }
    } elseif ($choice === "4") {
        $state["health"] = max(0, min(100, $state["health"] + 5));
        echo "You rested and recovered a little health.\n";
    }

    $state["survival_chance"] = (int) (($state["hunger"] + $state["thirst"] + $state["health"]) / 3);
    saveState($stateFile, $state);

    if ($state["survival_chance"] <= 0 || $state["health"] <= 0) {
        echo "Your health or survival chance fell to 0%. You did not survive.\n";
        break;
    }
}