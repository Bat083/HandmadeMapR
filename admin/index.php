<?php
$page = "index";
include "header.php";


// hide marker on map
if($task == "hide") {
  $place_id = htmlspecialchars($_GET['place_id']);
  mysql_query("UPDATE $table SET approved=0 WHERE id='$place_id'") or die(mysql_error());  
  header("Location: index.php?view=$view&search=$search&p=$p&table=$table");
  exit;
}

// show marker on map
if($task == "approve") {
  $place_id = htmlspecialchars($_GET['place_id']);
  mysql_query("UPDATE $table SET approved=1 WHERE id='$place_id'") or die(mysql_error());  
  header("Location: index.php?view=$view&search=$search&p=$p&table=$table");
  exit;
}

// completely delete marker from map
if($task == "delete") {
  $place_id = htmlspecialchars($_GET['place_id']);
  mysql_query("DELETE FROM $table WHERE id='$place_id'") or die(mysql_error());  
  header("Location: index.php?view=$view&search=$search&p=$p&table=$table");
  exit;
}

// paginate
$items_per_page = 15;
$page_start = ($p-1) * $items_per_page;
$page_end = $page_start + $items_per_page;

// get results

if($view == "approved") {
  $places = mysql_query("SELECT * FROM $table WHERE approved='1' ORDER BY title LIMIT $page_start, $items_per_page");
  $total = $total_approved;
} else if($view == "rejected") {
  $places = mysql_query("SELECT * FROM $table WHERE approved='0' ORDER BY title LIMIT $page_start, $items_per_page");
  $total = $total_rejected;
} else if($view == "pending") {
  $places = mysql_query("SELECT * FROM $table WHERE approved IS null ORDER BY id DESC LIMIT $page_start, $items_per_page");
  $total = $total_pending;

} else if($view == "") {
  $places = mysql_query("SELECT * FROM $table ORDER BY title LIMIT $page_start, $items_per_page");
  $total = $total_all;
}
if($search != "") {
  $places = mysql_query("SELECT * FROM $table WHERE title LIKE '%$search%' ORDER BY title LIMIT $page_start, $items_per_page");
  $total = mysql_num_rows(mysql_query("SELECT id FROM $table WHERE title LIKE '%$search%'")); 
}

echo $admin_head;
?>


<div id="admin">
  <h3>
    <? if($total > $items_per_page) { ?>
      <?=$page_start+1?>-<? if($page_end > $total) { echo $total; } else { echo $page_end; } ?>
      de <?=$total?> marcadores
    <? } else { ?>
      <?=$total?> marcadores
    <? } ?>
  </h3>
  <ul>
    <?
      while($place = mysql_fetch_assoc($places)) {
        $place[uri] = str_replace("http://", "", $place[uri]);
        $place[uri] = str_replace("https://", "", $place[uri]);
        $place[uri] = str_replace("www.", "", $place[uri]);
        echo "
          <li>
            <div class='options'>
              <a class='btn btn-small' href='edit.php?place_id=$place[id]&view=$view&search=$search&p=$p&table=$table'>Edit</a>
              ";
              if($place[approved] == 1) {
                echo "
                  <a class='btn btn-small btn-success disabled'>Approve</a>
                  <a class='btn btn-small btn-inverse' href='index.php?task=hide&place_id=$place[id]&view=$view&search=$search&p=$p&table=$table'>Reject</a>
                ";
              } else if(is_null($place[approved])) {
                echo "
                  <a class='btn btn-small btn-success' href='index.php?task=approve&place_id=$place[id]&view=$view&search=$search&p=$p&table=$table'>Approve</a>
                  <a class='btn btn-small btn-inverse' href='index.php?task=hide&place_id=$place[id]&view=$view&search=$search&p=$p&table=$table'>Reject</a>
                ";
              } else if($place[approved] == 0) {
                echo "
                  <a class='btn btn-small btn-success' href='index.php?task=approve&place_id=$place[id]&view=$view&search=$search&p=$p&table=$table'>Approve</a>
                  <a class='btn btn-small btn-inverse disabled'>Reject</a>
                ";
              }
              $title_utf8 = utf8_encode($place[title]);
              $subtype_utf8 = utf8_encode($place[subtype]);
              echo "
              <a class='btn btn-small btn-danger' href='index.php?task=delete&place_id=$place[id]&view=$view&search=$search&p=$p&table=$table'>Delete</a>
            </div>
            <div class='place_info'>
              <a href='http://$place[uri]' target='_blank'>
                 $title_utf8
                <span class='url'>
                  $place[uri]
                </span>               
                <span class='url'>
                  $place[type]
                </span>
                <span class='url'>
                  $subtype_utf8
                </span>
              </a>
            </div>
          </li>
        ";
      }
    ?>
  </ul>
  
  <? if($p > 1 || $total >= $items_per_page) { ?>
    <ul class="pager">
      <? if($p > 1) { ?>
        <li class="previous">
          <a href="index.php?view=<?=$view?>&search=<?=$search?>&p=<? echo $p-1; ?>&table=<?=$table?>">&larr; Anterior</a>
        </li>
      <? } ?>
      <? if($total >= $items_per_page * $p) { ?>
        <li class="next">
          <a href="index.php?view=<?=$view?>&search=<?=$search?>&p=<? echo $p+1; ?>&table=<?=$table?>">Siguiente &rarr;</a>
        </li>
      <? } ?>
    </ul>
  <? } ?>

</div>


<? echo $admin_foot ?>