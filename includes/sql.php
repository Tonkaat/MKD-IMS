<?php
  require_once('includes/load.php');

/*--------------------------------------------------------------*/
/* Function for find all database table rows by table name
/*--------------------------------------------------------------*/
function find_all($table) {
   global $db;
   if(tableExists($table))
   {
     return find_by_sql("SELECT * FROM ".$db->escape($table));
   }
}


function find_all_assoc($table) {
  global $db;
  $sql = "SELECT * FROM {$table}";
  $result = $db->query($sql);
  return $result->fetch_all(MYSQLI_ASSOC); // This should return an associative array
}
/*--------------------------------------------------------------*/
/* Function for Perform queries
/*--------------------------------------------------------------*/
function find_by_sql($sql)
{
  global $db;
  $result = $db->query($sql);
  $result_set = $db->while_loop($result);
 return $result_set;
}
/*--------------------------------------------------------------*/
/*  Function for Find data from table by id
/*--------------------------------------------------------------*/
function find_by_id($table,$id)
{
  global $db;
  $id = (int)$id;
    if(tableExists($table)){
          $sql = $db->query("SELECT * FROM {$db->escape($table)} WHERE id='{$db->escape($id)}' LIMIT 1");
          if($result = $db->fetch_assoc($sql))
            return $result;
          else
            return null;
     }
}
/*--------------------------------------------------------------*/
/* Function for Delete data from table by id
/*--------------------------------------------------------------*/
function delete_by_id($table,$id)
{
  global $db;
  if(tableExists($table))
   {
    $sql = "DELETE FROM ".$db->escape($table);
    $sql .= " WHERE id=". $db->escape($id);
    $sql .= " LIMIT 1";
    $db->query($sql);
    return ($db->affected_rows() === 1) ? true : false;
   }
}
/*--------------------------------------------------------------*/
/* Function for Count id  By table name
/*--------------------------------------------------------------*/

