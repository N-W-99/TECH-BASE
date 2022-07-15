<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>
            mission_5-1
        </title>
    </head>
    <body>
        <?php
            $dsn = 'データベース名';
            $user = 'ユーザー名';
            $password = 'パスワード';
            $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

            $sql = "CREATE TABLE IF NOT EXISTS mission"
            ." ("
            . "id INT AUTO_INCREMENT PRIMARY KEY,"
            . "name char(32),"
            . "comment TEXT,"
            . "date TEXT,"
            . "pass TEXT"
            .");";
            $stmt = $pdo->query($sql);

            $error_name = "";
            $error_comment = "";
            $error_pass = "";
            $error_num = "";

            if(!empty($_POST["submit"])){
                $name = $_POST["name"];
                $comment = $_POST["comment"];
                $date = date("Y/m/d H:i:s");
                $pass = $_POST["passnew"];
                $editnum = $_POST["editnum"];

                if(!empty($name) && !empty($comment) && !empty($pass)){
                    if(empty($editnum)){
                        $sql = $pdo -> prepare("INSERT INTO mission (name, comment, date, pass) VALUES (:name, :comment, :date, :pass)");
                        $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                        $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                        $sql -> bindParam(':date', $date, PDO::PARAM_STR);
                        $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
                        $sql -> execute();
                    }else{
                        $id = $editnum;
                        $sql = 'UPDATE mission SET name=:name, comment=:comment, date=:date, pass=:pass WHERE id=:id';
                        $stmt = $pdo -> prepare($sql);
                        $stmt -> bindParam(':name', $name, PDO::PARAM_STR);
                        $stmt -> bindParam(':comment', $comment, PDO::PARAM_STR);
                        $stmt -> bindParam(':date', $date, PDO::PARAM_STR);
                        $stmt -> bindParam(':pass', $pass, PDO::PARAM_STR);
                        $stmt -> bindParam(':id', $id, PDO::PARAM_INT);
                        $stmt -> execute();
                    }
                }else{
                    if(empty($name)){
                        $error_name = "empty";
                    }
                    
                    if(empty($comment)){
                        $error_comment = "empty";
                    }
                    
                    if(empty($pass)){
                        $error_pass = "empty";
                    }
                }
            }else if(!empty($_POST["edit"])){
                $editnum = $_POST["editnum"];
                $pass = $_POST["passedit"];

                if(!empty($editnum) && !empty($pass)){
                    $id = $editnum;
                    $sql = 'SELECT * FROM mission WHERE id=:id';
                    $stmt = $pdo -> prepare($sql);
                    $stmt -> bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt -> execute();
                    $results = $stmt -> fetchAll();
                    foreach($results as $row){
                        if($row["pass"] == $pass){
                            $editname = $row["name"];
                            $editcomment = $row["comment"];
                            $editpass = $row["pass"];
                        }else{
                            $error_pass = "wrong";
                        }
                    }                  
                }else{
                    if(empty($editnum)){
                        $error_num = "empty";
                    }

                    if(empty($pass)){
                        $error_pass = "empty";
                    }
                }
            }else if(!empty($_POST["delete"])){
                $delnum = $_POST["delnum"];
                $pass = $_POST["passdel"];

                if(!empty($delnum) && !empty($pass)){
                    $id = $delnum;
                    $sql = 'SELECT * FROM mission WHERE id=:id';
                    $stmt = $pdo -> prepare($sql);
                    $stmt -> bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt -> execute();
                    $results = $stmt -> fetchAll();
                    foreach($results as $row){
                        if($row["pass"] == $pass){
                            $sql = 'DELETE from mission where id=:id';
                            $stmt = $pdo -> prepare($sql);
                            $stmt -> bindParam(':id', $id, PDO::PARAM_INT);
                            $stmt -> execute();
                        }else{
                            $error_pass = "wrong";
                        }
                    }
                }else{
                    if(empty($delnum)){
                        $error_num = "empty";
                    }

                    if(empty($pass)){
                        $error_pass = "empty";
                    }
                }
            }
        ?>

        <h2>【投稿フォーム】</h2>
        <form action="" method="post">
            <dl>
                <dt>名前</dt>
                <dd><input type="name" name="name" placeholder="Name" value="<?php if(!empty($editname)){echo $editname;} ?>"></dd>
                <dt>コメント</dt>
                <dd><input typw="text" name="comment" placeholder="Comment" value="<?php if(!empty($editcomment)){echo $editcomment;} ?>"></dd>
                <dt>パスワード</dt>
                <dd><input type="password" name="passnew" placeholder="Password" value="<?php if(!empty($editpass)){echo $editpass;} ?>"></dd>
                <dd><input type="hidden" name="editnum" value="<?php if(!empty($editnum)){echo $editnum;} ?>"></dd>
            <dl>
            <input type="submit" name="submit" value="送信">
        </form>

        <h2>【削除フォーム】</h2>
        <form action="" method="post">
            <dl>
                <dt>削除番号</dt>
                <dd><input type="number" name="delnum" placeholder="Delete Number"></dd>
                <dt>パスワード</dt>
                <dd><input type="password" name="passdel" placeholder="Password"></dd>
            </dl>
            <input type="submit" name="delete" value="削除">
        </form>

        <h2>【編集フォーム】</h2>
        <form action="" method="post">
            <dl>
                <dt>編集番号</dt>
                <dd><input type="number" name="editnum" placeholder="Edit Number"></dd>
                <dt>パスワード</dt>
                <dd><input type="password" name="passedit" placeholder="Password"></dd>
            </dl>
            <input type="submit" name="edit" value="編集">
        </form>

        <hr>

        <?php
            if($error_name == "empty")
                echo "名前の入力がありません。<br>";

            if($error_comment == "empty")
                echo "コメントの入力がありません。<br>";

            if($error_pass == "empty")
                echo "パスワードの入力がありません。<br>";

            if($error_num == "empty")
                echo "番号の入力がありません。<br>";

            if($error_pass == "wrong")
                echo "パスワードが間違っています。<br>";
        ?>

        <hr>

        <h2>【投稿一覧】</h2>

        <?php
            $sql = 'SELECT * FROM mission';
            $stmt = $pdo -> query($sql);
            $results = $stmt -> fetchAll();
            foreach($results as $row){
                echo $row['id']. ' , ';
                echo $row['name']. ' , ';
                echo $row['comment']. ' , ';
                echo $row['date']. '<br>';
            }
        ?>
    </body>
</html>
