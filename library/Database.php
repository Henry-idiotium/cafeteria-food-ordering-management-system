<?php

    /**
    *
    */
    class Database
    {
      /**
       * Declare connecting var
       * @var [type]
       */
        //Declare connecting var
        public $link;
        //

        //Contructor
        public function __construct()
        {
            $this->link = mysqli_connect("localhost","root","","Canteen") or die ();
            mysqli_set_charset($this->link,"utf8");
        }



        /**
         * insert description
         * @param  $table
         * @param  array  $data
         * @return integer
         */

        //Insert function
        public function insert($table, array $data)
        {
            //code
            $sql = "INSERT INTO {$table} ";
            $columns = implode(',', array_keys($data));
            $values  = "";
            $sql .= '(' . $columns . ')';
            foreach($data as $field => $value) {
                if(is_string($value)) {
                  $values .= "'". mysqli_real_escape_string($this->link,$value) ."',";
                }
                else {
                  $values .= mysqli_real_escape_string($this->link,$value) . ',';
                }
            }
            $values = substr($values, 0, -1);
            $sql .= " VALUES (" . $values . ')';
            // _debug($sql);die;
            mysqli_query($this->link, $sql) or die("Wrong query ----" .mysqli_error($this->link));
            return mysqli_insert_id($this->link);
        }
        //


        //Update function
        public function update($table, array $data, array $conditions)
        {
            $sql = "UPDATE {$table}";

            $set = " SET ";

            $where = " WHERE ";

            foreach($data as $field => $value) {
                if(is_string($value)) {
                    $set .= $field .'='.'\''. mysqli_real_escape_string($this->link, xss_clean($value)) .'\',';
                } else {
                    $set .= $field .'='. mysqli_real_escape_string($this->link, xss_clean($value)) . ',';
                }
            }

            $set = substr($set, 0, -1);


            foreach($conditions as $field => $value) {
                if(is_string($value)) {
                    $where .= $field .'='.'\''. mysqli_real_escape_string($this->link, xss_clean($value)) .'\' AND ';
                } else {
                    $where .= $field .'='. mysqli_real_escape_string($this->link, xss_clean($value)) . " AND " ;
                }
            }

            $where = substr($where, 0, -5);

            $sql .= $set . $where;
            // _debug($sql);die;
            mysqli_query($this->link, $sql) or die( "Wrong query ----" .mysqli_error($this->link));
            return mysqli_affected_rows($this->link);
        }
        //



        public function updateview($sql)
        {
            $result = mysqli_query($this->link,$sql)  or die ("Wrong query ----" .mysqli_error($this->link));
            return mysqli_affected_rows($this->link);

        }
        public function countTable($table,$id,$role="")
        {
            $sql = "SELECT $id FROM  {$table} ".$role;
            $result = mysqli_query($this->link, $sql) or die("Wrong query ----" .mysqli_error($this->link));
            $num = mysqli_num_rows($result);
            return $num;
        }


        /**
         * Delete description
         * @param  $table      [description]
         * @param  array  $conditions [description]
         * @return integer             [description]
         */
        public function delete ($table , $idname,  $id )
        {
            $sql = "DELETE FROM {$table} WHERE $idname = $id ";

            mysqli_query($this->link,$sql) or die ("Wrong query ----" .mysqli_error($this->link));
            return mysqli_affected_rows($this->link);
        }
        //
        public function dele($table , $sql)
        {
            mysqli_query($this->link,$sql) or die ("Wrong query ----" .mysqli_error($this->link));
            return mysqli_affected_rows($this->link);
        }




        /**
         * delete array
         */

        public function deletewhere($table,$data = array())
        {
            foreach ($data as $id)
            {
                $id = intval($id);
                $sql = "DELETE FROM {$table} WHERE id = $id ";
                mysqli_query($this->link,$sql) or die (" Wrong query delete   --- " .mysqli_error($this->link));
            }
            return true;
        }

        public function fetchsql( $sql )
        {
            $result = mysqli_query($this->link,$sql) or die("Wrong query " .mysqli_error($this->link));
            $data = [];
            if( $result)
            {
                while ($num = mysqli_fetch_assoc($result))
                {
                    $data[] = $num;
                }
            }
            return $data;
        }

        public function fetchID($table , $idname, $id )
        {
            $sql = "SELECT * FROM {$table} WHERE $idname = $id ";
            $result = mysqli_query($this->link,$sql) or die("Wrong query fetchID " .mysqli_error($this->link));
            return mysqli_fetch_assoc($result);
        }

        public function fetchOne($table , $query)
        {
            $sql  = "SELECT * FROM {$table} WHERE ";
            $sql .= $query;
            $sql .= "LIMIT 1";
            $result = mysqli_query($this->link,$sql) or die("Wrong query fetchOne " .mysqli_error($this->link));
            return mysqli_fetch_assoc($result);
        }

        public function deletesql ($table ,  $sql )
        {
            $sql = "DELETE FROM {$table} WHERE " .$sql;
            // _debug($sql);die;
            mysqli_query($this->link,$sql) or die (" Wrong query delete   --- " .mysqli_error($this->link));
            return mysqli_affected_rows($this->link);
        }



        public function fetchAll($table)
        {
            $sql = "SELECT * FROM {$table} WHERE 1" ;
            $result = mysqli_query($this->link,$sql) or die("Wrong query fetchAll " .mysqli_error($this->link));
            $data = [];
            if( $result)
            {
                while ($num = mysqli_fetch_assoc($result))
                {
                    $data[] = $num;
                }
            }
            return $data;
        }

        public  function fetchJone($table,$sql ,$page = 0,$row ,$pagi = false, $id, $role)
        {

            $data = [];
            // _debug($sql);die;
            if ($pagi == true )
            {
                $total = $this->countTable($table, $id, $role);
                $pageNo = ceil($total / $row);
                $start = ($page - 1 ) * $row ;
                $sql .= " LIMIT $start,$row";
                $data = [ "page" => $pageNo];
                $result = mysqli_query($this->link,$sql) or die("Wrong query fetchJone ---- " .mysqli_error($this->link));
            }
            else
            {
                $result = mysqli_query($this->link,$sql) or die("Wrong query fetchJone ---- " .mysqli_error($this->link));
            }

            if( $result)
            {
                while ($num = mysqli_fetch_assoc($result))
                {
                    $data[] = $num;
                }
            }
            // _debug($data);

            return $data;
        }



        public function total($sql)
        {
            $result = mysqli_query($this->link  , $sql);
            $tien = mysqli_fetch_assoc($result);
            return $tien;
        }
    }

?>
