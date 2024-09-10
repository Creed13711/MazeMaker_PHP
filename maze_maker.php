<?php
//Set the time limit to infinite - needed for the large mazes
set_time_limit(0);
//Global Variables
$ROWS = 100;
$COLS = 100;
$MATRIX = [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Maze Maker</title>
    <link rel="stylesheet" href="mystyle.css">
</head>
<body>
<form method="POST" action="">
    <br>
    <h2>Maze Maker</h2><br>
    <label for="rows">Number of rows:</label>
    <input type="number" id="rows" name="rows" min="5" required><br>
    <br>
    <label for="cols">Number of columns:</label>
    <input type="number" id="cols" name="cols" min="5" required><br>
    <br>
    <input type="submit" name="submit" value="Generate and save maze"><br>
    <input type="reset" name="cancel" value="Cancel"><br>
    <br>
</form>
</body>
</html>

<?php
///POST
//submit for the rows and cols count
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    global $ROWS, $COLS;
    $ROWS = $_POST["rows"];
    $COLS = $_POST["cols"];
    GenerateAndSaveMaze();
}
///FUNCTIONS
//Generates a maze based on the input rows and cols and saves it to the user's downloads folder
function GenerateAndSaveMaze() {
    global $ROWS, $COLS;
    //set up the matrix
    SetUpMatrix();
    //generate a maze
    GenerateMaze();
    //generate an image of the maze
    $imageOfMaze = GenerateImage();
    //save the image of the maze
    SaveImage($imageOfMaze);
}
//Returns a bool matrix of specified rows and cols with all positions set to false
function SetUpMatrix() {
    global $ROWS, $COLS, $MATRIX;
    $MATRIX = [$ROWS];
    for ($i = 0; $i < $ROWS; $i++) {
        //initialize each row as an array filled with 'false' values
        $MATRIX[$i] = array_fill(0, $COLS, false);
    }
}
//Uses $MATRIX to create a maze of true and false positions
function GenerateMaze() {
    global $ROWS, $COLS, $MATRIX;
    //create the array that will store moves
    $positions = [];
    //set the start location and push the first position
    $startRow = (int) rand($ROWS / 4, $ROWS / 4 * 3);
    $startCol = (int) rand($COLS / 4, $COLS / 4 * 3);
    $firstPosition = [$startRow, $startCol];
    array_push($positions, $firstPosition);
    //continue going through the possible positions, pushing each valid position to the array, for as long as there are moves left
    while(count($positions) > 0) {
        $currentPosition = array_pop($positions);
        if(($currentPosition[0] == $startRow && $currentPosition[1] == $startCol) || ValidMove($currentPosition[0], $currentPosition[1])) {
            //mark the spot as true
            $MATRIX[$currentPosition[0]][$currentPosition[1]] = true;
            //get and save the 4 possible moves
            $nextMoves = GetNextMoves($currentPosition[0], $currentPosition[1]);
            //check each move and push the valid ones to the array
            foreach ($nextMoves as $nextMove) {
                if(ValidMove($nextMove[0], $nextMove[1])) {
                    $validNextMove = [$nextMove[0], $nextMove[1]];
                    array_push($positions, $validNextMove);
                }
            }
        }
    }
}
//Takes in a position and returns the next moves
function GetNextMoves($currentRow, $currentCol) : array{
    $moves = [
      [$currentRow - 1, $currentCol],
      [$currentRow + 1, $currentCol],
      [$currentRow, $currentCol - 1],
      [$currentRow, $currentCol + 1]
    ];
    Shuffle($moves);
    return $moves;
}
//Check that a move is valid - valid move has only one bordering true spot
function ValidMove($row, $col) : bool {
    global $ROWS, $COLS, $MATRIX;
    //check that it is within bounds
    if ($row < 0 || $row >= $ROWS || $col < 0 || $col >= $COLS) {
        return false;
    }
    //check that it is not already a true spot
    if($MATRIX[$row][$col]) {
        return false;
    }
    //variable to track how many bordering spaces are set to true
    $borderingTrue = 0;
    //check all the bordering spaces
    if(($row-1 >= 0 && $row-1 < $ROWS) && $MATRIX[$row-1][$col]) {
        $borderingTrue++;
    }
    if(($row+1 >= 0 && $row+1 < $ROWS) && $MATRIX[$row+1][$col]) {
        $borderingTrue++;
    }
    if(($col-1 >= 0 && $col-1 < $COLS) && $MATRIX[$row][$col-1]) {
        $borderingTrue++;
    }
    if(($col+1 >= 0 && $col+1 < $COLS) && $MATRIX[$row][$col+1]) {
        $borderingTrue++;
    }
    //validate whether it is a valid spot
    if($borderingTrue == 1) return true;
    else return false;
}
//Generates an image from the bool matrix
function GenerateImage(){
    global $ROWS, $COLS, $MATRIX;
    //create image
    $image = imagecreatetruecolor($COLS, $ROWS);
    //define colors
    $black = imagecolorallocate($image, 0, 0, 0);
    $white = imagecolorallocate($image, 255, 255, 255);
    //loop through the matrix and set the corresponding pixel color
    for ($row = 0; $row < $ROWS; $row++) {
        for ($col = 0; $col < $COLS; $col++) {
            $color = $MATRIX[$row][$col] ? $white : $black;
            imagesetpixel($image, $col, $row, $color);
        }
    }
    return $image;
}
//Saves a given image to the user's downloads folder
function SaveImage($image){
    global $ROWS, $COLS;
    //set the path to save the file in the user's Downloads folder
    $outputFileName = "maze_" . $ROWS . "R" . $COLS . "C.png";
    $homeDir = getenv('USERPROFILE') ?: getenv('HOME');
    $downloadDir = $homeDir . "/Downloads/";
    $filePath = $downloadDir . DIRECTORY_SEPARATOR . $outputFileName;
    //save the image as PNG
    imagepng($image, $filePath);
    //free memory
    imagedestroy($image);
    echo "Image saved to " . $filePath . "\n";
}
?>

















