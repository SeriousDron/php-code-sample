#  PHP Code Sample

### Background info

This code was written as a coding challenge for one of my previous jobs. Initial task description is in task folder. 
Vagrant config mostly was provided by interviewer.  

### Short task description

I was given short and dirty PHP code in 3 files and one executable file emulating some long external call (see `task/code/web` folder). 
Task was rewrite that code into production grade with modern development practices. Two files are entry points and have different subtask inside.
  
##### index.php 
The code is unscalable because it immediately runs `registermo` executable and wait until exit.
Task is to make `registermo` processing scalable so I make it happen in background and possibly on other servers using jon queue. 

##### stats.php
This code queries some data from database. Except code rewriting I had to optimize the query.
 
##### bonus goal 
I made command like tools to get number of `registermo` calls in the queue and clean the queue.

### Some assumptions/solutions made

1. I've assumed that I should write it as if it was part of big and complicated system so it's badly overengineered for task that 
small, but shows a lot of my design style.
2. I've two predefined entry points as PHP files so I don't need query router. And I don't need views and so on. 
And I want to show some architecture, after all. So I haven't used any framework, but I've used part I needed 
(mostly from Symfony components).
3. I'm using PHP-DI container just because I like to have dependency injection and this particular container.