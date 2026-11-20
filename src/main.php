<?php

// вeclaring variables, loading state file
$stateFile = __DIR__ . "/state.json";

$numberRounds = 0;
$leftGame = false;

$state = loadState($stateFile);

// сreating variables to declare a menu
$tableContentWidth = 45;
$printTableBorder = function () use ($tableContentWidth): void {
    echo str_repeat("-", $tableContentWidth + 4) . "\n";
};
$printTableRow = function (string $text, int $padding = STR_PAD_RIGHT) use ($tableContentWidth): void {
    echo "| " . str_pad($text, $tableContentWidth, " ", $padding) . " |\n";
};

// сhecking a file for a character's status
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
        !is_array($state) or
        !isset($state["hunger"], $state["thirst"], $state["survival_chance"]) or
        !is_numeric($state["hunger"]) or
        !is_numeric($state["thirst"]) or
        !is_numeric($state["survival_chance"])
    ) {
        exit("Error: state.json has an invalid format.\n");
    }

    return $state;
}

// saving the character's current state
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

// validation of the answer to the question
// accept only a single digit that matches an available menu option.
function askChoice(string $question, array $options): string
{
    do {
        $input = readline($question);

        if ($input === false) {
            exit("\nInput closed. Goodbye!\n");
        }

        $choice = trim($input);

        $valid = preg_match('/^[0-9]$/', $choice) === 1
            and in_array($choice, $options, true);

        if (!$valid) {
            echo "Please enter " . implode(", ", $options) . ".\n";
        }
    } while (!$valid);

    return $choice;
}

// limiting maximum hunger, thirst and survival chance
foreach (["hunger", "thirst", "survival_chance"] as $name) {
    $state[$name] = max(0, min(100, (int) $state[$name]));
}

// start menu
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
    exit("Thanks for playing. Goodbye!\n");
}

// backgroundstory if 1 was chosen
echo "Let's play...\n";
sleep(1);
echo "You must stay alive within 10 days\n";
sleep(2);
$printTableBorder();
echo "You were driving your car on your way to your vacation...\n";
sleep(2);
echo "But suddenly, zombies attacked your car...\n";
sleep(2);
echo "You broke away from them and hid yourself in a bunker...\n";
sleep(2);
echo "The outside is full of zombies, and every foray could be fatal...\n";
sleep(2);
echo "Let's see if you can survive!\n";

sleep(2);

// game loop
do {
    $numberRounds = 0;
    $leftGame = false;
// reset statistics
    $state["hunger"] = 100;
    $state["thirst"] = 100;
    $state["survival_chance"] = 100;
    $state["food"] = 1;
    $state["water"] = 1;
// save start stats
    saveState($stateFile, $state);
// purpose 10 rounds 
    while ($numberRounds < 10) {
    $numberRounds++;
// A change status of stats every round
    $state["hunger"] = max(0, $state["hunger"] - 8);
    $state["thirst"] = max(0, $state["thirst"] - 12);
// round menu
    $state["survival_chance"] = (int) (($state["hunger"] + $state["thirst"]) / 2);

    $printTableBorder();
    $printTableRow("ROUND " . $numberRounds . " - CHARACTER STATUS", STR_PAD_BOTH);
    $printTableBorder();
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
    $printTableRow("4. Leave the game", STR_PAD_BOTH);
    $printTableBorder();
// a choice of choices
    $choice = askChoice("Choose an option: ", ["1", "2", "3", "4"]);
// early game end
    if ($choice === "4") {
        saveState($stateFile, $state);
        $leftGame = true;
        echo "You chose to leave!\n";
        break;
    }
// use of food supplies
    if ($choice === "1") {
        if ($state["food"] > 0) {
            $state["food"]--;
            $state["hunger"] = max(0, min(100, $state["hunger"] + 25));
            echo "You ate some food. Hunger is now " . $state["hunger"] . "%.\n";
        } else {
            echo "You have no food left.\n";
        }
// use of water supplies
    } elseif ($choice === "2") {
        if ($state["water"] > 0) {
            $state["water"]--;
            $state["thirst"] = max(0, min(100, $state["thirst"] + 25));
            echo "You drank some water. Thirst is now " . $state["thirst"] . "%.\n";
        } else {
            echo "You have no water left.\n";
        }
// searching for supplies in the bunker
    } elseif ($choice === "3") {
        $result = rand(1, 100);

        if ($result <= 40) {
            $state["food"]++;
            echo "You found one unit of food.\n";
        } elseif ($result <= 80) {
            $state["water"]++;
            echo "You found one unit of water.\n";
        } else {
            echo "You found nothing.\n";
        }
    }
// saving the chance of survival
    $state["survival_chance"] = (int) (($state["hunger"] + $state["thirst"]) / 2);
    saveState($stateFile, $state);
// сhecking if there's no chance
            if ($state["survival_chance"] <= 0) {
            break;
        }
    }

    if ($leftGame) {
        break;
    }
// victory declaration
    $won = false;
// сhecking the conditions for victory using odds
    if ($numberRounds === 10 and $state["survival_chance"] > 0) {
        $won = random_int(1, 100) <= $state["survival_chance"];
    }
// end menu
    $printTableBorder();
    $printTableRow("GAME RESULT", STR_PAD_BOTH);
    $printTableBorder();
// resultat of game
    if ($won) {
        $printTableRow("VICTORY! You survived!", STR_PAD_BOTH);
    } else {
        $printTableRow("DEFEAT! You did not survive.", STR_PAD_BOTH);
    }
// stats of game
    $printTableBorder();
    $printTableRow("Rounds played: " . $numberRounds . "/10");
    $printTableRow(
        "Final survival chance: " . $state["survival_chance"] . "%"
    );
    $printTableBorder();
    $printTableRow("1. Exit", STR_PAD_BOTH);
    $printTableRow("2. New game", STR_PAD_BOTH);
    $printTableBorder();

    $playAgain = askChoice("Choose an option: ", ["1", "2"]);
// if you select 2, start over
} while ($playAgain === "2");