function count_by_id($table){
  global $db;
  if(tableExists($table))
  {
    $sql    = "SELECT COUNT(id) AS total FROM ".$db->escape($table);
    $result = $db->query($sql);
     return($db->fetch_assoc($result));
  }
}
/*--------------------------------------------------------------*/
/* Determine if database table exists
/*--------------------------------------------------------------*/
function tableExists($table){
  global $db;
  $table_exit = $db->query('SHOW TABLES FROM '.DB_NAME.' LIKE "'.$db->escape($table).'"');
      if($table_exit) {
        if($db->num_rows($table_exit) > 0)
              return true;
         else
              return false;
      }
  }
 /*--------------------------------------------------------------*/
 /* Login with the data provided in $_POST,
 /* coming from the login form.
/*--------------------------------------------------------------*/
  function authenticate($username='', $password='') {
    global $db;
    $username = $db->escape($username);
    $password = $db->escape($password);
    $sql  = sprintf("SELECT id,username,password,user_level FROM users WHERE username ='%s' LIMIT 1", $username);
    $result = $db->query($sql);
    if($db->num_rows($result)){
      $user = $db->fetch_assoc($result);
      $password_request = sha1($password);
      if($password_request === $user['password'] ){
        return $user['id'];
      }
    }
   return false;
  }
  /*--------------------------------------------------------------*/
  /* Login with the data provided in $_POST,
  /* coming from the login_v2.php form.
  /* If you used this method then remove authenticate function.
 /*--------------------------------------------------------------*/
   function authenticate_v2($username='', $password='') {
     global $db;
     $username = $db->escape($username);
     $password = $db->escape($password);
     $sql  = sprintf("SELECT id,username,password,user_level FROM users WHERE username ='%s' LIMIT 1", $username);
     $result = $db->query($sql);
     if($db->num_rows($result)){
       $user = $db->fetch_assoc($result);
       $password_request = sha1($password);
       if($password_request === $user['password'] ){
         return $user;
       }
     }
    return false;
   }


  /*--------------------------------------------------------------*/
  /* Find current log in user by session id
  /*--------------------------------------------------------------*/
  function current_user(){
      static $current_user;
      global $db;
      if(!$current_user){
         if(isset($_SESSION['user_id'])):
             $user_id = intval($_SESSION['user_id']);
             $current_user = find_by_id('users',$user_id);
        endif;
      }
    return $current_user;
  }
  /*--------------------------------------------------------------*/
  /* Find all user by
  /* Joining users table and user gropus table
  /*--------------------------------------------------------------*/
  function find_all_user(){
      global $db;
      $results = array();
      $sql  = "SELECT u.id, u.name AS user_name, u.username, u.status, u.last_login, ";
      $sql .= "g.group_name, l.name AS location ";
      $sql .= "FROM users u ";
      $sql .= "LEFT JOIN user_groups g ON u.user_level = g.group_level ";
      $sql .= "LEFT JOIN location l ON u.location_id = l.id "; // Join with locations table
      $sql .= "ORDER BY u.name ASC";
      $result = find_by_sql($sql);
      return $result;
  }
  /*--------------------------------------------------------------*/
  /* Function to update the last log in of a user
  /*--------------------------------------------------------------*/

 function updateLastLogIn($user_id)
	{
		global $db;
    $date = make_date();
    $sql = "UPDATE users SET last_login='{$date}' WHERE id ='{$user_id}' LIMIT 1";
    $result = $db->query($sql);
    return ($result && $db->affected_rows() === 1 ? true : false);
	}

  /*--------------------------------------------------------------*/
  /* Find all Group name
  /*--------------------------------------------------------------*/
  function find_by_groupName($val)
  {
    global $db;
    $sql = "SELECT group_name FROM user_groups WHERE group_name = '{$db->escape($val)}' LIMIT 1 ";
    $result = $db->query($sql);
    return($db->num_rows($result) === 0 ? true : false);
  }
  /*--------------------------------------------------------------*/
  /* Find group level
  /*--------------------------------------------------------------*/
  function find_by_groupLevel($level)
  {
    global $db;
    $sql = "SELECT group_level FROM user_groups WHERE group_level = '{$db->escape($level)}' LIMIT 1 ";
    $result = $db->query($sql);
    return($db->num_rows($result) === 0 ? true : false);
  }
  /*--------------------------------------------------------------*/
  /* Function for cheaking which user level has access to page
  /*--------------------------------------------------------------*/
   function page_require_level($require_level){
     global $session;
     $current_user = current_user();
     $login_level = find_by_groupLevel($current_user['user_level']);
     //if user not login
     if (!$session->isUserLoggedIn(true)):
            $session->msg('d','Please login...');
            redirect('index.php', false);
      //if Group status Deactive
     elseif($login_level['group_status'] === '0'):
           $session->msg('d','This level user has been band!');
           redirect('home.php',false);
      //cheackin log in User level and Require level is Less than or equal to
     elseif($current_user['user_level'] <= (int)$require_level):
              return true;
      else:
            $session->msg("d", "Sorry! you dont have permission to view the page.");
            redirect('home.php', false);
        endif;

     }
   /*--------------------------------------------------------------*/
   /* Function for Finding all product name
   /* JOIN with categorie  and media database table
   /*--------------------------------------------------------------*/
  function join_product_table(){
     global $db;
    $sql  = "SELECT p.id, p.name, p.quantity, p.media_id, p.date, c.name AS categorie, m.file_name AS image, ";
    $sql  .= "l.name AS location, s.name AS status ";  // Adding location and status
    $sql  .=" FROM products p";
    $sql  .=" LEFT JOIN categories c ON c.id = p.categorie_id";
    $sql  .=" LEFT JOIN media m ON m.id = p.media_id";
    $sql  .=" LEFT JOIN location l ON l.id = p.location_id";
    $sql  .=" LEFT JOIN status s ON s.id = p.status_id";
    $sql  .=" ORDER BY p.id ASC";
    return find_by_sql($sql);

   }

   function join_stock_table() {
      global $db;
      $sql  = "SELECT s.id, p.stock_number, p.status,";
      $sql  .= "l.name AS location, s.name AS status ";  // Adding location and status
      $sql  .=" FROM stocks s";
      $sql  .=" LEFT JOIN categories c ON c.id = p.categorie_id";
      $sql  .=" LEFT JOIN media m ON m.id = p.media_id";
      $sql  .=" LEFT JOIN location l ON l.id = p.location_id";
      $sql  .=" LEFT JOIN status s ON s.id = p.status_id";
      $sql  .=" ORDER BY p.id ASC";
      return find_by_sql($sql);
    
   }
  /*--------------------------------------------------------------*/
  /* Function for Finding all product name
  /* Request coming from ajax.php for auto suggest
  /*--------------------------------------------------------------*/

   function find_product_by_title($product_name){
     global $db;
     $p_name = remove_junk($db->escape($product_name));
     $sql = "SELECT name FROM products WHERE name like '%$p_name%' LIMIT 5";
     $result = find_by_sql($sql);
     return $result;
   }

  /*--------------------------------------------------------------*/
  /* Function for Finding all product info by product title
  /* Request coming from ajax.php
  /*--------------------------------------------------------------*/
  function find_all_product_info_by_title($title){
    global $db;
    $sql  = "SELECT * FROM products ";
    $sql .= " WHERE name ='{$title}'";
    $sql .=" LIMIT 1";
    return find_by_sql($sql);
  }

  /*--------------------------------------------------------------*/
  /* Function for Update product quantity
  /*--------------------------------------------------------------*/
  function update_product_qty($qty,$p_id){
    global $db;
    $qty = (int) $qty;
    $id  = (int)$p_id;
    $sql = "UPDATE products SET quantity=quantity -'{$qty}' WHERE id = '{$id}'";
    $result = $db->query($sql);
    return($db->affected_rows() === 1 ? true : false);

  }
  /*--------------------------------------------------------------*/
  /* Function for Display Recent product Added
  /*--------------------------------------------------------------*/
