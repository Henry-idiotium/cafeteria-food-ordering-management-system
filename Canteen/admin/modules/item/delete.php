<?php
  require_once __dir__. "/../../autoload/autoload.php";
  $id=intval(getInput("id"));
  $idDelete = $db->delete("tblitem", "itemId", $id);
  if ($idDelete>0) {
    $_SESSION["success"]="<i class='fas fa-trash'></i> Delete category successfully";
    redirectCate("item");
  }
  else{
    $_SESSION["error"]="Delete failed";
  }
?>
