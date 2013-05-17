<?php
require('staff.inc.php');
if(isset($_SESSION['_staff'])) {
    $one = db_query("select isadmin from ost_staff where username='".$_SESSION['_staff']['userID']."'");
    $row = db_fetch_row($one);
    if($row[0]!=1) {
        exit;
    }
}else {
    exit;
}
//$result=db_query('select category_id,dept_id,isenabled,title,description,answer,ost_kb_premade.no from ost_kb_premade,ost_premade_category where ost_kb_premade.premade_id = ost_premade_category.premade_id');
$result=db_query('select dept_id,isenabled,title,description,answer,no,premade_id from ost_kb_premade');
$text='';
header("Content-type:text/csv");
header("Content-Disposition:attachment;filename=".date("Y-m-d H:i:s").".csv");
header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
header('Expires:0');
header('Pragma:public');
//header("Content-type:application/vnd.ms-csv");
//header("Content-Disposition:filename=".date("Y-m-d H:i:s").".csv");
echo 'no,category_id,dept_id,isenabled,title,description,answer'."\n";
while ($row=db_fetch_row($result)) {
    if($row[6]>0) {
        $result_temp = db_query('select category_id from ost_premade_category where premade_id='.$row[6]);
        $temp_cate=array();
        while ($row_temp = db_fetch_row($result_temp)) {
            $temp_cate[] = $row_temp[0];
        }
        $row[2] = str_replace('"', '""', $row[2]);
        $row[3] = str_replace('"', '""', $row[3]);
        $row[4] = str_replace('"', '""', $row[4]);
        echo $row[5].','.implode('/', $temp_cate).','.$row[0].','.$row[1].',"'.$row[2].'","'.$row[3].'","'.$row[4]."\"\n";
    }
}
?>
