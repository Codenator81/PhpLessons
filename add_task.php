<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Add a Task</title>

</head>
<body>
<?php # Script 1.2 - add_task.php
$mysqli = new mysqli('localhost', 'root', 'root', 'phpulmi');

/* check connection */
if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}
   // Check if the form has been submitted:
   if (($_SERVER['REQUEST_METHOD'] == 'POST') && !empty($_POST['task'])) {

      // Sanctify the input...

      // The parent_id must be an integer:
      if (isset($_POST['parent_id']) &&
              filter_var($_POST['parent_id'], FILTER_VALIDATE_INT, ['min_range' => 1]) ) {
                 $parent_id = $_POST['parent_id'];
      } else {
                 $parent_id = 0;
      }

      // Escape the task:
      $task = mysqli_real_escape_string($mysqli, strip_tags($_POST['task']));

      // Add the task to the database.
      $q = "INSERT INTO tasks (parent_id, task) VALUES ($parent_id, '$task')";
      $r = $mysqli->query($q);

      // Report on the results:
      if (mysqli_affected_rows($mysqli) == 1) {
                 echo '<p>The task has been added!</p>';
      } else {
                 echo '<p>The task could not be added!</p>';
      }

   } // End of submission IF.

   // Display the form:
   echo '<form action="add_task.php" method="post">
   <fieldset style="width:200px;">
      <legend>Add a Task</legend>
      <p>Task: <input name="task" type="text" size="60" maxlength="100" required></p>
      <p>Parent Task: <select name="parent_id"><option value="0">None</option>';

   // Retrieve all the uncompleted tasks:
   $q = 'SELECT task_id, parent_id, task FROM tasks WHERE date_completed="0000-00-00 00:00:00" ORDER BY date_added ASC';
   $r = $mysqli->query($q);

   // Also store the tasks in an array for use later:
   $tasks = [];

   // Fetch the records:
  while (list($task_id, $parent_id, $task) = mysqli_fetch_array($r, MYSQLI_NUM)) {

     // Add to the select menu:
     echo "<option value=\"$task_id\">$task</option>\n";

     // Add to the array:
     $tasks[] = ['task_id' => $task_id, 'parent_id' => $parent_id, 'task' => $task];

  }

   // Complete the form:
   echo '</select></p>
   <input name="submit" type="submit" value="Add This Task">
   </fieldset>
   </form>';

   // Sort the tasks by parent_id:
   function parent_sort($x, $y) {
          return ($x['parent_id'] > $y['parent_id']);
   }
   usort($tasks, 'parent_sort');

   // Display all the tasks:
   echo '<h2>Current To-Do List</h2><ul>';
   foreach ($tasks as $task) {
          echo "<li>{$task['task']}</li>\n";
   }
   echo '</ul>';
   ?>
   </body>
   </html>
<?php
