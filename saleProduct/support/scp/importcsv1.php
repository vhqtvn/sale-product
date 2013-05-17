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
////application/octet-stream
//if (($_FILES["file"]["type"] == "application/octet-stream" || $_FILES["file"]["type"] == "text/comma-separated-values" || $_FILES["file"]["type"] == "application/vnd.ms-excel")&& ($_FILES["file"]["size"] < 200000)) {
if ($_FILES["file"]["error"] > 0) {
    $msg= "Return Code: " . $_FILES["file"]["error"];
    alert($msg);
}
else {
    if (file_exists("csv/" . $_FILES["file"]["name"])) {
        unlink("csv/" . $_FILES["file"]["name"]);
    }

    move_uploaded_file($_FILES["file"]["tmp_name"],"csv/" . $_FILES["file"]["name"]);
    
    $file="csv/" . $_FILES["file"]["name"];
    if($handle = fopen ($file,'r')) {
        $num=0;
        try {
            //第一行数据
            if($first = fgetcsv($handle)) {
                foreach($first as $key=>$value) {
                    unset ($first[$key]);
                    $first[$value]=$key;
                }
            }else {
                alert('data error');
                exit;
            }
            while ($data = fgetcsv($handle)) {
                $kb[] = $data;
                $idid=0;
                //查看有没有这条记录
                $answer=db_fetch_array(db_query("SELECT * FROM ost_kb_premade WHERE no='{$kb[$num][$first['no']]}'"));
                if($answer['premade_id']>0) {
                    $sql=" SET updated=NOW(),".
                            "dept_id='".$kb[$num][$first['dept_id']].
                            "',isenabled='".$kb[$num][$first['isenabled']].
                            "', title='".addslashes($kb[$num][$first['title']]).
                            "', description='".addslashes($kb[$num][$first['description']]).
                            "', answer='".addslashes($kb[$num][$first['answer']])."'";
                    @db_query('update ost_kb_premade '.$sql.',created=NOW() where no=\''.$kb[$num][$first['no']].'\'');
                    $idid = $answer['premade_id'];
                }else {
                    $sql=" SET updated=NOW(),".
                            "dept_id='".$kb[$num][$first['dept_id']].
                            "',no='".$kb[$num][$first['no']].
                            "',isenabled='".$kb[$num][$first['isenabled']].
                            "', title='".addslashes($kb[$num][$first['title']]).
                            "', description='".addslashes($kb[$num][$first['description']]).
                            "', answer='".addslashes($kb[$num][$first['answer']])."'";
                    @db_query('insert into ost_kb_premade '.$sql.',created=NOW()');
                    $idid=db_insert_id();
                }

                if($idid>0) {
                    $category_ids=explode('/', $kb[$num][$first['category_id']]);
                    db_query('delete FROM ost_premade_category WHERE premade_id='.$idid);
                    foreach ($category_ids as $category_id) {
                        @db_query('INSERT INTO ost_premade_category SET premade_id='.$idid.' , category_id='.$category_id);
                    }
                }

                $num+=1;
            }
            alert($num.' data imported');
            fclose($handle);

            $result = @unlink ($file);//ɾ���ļ�
        } catch (Exception $e) {
            $result = @unlink ($file);
            alert('data error');
        }
    }else {
        alert('cant read file');
    }
}
//}
//else {
//    $msg= "Invalid file";
//    alert($msg);
//}

function alert($msg) {
    echo   "<script>alert('$msg')</script>";
}
?>