//  function find_recent_product_added($limit){
//    global $db;
//    $sql   = " SELECT p.id,p.name,p.sale_price,p.media_id,c.name AS categorie,";
//    $sql  .= "m.file_name AS image FROM products p";
//    $sql  .= " LEFT JOIN categories c ON c.id = p.categorie_id";
//    $sql  .= " LEFT JOIN media m ON m.id = p.media_id";
//    $sql  .= " ORDER BY p.id DESC LIMIT ".$db->escape((int)$limit);
//    return find_by_sql($sql);
//  }
//  /*--------------------------------------------------------------*/
//  /* Function for Find Highest saleing Product
//  /*--------------------------------------------------------------*/
//  function find_higest_saleing_product($limit){
//    global $db;
//    $sql  = "SELECT p.name, COUNT(s.product_id) AS totalSold, SUM(s.qty) AS totalQty";
//    $sql .= " FROM sales s";
//    $sql .= " LEFT JOIN products p ON p.id = s.product_id ";
//    $sql .= " GROUP BY s.product_id";
//    $sql .= " ORDER BY SUM(s.qty) DESC LIMIT ".$db->escape((int)$limit);
//    return $db->query($sql);
//  }
//  /*--------------------------------------------------------------*/
//  /* Function for find all sales
//  /*--------------------------------------------------------------*/
//  function find_all_sale(){
//    global $db;
//    $sql  = "SELECT s.id,s.qty,s.price,s.date,p.name";
//    $sql .= " FROM sales s";
//    $sql .= " LEFT JOIN products p ON s.product_id = p.id";
//    $sql .= " ORDER BY s.date DESC";
//    return find_by_sql($sql);
//  }
//  /*--------------------------------------------------------------*/
//  /* Function for Display Recent sale
//  /*--------------------------------------------------------------*/
// function find_recent_sale_added($limit){
//   global $db;
//   $sql  = "SELECT s.id,s.qty,s.price,s.date,p.name";
//   $sql .= " FROM sales s";
//   $sql .= " LEFT JOIN products p ON s.product_id = p.id";
//   $sql .= " ORDER BY s.date DESC LIMIT ".$db->escape((int)$limit);
//   return find_by_sql($sql);
// }
// /*--------------------------------------------------------------*/
// /* Function for Generate sales report by two dates
// /*--------------------------------------------------------------*/
// function find_sale_by_dates($start_date,$end_date){
//   global $db;
//   $start_date  = date("Y-m-d", strtotime($start_date));
//   $end_date    = date("Y-m-d", strtotime($end_date));
//   $sql  = "SELECT s.date, p.name,p.sale_price,p.buy_price,";
//   $sql .= "COUNT(s.product_id) AS total_records,";
//   $sql .= "SUM(s.qty) AS total_sales,";
//   $sql .= "SUM(p.sale_price * s.qty) AS total_saleing_price,";
//   $sql .= "SUM(p.buy_price * s.qty) AS total_buying_price ";
//   $sql .= "FROM sales s ";
//   $sql .= "LEFT JOIN products p ON s.product_id = p.id";
//   $sql .= " WHERE s.date BETWEEN '{$start_date}' AND '{$end_date}'";
//   $sql .= " GROUP BY DATE(s.date),p.name";
//   $sql .= " ORDER BY DATE(s.date) DESC";
//   return $db->query($sql);
// }
// /*--------------------------------------------------------------*/
// /* Function for Generate Daily sales report
// /*--------------------------------------------------------------*/
// function  dailySales($year,$month){
//   global $db;
//   $sql  = "SELECT s.qty,";
//   $sql .= " DATE_FORMAT(s.date, '%Y-%m-%e') AS date,p.name,";
//   $sql .= "SUM(p.sale_price * s.qty) AS total_saleing_price";
//   $sql .= " FROM sales s";
//   $sql .= " LEFT JOIN products p ON s.product_id = p.id";
//   $sql .= " WHERE DATE_FORMAT(s.date, '%Y-%m' ) = '{$year}-{$month}'";
//   $sql .= " GROUP BY DATE_FORMAT( s.date,  '%e' ),s.product_id";
//   return find_by_sql($sql);
// }
// /*--------------------------------------------------------------*/
// /* Function for Generate Monthly sales report
// /*--------------------------------------------------------------*/
// function  monthlySales($year){
//   global $db;
//   $sql  = "SELECT s.qty,";
//   $sql .= " DATE_FORMAT(s.date, '%Y-%m-%e') AS date,p.name,";
//   $sql .= "SUM(p.sale_price * s.qty) AS total_saleing_price";
//   $sql .= " FROM sales s";
//   $sql .= " LEFT JOIN products p ON s.product_id = p.id";
//   $sql .= " WHERE DATE_FORMAT(s.date, '%Y' ) = '{$year}'";
//   $sql .= " GROUP BY DATE_FORMAT( s.date,  '%c' ),s.product_id";
//   $sql .= " ORDER BY date_format(s.date, '%c' ) ASC";
//   return find_by_sql($sql);
// }

