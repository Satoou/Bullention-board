<?php
	//初期値設定
	$edname = "名前";
	$edcomment = "コメント";
	$ednum = "";
	$moedipas = null;
	
	//データベースへの接続
	$dsn = 'データベース名';
	$user = 'ユーザー名';
	$password = 'パスワード';
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
		
	//データベース内にテーブルtb51aを作成する
	$sql = "CREATE TABLE IF NOT EXISTS tb51a"
	."("
	."id INT AUTO_INCREMENT PRIMARY KEY,"
	."name char(32),"
	."comment TEXT,"
	."pass char(32),"
	."date char(32)"
	.");";
	$stmt = $pdo->query($sql);

	//編集パスと編集に入力されたとき
	if(isset($_POST['edit'])&&isset($_POST['edpas'])){

		//POST送信で入力されたものを受け取る
		$editpas = $_POST['edpas'];
		$editnum = $_POST['edit'];
		
		//データベースからselectを使ってデータを抽出
		$sql = 'SELECT * FROM tb51a';
		$stmt = $pdo->query($sql);
		$results = $stmt->fetchAll();

	
		//foreach文で配列の数だけループさせパスを抜き出す
		foreach($results as $row){

			//削除番号と行番号が一致していればpasを抜き出す
			if($row['id'] == $editnum){
				$moedipas = $row['pass'];
			} 
		}
		

		//pasが一致していれば編集
		if($editpas == $moedipas){
				
			echo "編集モードになりました、入力フォームに編集内容を記入してください。<br/> 編集を送信する際にはパスワードを入力しなくても大丈夫です。またこの時パスワードは変更されません。";
	
				//POST送信
			$editnum = $_POST['edit'];

			//データベースからselectを使ってデータを抽出
			$sql = 'SELECT * FROM tb51a';
			$stmt = $pdo->query($sql);
			$results = $stmt->fetchAll();


			//foreach文で配列の数だけループさせる
			foreach($results as $row){
				
				//編集番号と行番号が一致していればコメントと名前を取得
				if($row['id'] == $editnum){
					$edname = $row['name'];
					$edcomment = $row['comment'];
					$ednum = $row['id'];
				}
			}
		}
	}
?>


<!DOCTYPE html>
<html>
	<head>
	<meta charset="utf-8">
	<title>mission_5-1a</title>
	</head>
	<body>
		<hr>
		入力フォーム<br>
	<form action="mission_5-1a.php" method="post" >
		名前：<input type="text" name="name" value="<?php echo $edname;?>"><br>
		コメント：<input type="text" name="comment" value="<?php echo $edcomment;?>"><br>
		<input type="hidden" name="num" value="<?php echo $ednum;?>">
		パスワード：<input type="text" name="inpas" value="パスワードを入力"><br>
		<input type="submit" value="送信"><br><br>
	</form>

		削除番号指定用フォーム<br>
	<form action="mission_5-1a.php" method="post" >
		削除対象番号:<input type="number" name="delete" value="削除対象番号"><br>
		パスワード：<input type="text" name="delpas" value="パスワードを入力"><br>
		<input type="submit" value="削除"><br><br>
	</form>

		編集番号指定用フォーム<br>
	<form action="mission_5-1a.php" method="post" >
		編集対象番号：<input type="number" name="edit" value="編集番号"><br>
		パスワード：<input type="text" name="edpas" value="パスワードを入力"><br>
		<input type="submit" value="編集"><br><hr>
	</form>
	</body>
</html>



<?php

	//入力フォームに入力されたとき
	if(isset($_POST['name'])&&isset($_POST['comment'])&&isset($_POST['inpas'])){

		//POST送信
		$comment = $_POST['comment'];
		$name = $_POST['name'];
		$inpas = $_POST['inpas'];

		//日時の取得
		$date = date("Y/m/d H:i:s");

		//編集番号作業用フォームに番号が入力されたとき(入力モード)
		if($_POST['num'] == ""){
			
			//日時の取得
			$date = date("Y/m/d H:i:s");

			//データベースに書き込み
			$name = $_POST['name'];
			$comment = $_POST['comment'];
			$pass = $_POST['inpas'];
			$sql = $pdo -> prepare("INSERT INTO tb51a (name, comment, pass, date) VALUES (:name, :comment, :pass, :date)");
			$sql -> bindParam(':name', $name, PDO::PARAM_STR);
			$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
			$sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
			$sql -> bindParam(':date', $date, PDO::PARAM_STR);
			$sql -> execute();
		

		//編集番号作業用フォームに番号が入力されたとき
		}else{
			
			//編集番号指定用フォームに入力された番号を取得
			$editnum = $_POST['num'];
			//ＰＯＳＴ送信
			$comment = $_POST['comment'];	
			$name = $_POST['name'];

			//updateによってデータを編集
			$id = $_POST['num']; 
			$name = $_POST['name'];
			$comment = $_POST['comment'];
			$sql = 'update tb51a set name=:name,comment=:comment where id=:id';
			$stmt = $pdo->prepare($sql);
			$stmt->bindParam(':name', $name, PDO::PARAM_STR);
			$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
			$stmt->execute();
		}

	//削除番号指定用フォームに入力されたとき
	}else if(isset($_POST['delete'])&&(isset($_POST['delpas']))){
		
		$sql = 'SELECT * FROM tb51a';
		$stmt = $pdo -> query($sql);
		$results = $stmt -> fetchAll();

		foreach($results as $row){
			
			//削除番号とパスワードがあっている場合のみ処理
			if($_POST['delete'] == $row['id'] and $_POST['delpas'] == $row['pass']){
				$id = $_POST['delete'];
				$sql = 'delete from tb51a where id=:id';
				$stmt = $pdo -> prepare($sql);
				$stmt -> bindParam(':id', $id, PDO::PARAM_INT);
				$stmt -> execute();
			}
		}
	
	//編集番号指定用フォームに入力されたとき
	}else if(isset($_POST['edit'])){
	
		
		$sql = 'SELECT * FROM tb51a';
		$stmt = $pdo -> query($sql);
		$results = $stmt -> fetchAll();

		foreach($results as $row){
			
			//削除番号があっている場合のみ処理
			if($editnum == $row['id']){
				$edname = $row['name'];
				$edcomment = $row['comment'];
			}
		}
	}
?>




<?php
	
	//表示
	$sql = 'SELECT * FROM tb51a';
	$stmt = $pdo -> query($sql);
	$results = $stmt -> fetchAll();
	
	foreach($results as $row){
		//echo で表示
		echo $row['id'].',';
		echo $row['name'].',';
		echo $row['comment'].',';
		echo $row['date'].'<br>';
	}
?>