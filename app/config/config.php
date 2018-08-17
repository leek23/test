<?php
class mysql {
   public function get() {
     $con = array(
            'host'=>'localhost',
            'db'=>'test',
            'user'=>'test',
            'password'=>'test'
     );

       return $con;
    }
}