//
function getStockBreakdown() {
  global $db;

  $sql = "
      SELECT 
          c.name AS category_name,
          SUM(p.quantity) AS total_stock
      FROM 
          products p
      JOIN 
          categories c ON p.categorie_id = c.id
      GROUP BY 
          c.name;
  ";

  $result = $db->query($sql);

  // Check if the query returns results
  if ($result->num_rows > 0) {
      $stockData = [];

      // Fetch data and store it in an array
      while ($row = $result->fetch_assoc()) {
          $stockData[] = [
              'category_name' => $row['category_name'],
              'total_stock' => $row['total_stock']
          ];
      }

      return $stockData;
  } else {
      return [];
  }
}

// // Example usage of the function
// $stockData = getStockBreakdown();
// if (!empty($stockData)) {
//   foreach ($stockData as $data) {
//       echo "Category: " . $data['category_name'] . " - Total Stock: " . $data['total_stock'] . "<br>";
//   }
// } else {
//   echo "No data found.";
// }

/*--------------------------------------------------------------*/
/* Function for Counting Borrowed items
/*--------------------------------------------------------------*/
function count_borrowed_items() {
  global $db;
  $query = "SELECT COUNT(*) AS total FROM borrowed_items WHERE stat = 'Borrowed'";
  $result = $db->query($query);
  $row = $result->fetch_assoc();
  return $row['total'];
}


function count_missinglost_items() {
  global $db;
  $query = "SELECT COUNT(*) AS total FROM products WHERE status_id IN (3, 4)";
  $result = $db->query($query);
  $row = $result->fetch_assoc();
  return $row['total'];
}

