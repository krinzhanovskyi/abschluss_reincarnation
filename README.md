# SURVIVE 10 DAYS AND STAY ALIVE

`survive 10 days and stay alive` is a console-based survival game set in a post-apocalyptic world. After zombies attack the player's car, the character hides in a bunker. The player must manage limited resources and survive 10 rounds.

All interactions take place through a text menu in the terminal.

## Features

- 10 rounds of survival;
- hunger and thirst statistics;
- survival chance;
- limited food and water supplies;
- random resource searches;
- victory and defeat conditions;
- saved game state in a JSON file;
- input validation for menu choices.

## How to Play

At the beginning of each round, hunger decreases by 8% and thirst decreases by 12%. The player can then choose one of the following actions:

1. **Eat something** - consume one unit of food and restore hunger by 25%.
2. **Drink water** - consume one unit of water and restore thirst by 25%.
3. **Search the bunker** - try to find supplies:
   - 40% chance to find food;
   - 40% chance to find water;
   - 20% chance to find nothing.
4. **Leave the game** - save the current state and exit.

The survival chance is calculated as the average of hunger and thirst. It is limited to a range from 0 to 100. The game ends in defeat early if the survival chance reaches 0.
After round 10, the final survival chance is used as the probability of victory. For example, a 60% survival chance gives a 60% chance of winning.

## Requirements

- PHP with the `readline` function;
- PowerShell or Windows Command Prompt.

Check the PHP installation with:

```powershell
php --version
```

## Running the Game

Open a terminal in the project root and run:

```powershell
php src/main.php
```

## Saved State

The current game data is saved in [src/state.json](src/state.json). The file stores hunger, thirst, survival chance, and the collected supplies.
