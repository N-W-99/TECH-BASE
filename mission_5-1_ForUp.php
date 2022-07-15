<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>
            mission_5-1
        </title>
    </head>
    <body>
        <?php
            //データベース接続
            $dsn = 'データベース名';
            $user = 'ユーザー名';
            $password = 'パスワード';
            $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

            //テーブルの作成
            $sql = "CREATE TABLE IF NOT EXISTS mission"
            ." ("
            . "id INT AUTO_INCREMENT PRIMARY KEY,"
            . "name char(32),"
            . "comment TEXT,"
            . "date TEXT,"
            . "pass TEXT"
            .");";
            $stmt = $pdo->query($sql);

            //エラー判定の準備
            $error_name = "";
            $error_comment = "";
            $error_pass = "";
            $error_num = "";

            if(!empty($_POST["submit"])){   //送信ボタンが押されたとき
                //テーブルに記入する各変数と編集分岐のための変数を準備
                $name = $_POST["name"];
                $comment = $_POST["comment"];
                $date = date("Y/m/d H:i:s");
                $pass = $_POST["passnew"];
                $editnum = $_POST["editnum"];

                if(!empty($name) && !empty($comment) && !empty($pass)){ //名前、コメント、パスワードの入力が全てあるとき
                    if(empty($editnum)){    //編集番号が空のとき
                        //データベースに入力
                        $sql = $pdo -> prepare("INSERT INTO mission (name, comment, date, pass) VALUES (:name, :comment, :date, :pass)");
                        $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                        $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                        $sql -> bindParam(':date', $date, PDO::PARAM_STR);
                        $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
                        $sql -> execute();
                    }else{  //編集番号が存在するとき
                        //編集番号をidに代入
                        $id = $editnum;
                        //各データを更新
                        $sql = 'UPDATE mission SET name=:name, comment=:comment, date=:date, pass=:pass WHERE id=:id';
                        $stmt = $pdo -> prepare($sql);
                        $stmt -> bindParam(':name', $name, PDO::PARAM_STR);
                        $stmt -> bindParam(':comment', $comment, PDO::PARAM_STR);
                        $stmt -> bindParam(':date', $date, PDO::PARAM_STR);
                        $stmt -> bindParam(':pass', $pass, PDO::PARAM_STR);
                        $stmt -> bindParam(':id', $id, PDO::PARAM_INT);
                        $stmt -> execute();
                    }
                }else{  //名前、コメント、パスワードの入力が全て揃っていないとき
                    if(empty($name)){   //名前の入力がないとき
                        //名前のエラー判定に空であると記入
                        $error_name = "empty";
                    }
                    
                    if(empty($comment)){    //コメントの入力がないとき
                        //コメントのエラー判定に空であると記入
                        $error_comment = "empty";
                    }
                    
                    if(empty($pass)){   //パスワードの入力がないとき
                        //パスワードのエラー判定に空であると記入
                        $error_pass = "empty";
                    }
                }
            }else if(!empty($_POST["edit"])){   //編集ボタンが押されたとき
                //編集可能か判定するために必要な変数を準備
                $editnum = $_POST["editnum"];
                $pass = $_POST["passedit"];

                if(!empty($editnum) && !empty($pass)){  //編集番号、パスワードの入力が全てあるとき
                    //編集番号をidに代入
                    $id = $editnum;
                    //入力された編集番号と一致するidを持つデータセットを検索
                    $sql = 'SELECT * FROM mission WHERE id=:id';
                    $stmt = $pdo -> prepare($sql);
                    $stmt -> bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt -> execute();
                    $results = $stmt -> fetchAll();
                    foreach($results as $row){
                        if($row["pass"] == $pass){  //データベースに登録されたパスワードと入力されたパスワードが一致するとき
                            //フォームに表示するための変数にデータベースに登録されていたデータをそれぞれ代入
                            $editname = $row["name"];
                            $editcomment = $row["comment"];
                            $editpass = $row["pass"];
                        }else{  //データベースに登録されたパスワードと入力されたパスワードが異なるとき
                            //パスワードのエラー判定に間違っていると記入
                            $error_pass = "wrong";
                        }
                    }                  
                }else{  //編集番号、パスワードの入力が全て揃っていないとき
                    if(empty($editnum)){    //編集番号の入力がないとき
                        //番号のエラー判定に空であると記入
                        $error_num = "empty";
                    }

                    if(empty($pass)){   //パスワードの入力がないとき
                        //パスワードのエラー判定に空であると記入
                        $error_pass = "empty";
                    }
                }
            }else if(!empty($_POST["delete"])){ //削除ボタンが押されたとき
                //削除可能か判定するために必要な変数を準備
                $delnum = $_POST["delnum"];
                $pass = $_POST["passdel"];

                if(!empty($delnum) && !empty($pass)){   //削除番号、パスワードの入力が全てあるとき
                    //削除番号をidに代入
                    $id = $delnum;
                    //入力された削除番号と一致するidを持つデータセットを検索
                    $sql = 'SELECT * FROM mission WHERE id=:id';
                    $stmt = $pdo -> prepare($sql);
                    $stmt -> bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt -> execute();
                    $results = $stmt -> fetchAll();
                    foreach($results as $row){
                        if($row["pass"] == $pass){  //データベースに登録されたパスワードと入力されたパスワードが一致するとき
                            //ヒットしたデータセットを削除する
                            $sql = 'DELETE from mission where id=:id';
                            $stmt = $pdo -> prepare($sql);
                            $stmt -> bindParam(':id', $id, PDO::PARAM_INT);
                            $stmt -> execute();
                        }else{  //データベースに登録されたパスワードと入力されたパスワードが異なるとき
                            //パスワードのエラー判定に間違っていると記入
                            $error_pass = "wrong";
                        }
                    }
                }else{  //削除番号、パスワードの入力が全て揃っていないとき
                    if(empty($delnum)){ //削除番号の入力がないとき
                        //番号のエラー判定に空であると記入
                        $error_num = "empty";
                    }

                    if(empty($pass)){   //パスワードの入力がないとき
                        //パスワードのエラー判定に空であると記入
                        $error_pass = "empty";
                    }
                }
            }
        ?>

        <h2>【投稿フォーム】</h2>
        <form action="" method="post">
            <dl>
                <dt>名前</dt>   <!--編集のために用意した各変数にデータが入っていれば初期表示する-->
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
            //エラーを以下のように表示する
            if($error_name == "empty")  //名前のエラー判定に空と記入されているとき
                echo "名前の入力がありません。<br>";

            if($error_comment == "empty")   //コメントのエラー判定に空と記入されているとき
                echo "コメントの入力がありません。<br>";

            if($error_pass == "empty")  //パスワードのエラー判定に空と記入されているとき
                echo "パスワードの入力がありません。<br>";

            if($error_num == "empty")   //番号のエラー判定に空と記入されているとき
                echo "番号の入力がありません。<br>";

            if($error_pass == "wrong")  //パスワードのエラー判定に間違っていると記入されているとき
                echo "パスワードが間違っています。<br>";
        ?>

        <hr>

        <h2>【投稿一覧】</h2>

        <?php
            //データベースに記録されているすべてのデータを表示する
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