// Example of the function that fetches missing/lost items
function get_missing_lost_items() {
  global $db; // Assuming you are using $db as your database connection

  // Query to fetch missing/lost items
  $query = "SELECT * FROM stock WHERE status_id IN (3, 4)";
  $result = $db->query($query);

  // Return the result as an array
  return $db->while_loop($result);
}

function find_by_location_id($table, $location_id) {
  global $db;
  $sql = "SELECT * FROM " . $table . " WHERE location_id = '{$location_id}'";
  return find_by_sql($sql);
}


// Function to log recent actions
function log_recent_action($user_id, $action) {
  global $db;

  // Make sure both parameters are safe to use in the query
  $user_id = (int)$user_id;
  $action = $db->escape($action);  // Escape special characters for security

  // SQL query to log the action
  $sql = "INSERT INTO recent_actions (user_id, action, timestamp) VALUES ('{$user_id}', '{$action}', NOW())";
  
  // Execute the query
  if($db->query($sql)){
    return true;
  } else {
    return false;
  }
}

function find_recent_actions($limit = 10) {
  global $db;
  // Modify the query to join recent_actions and users tables to fetch the username
  $sql = "SELECT ra.action, ra.user_id, u.username, ra.timestamp 
          FROM recent_actions ra
          JOIN users u ON ra.user_id = u.id
          ORDER BY ra.timestamp DESC LIMIT " . (int)$limit;
  
  $result = $db->query($sql);
  
  if ($result && $db->num_rows($result) > 0) {
      return $result->fetch_all(MYSQLI_ASSOC);
  } else {
      return [];
  }
}

function fetch_logged_in_users() {
  global $db;
  $sql = "SELECT username, user_level, last_login FROM users WHERE is_logged_in = 1 ORDER BY last_login DESC";
  $result = $db->query($sql);
  return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

// Modify the SQL query to fetch all access logs
function fetch_access_log() {
  global $db;
  
  // SQL query to fetch all logs, ordered by timestamp (newest first)
  $sql = "SELECT ra.action, ra.user_id, u.username, ra.timestamp 
          FROM recent_actions ra
          JOIN users u ON ra.user_id = u.id
          ORDER BY ra.timestamp DESC LIMIT ";
  $result = $db->query($sql);
  
  if ($result && $db->num_rows($result) > 0) {
      return $result->fetch_all(MYSQLI_ASSOC);
  } else {
      return [];
  }
}

function insert_report_history($user_id, $location_id, $report_type, $file_path) {
  global $db;
  $query  = "INSERT INTO report_history (generated_by, location_id, report_type, file_path) ";
  $query .= "VALUES ('{$user_id}', '{$location_id}', '{$report_type}', '{$file_path}')";
  if ($db->query($query)) {
      return true;
  } else {
      return false;
  }
}

function fetch_report_history() {
  global $db;
  $query = "SELECT * FROM report_history ORDER BY generated_at DESC";
  $result = $db->query($query);
  return $result;
}


// Get user info by user ID
function get_user_by_id($user_id) {
    global $db;
    $query = "SELECT * FROM users WHERE id = {$user_id} LIMIT 1";
    $result = $db->query($query);
    return $result ? $result->fetch_assoc() : null;
}

// Get location name by location ID
function get_location_name($location_id) {
  global $db;

  // Check if the location ID is valid (not empty)
  if (empty($location_id) || !is_numeric($location_id)) {
      return 'All Locations'; // Default value if location_id is empty or invalid
  }

  $query = "SELECT name FROM location WHERE id = {$location_id} LIMIT 1";
  $result = $db->query($query);

  // Check if the query returns a valid result
  if ($result && $result->num_rows > 0) {
      return $result->fetch_assoc()['name'];
  } else {
      return 'Unknown Location'; // Return a default value if no result found
  }
}

function check_overdue_items() {
  global $db;
  $today = date('Y-m-d');

  $sql = "SELECT * FROM borrowed_items WHERE due_date < '$today'";
  $result = $db->query($sql);

  if ($db->num_rows($result) > 0) {
      while ($row = $result->fetch_assoc()) {
          echo "Item " . $row['item_id'] . " is overdue!<br>";
          // Send email or notification here
      }
  }
}


?>



