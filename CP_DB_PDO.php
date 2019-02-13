<?php
/* version 2.0.1
 * updated Sept 2018
 * Fixed bug quotes character e.g. ' or " are not inserted 
 * prepared statements are not used correctly before
 */
class CpPdo {
    
    /*** mysql hostname ***/
private $hostname = 'localhost';

/*** mysql username ***/
private $username = 'root';


/*** mysql password ***/
private $password = '';

private $dbh;


function __construct($dbname="milestone451pm",$username="greenflag", $password="Harmeet915pm#") {
        try {
                $this->dbh = new PDO("mysql:host=$this->hostname;dbname=$dbname", $username, $password);
                /*** echo a message saying we have connected ***/
                //echo 'Connected to database'.$dbname.$username.$password;
                }
    catch(PDOException $e)
            {
            echo $e->getMessage();
            exit();
            }
    }
public function cpSelect($tablename,$value1=0,$value2=0) {
     /*** The SQL SELECT statement ***/
   /*
    * Prepare the select statement
    */
    $sql="SELECT * FROM $tablename";
      if($value1!=0)
      { $key1= key($value1);
           $sql.=" where $key1='$value1[$key1]'";
      }
       if($value1!=0 && $value2!=0) 
       {
           $key2= key($value2);
           $sql.=" AND $key2='$value2[$key2]'";
       }
   
        $sth = $this->dbh->prepare($sql);
        $sth->execute();
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);
        return $result;
 
}
public function cpSearch($tablename,$value1=0,$value2=0) {
     /*** The SQL SELECT statement to search 
      Usage:
      cpSearch(name_of_table, first_field_search, second_field_search)


     ***/

   /*
    * Prepare the select statement
    */
    $sql="SELECT * FROM $tablename";
      if($value1!=0)
      { $key1= key($value1);
           $sql.=" where $key1 like '%$value1[$key1]%'";
      }
       if($value1!=0 && $value2!=0) 
       {
           $key2= key($value2);
           $sql.=" AND $key2='$value2[$key2]'";
       }
   
        $sth = $this->dbh->prepare($sql);
        $sth->execute();
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);
        return $result;
 
}
public function cpSelectAsc($tablename,$value3=0,$value1=0,$value2=0) {
     /*** The SQL SELECT statement ***/
   /*
    * Prepare the select statement
    */
    $sql="SELECT * FROM $tablename";
      if($value1!=0)
      { $key1= key($value1);
           $sql.=" where $key1='$value1[$key1]'";
      }
       if($value1!=0 && $value2!=0) 
       {
           $key2= key($value2);
           $sql.=" AND $key2='$value2[$key2]'";
       }
   $sql.= " ORDER BY $value3 ASC";
  
        $sth = $this->dbh->prepare($sql);
        $sth->execute();
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);
        return $result;
 
}
public function cpSelectDsc($tablename,$value3=0, $value1=0,$value2=0) {
     /*** The SQL SELECT statement ***/
   /*
    * Prepare the select statement
    */
    $sql="SELECT * FROM $tablename";
      if($value1!=0)
      { $key1= key($value1);
           $sql.=" where $key1='$value1[$key1]'";
      }
       if($value1!=0 && $value2!=0) 
       {
           $key2= key($value2);
           $sql.=" AND $key2='$value2[$key2]'";
       }
   $sql.= " ORDER BY $value3 DESC";
  
        $sth = $this->dbh->prepare($sql);
        $sth->execute();
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);
        return $result;
 
}
/*/*this function is used for pagination
 * value 3 is string (col name)
 * value 2 is comparison value optional array
 * value 1 is the pageno
 * by default limit per page is 10
 */

