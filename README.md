To run:
- Open command line and change directory to wherever "maze_maker.php" is.
- Start the php server by running "php -S localhost:8080" (8080 can be changed)
- Open web browser and go to "http://localhost:8080/maze_maker.php"

Pre-requisites:
- Have PHP installed, preferable the latest version.
- It is a good idea to increase the "memory_limit" variable, in the php.ini file under "Resource Limits", to "256M" or "512M" to allow for more memory usage with large scripts.

Notes:
- Tested up to 1000 x 1000 maze, has no issues.
- Start point for generation is random withing the middle two quadrants of the maze.
- Select any points as your start and end point.
