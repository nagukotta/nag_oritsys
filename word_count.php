<?php
class db_connection
{
  private $host;
  private $user;
  private $pass;
  private $db;
  public $conn;

   function __construct()
   {    
     $GLOBALS['conn'] = $this->db_connect(); 
   }
   
   
   private function db_connect(){
   
    $this->host = 'localhost';
    $this->user = 'root';
    $this->pass = '';
    $this->db = 'test';   
    
    $this->mysqli = new mysqli($this->host, $this->user, $this->pass, $this->db);
    return $this->mysqli;
  }
  
   function unique_words($file)
   {  
        $text = file_get_contents($file);      
        $result_text = preg_split('/[\s?:;,.]+/', $text, -1, PREG_SPLIT_NO_EMPTY);     
        $unique_word_array = array_unique($result_text);
         
        return $unique_word_array;
   }
  
    //******************** This Method for Get Qnique Words from  text file and saving ***************************\\
     
   public function save_unique_words($file_path)
   {  
       global $conn;       
       $unique_str = implode(' ',  $this->unique_words($file_path));                    
      
       
      //Storing Unique Words Into Text File        
      $unique_words_count =  str_word_count($unique_str); 
      file_put_contents("unique_words.txt", "");
      $myfile = fopen("unique_words.txt", "w+");     
      fwrite($myfile, $unique_str);        
     
   
      // TABLE CREATION 
      if ($result = mysqli_query($conn, "SHOW TABLES LIKE 'user_files' ")) 
      {
          if($result->num_rows == 0) 
          {     
             $sql_create_tb = "CREATE TABLE user_files ( file_id INT(11)  AUTO_INCREMENT PRIMARY KEY, filename VARCHAR(100) NOT NULL,  num_of_unique_words int(11) NOT NULL )";
             mysqli_query($conn, $sql_create_tb);   
          }
      }

       $sql_add_file = "INSERT into  user_files (filename, num_of_unique_words) VALUES('unique_words.txt', $unique_words_count)";
       mysqli_query($conn, $sql_add_file);
 
       echo "Answer For Question 1 : <br>";
       echo "File Name is : unique_words.txt <br>";
       echo " Number of unique words count : ".$unique_words_count;
     
   }
   
   
 
  function get_watch_list($file_path)
  {    
    global $conn; 
    
    $sql = "select * from watchlist";     
    $qur = mysqli_query($conn, $sql);      
    
    while($r = mysqli_fetch_array($qur)){
         $items[] = $r['interest_word'];
    }
    
     
     $result=array_intersect($this->unique_words($file_path),$items);
     echo "  Matched list with watch list tems : ". implode(' ', $result);
  }
   
 
   
 
   
   
}

$obj = new db_connection();

$large_txt_file= "sample.txt";
$obj->save_unique_words($large_txt_file);

echo "<br> <br>";
$file_path = "fruits.txt";

 echo "Answer For Question 2 : <br>";
 echo "Unique Words are : ".implode(' ', $obj->unique_words($file_path));
 //$obj->unique_words($file_path)

  echo "<br> <br> Answer For Question 3 : <br>";
 $obj->get_watch_list($file_path);
 

?>