public function cpSelectDsclimit($tablename,$value3, $value1=0,$value2=0) {
     /*** The SQL SELECT statement ***/
   /*
    * Prepare the select statement
    */
    $startvalue=($value1-1)*10;
    $sql="SELECT * FROM $tablename ";
      if($value2!=0)
      { $key1= key($value2);
           $sql.=" where $key1='$value2[$key1]'";
      }
      
   $sql.= " ORDER BY $value3 DESC LIMIT $startvalue, 10";
   
        $sth = $this->dbh->prepare($sql);
        $sth->execute();
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);
        return $result;
 
}
public function cpInsert($tablename,$in_param,$idfield=0) {
    /**
     * Insert Prep
     */
    $insertkeys= implode(',',array_keys($in_param));
    $insertprep=":".implode(',:',array_keys($in_param));
    //$insertvalues= implode('\',\'',array_values($in_param));
    $sql="INSERT INTO $tablename ($insertkeys) VALUES ($insertprep)";
    $sth = $this->dbh->prepare($sql);   
    $count=$sth->execute($in_param);
    if(!empty($idfield)){
    $lastid=$this->dbh->lastInsertId($idfield);
    return $lastid;}
    else{
    return $count;
    }
}
public function cpUpdate($tablename,$u_id,$up_param,$sec_id=0){
    /*
     * Prepare update
     */
    $up_fields="";
    if($up_param){
        foreach($up_param as $key=>$value){
            $up_fields.=$key."=:".$key.", ";
        }
    
    }
    $up_fields=rtrim($up_fields, ", ");
    //echo $up_fields;
    $id_key=  key($u_id);
    $id_value= $u_id[$id_key];
    $sql= "UPDATE $tablename SET $up_fields WHERE $id_key='$id_value'";
    if(!empty($sec_id)){
        $sec_id_key=key($sec_id);
        $sec_id_value=$sec_id[$sec_id_key];
        $sql.="AND $sec_id_key='$sec_id_value'"; 
    }
    //echo $sql;
    $sth = $this->dbh->prepare($sql);
    $count=$sth->execute($up_param);
    return $count;
 
}

public function cpDelete($tablename,$d_id) {
    #DELETE DATA
    $d_id_key= key($d_id);
    $d_value= $d_id[$d_id_key];
    $sql = "DELETE from $tablename where $d_id_key=:id";
    $preparedStatement = $this->dbh->prepare($sql);
    $deleted=$preparedStatement->execute(array(':id' => $d_value));
    return $deleted;
}

public function cpSelectGroup($tablename,$g_id){
    $sql="select * from $tablename ORDER BY $g_id";
     $sth = $this->dbh->prepare($sql);
        $sth->execute();
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);
        return $result;
}
//$table1,$table2,$table3,$commonId,$commonId=0
/*
 * select s.name as Student, c.name as Course 
from student s
inner join bridge b on s.id = b.sid
inner join course c on b.cid  = c.id 
order by s.name 
 */
public function cpJoin() {
    $sql="select packages.packageName, prices.id, prices.price, products.productName, products.cid, prices.productID, prices.areaID, prices.packageID "
            . "from prices"
            . " inner join packages on packages.packageID=prices.packageID"
            . " inner join products on prices.productID=products.productID where products.cid='9'";
    $sth=$this->dbh->prepare($sql);
    
    $sth->execute();
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);
        return $result;
}
public function autocomplete($tablename,$name,$request_term){
    $sql = "SELECT * "
	."FROM $tablename "
	."WHERE $name LIKE '%".$request_term."%' "; 
       $sth = $this->dbh->prepare($sql);
        $sth->execute();
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);
        return $result;
}

public function cpLargestNo($tablename,$columnname) {
    $sql= "SELECT MAX( $columnname ) AS max FROM $tablename";
    $sth = $this->dbh->prepare($sql);
    $sth->execute();
    $result = $sth->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}
/*
 * Find Range between two given numbers, dates etc
 */
public function cpRange($tablename,$columnname,$startvalue,$endvalue=0,$param=0) {
    $sql= "SELECT * FROM $tablename WHERE $columnname BETWEEN '$startvalue'";
         if($endvalue!=0){
           
        $sql.= " AND '$endvalue'";}
        if($param!=0)
        {
            $d_id_key= key($param);
            $d_value= $param[$d_id_key];
            $sql.= " AND $d_id_key>0";  
        }
        elseif ($param=0) {
        $d_id_key= key($param);
            $d_value= $param[$d_id_key];
            $sql.= " AND $d_id_key=0";  
        
        }
//print_r($sql);
    $sth = $this->dbh->prepare($sql);
    $sth->execute();
    $result = $sth->fetchAll(PDO::FETCH_ASSOC);
    
    return $result;
}

public function cpSelectGreater($tablename,$value1=0,$value2=0) {
     /*** The SQL SELECT statement ***/
   /*
    * Prepare the select statement
    */
    $sql="SELECT * FROM $tablename";
      if($value1!=0)
      { $key1= key($value1);
           $sql.=" where $key1>'$value1[$key1]'";
      }
       if($value1!=0 && $value2!=0) 
       {
           $key2= key($value2);
           $sql.=" AND $key2='$value2[$key2]'";
       }
   
        $sth = $this->dbh->prepare($sql);
        $sth->execute();
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);
        return $result;
 
}
public function cpCountRows($tablename){
    $sql="SELECT COUNT(*) FROM $tablename";
       $sth = $this->dbh->prepare($sql);
        $sth->execute();
        $result = $sth->fetchColumn();
        return $result;
    
}

}



