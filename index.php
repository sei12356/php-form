<?php 
//CSRF 冒頭に↓ セッションを使いますよという宣言 
session_start();

//XSS対策 JSなどで記述できないようにする
//https://qiita.com/mpyw/items/565b3670dd0c7f9162fa
function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

//バリデーション対策 外部読み込み
require 'validation.php';

$error = validation($_POST);

//flagで出し分け
$pageflag = 0;
if(!empty($_POST['btn_confirm']) && empty($error)){
    $pageflag = 1;
}elseif(!empty($_POST['btn_submit'])){
    $pageflag = 2;
}

//クリックジャッキング対策 ボタンやリンクなどを透明で見えない状態にして押させてくる
header('X-Frame-Options: DENY');

// echo '<pre>';
// var_dump($_SESSION);
// echo '</pre>';
?>
<html>
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" href="/common/css/form.css">
    </head>
    <body>
        <div class="ttl">
            <span class="ttl__EN">FORM</span>
            <h1 class="ttl__JP">自作フォーム</h1>
        </div>
        <?php if($pageflag === 1): ?>
            <?php if($_POST['csrf'] === $_SESSION['csrfToken']) : ?>
            <form class="form" method="POST" action="index.php">
                <dl class="form-item">
                    <dt class="form-item__ttl">氏名</dt>
                    <dd class="form-item__content"><?php echo h($_POST['your_name']);?></dd>
                </dl>
                <dl class="form-item">
                    <dt class="form-item__ttl">メールアドレス</dt>
                    <dd class="form-item__content"><?php echo h($_POST['email']);?></dd>
                </dl>
                <dl class="form-item">
                    <dt class="form-item__ttl">ホームページ</dt>
                    <dd class="form-item__content"><?php echo h($_POST['url']);?></dd>
                </dl>
                <dl class="form-item">
                    <dt class="form-item__ttl">性別</dt>
                    <dd class="form-item__content">
                        <?php 
                        if($_POST['gender'] === '0'){
                            echo '男性';
                        } elseif($_POST['gender'] === '1'){
                            echo '女性';
                        }
                        ?>
                    </dd>
                </dl>
                <dl class="form-item">
                    <dt class="form-item__ttl">年齢</dt>
                    <dd class="form-item__content">
                        <?php
                            if($_POST['age'] === '1'){
                                echo '~19歳';
                            }elseif($_POST['age'] === '2'){
                                echo '20歳~29歳';
                            }elseif($_POST['age'] === '3'){
                                echo '30歳~39歳';
                            }elseif($_POST['age'] === '4'){
                                echo '40歳~49歳';
                            }elseif($_POST['age'] === '5'){
                                echo '50歳~59歳';
                            }elseif($_POST['age'] === '6'){
                                echo '60歳~';
                            }
                        ?>
                    </dd>
                </dl>
                <dl class="form-item">
                    <dt class="form-item__ttl">お問い合わせの内容</dt>
                    <dd class="form-item__content"><?php echo h($_POST['contact'])?></textarea>
                    </dd>
                </dl>
                <div class="form-confirm">
                    <div class="form-confirm__btn">
                        <input type="submit" name="back" value="戻る">
                    </div>
                    <div class="form-confirm__btn">
                        <input type="submit" name="btn_submit" value="送信する">
                    </div>
                </div>
                
                <input type="hidden" name="your_name" value="<?php echo h($_POST['your_name']);?>">
                <input type="hidden" name="email" value="<?php echo h($_POST['email']);?>">
                <input type="hidden" name="url" value="<?php echo h($_POST['url']);?>">
                <input type="hidden" name="gender" value="<?php echo h($_POST['gender']);?>">
                <input type="hidden" name="age" value="<?php echo h($_POST['age']);?>">
                <input type="hidden" name="contact" value="<?php echo h($_POST['contact']);?>">
                <input type="hidden" name="csrf" value="<?php echo h($_POST['csrf']);?>">
            </form>
            <?php endif; ?>
        <?php endif; ?>

        <?php if($pageflag === 2): ?>
            <?php if($_POST['csrf'] === $_SESSION['csrfToken']): ?>
                <?php 
                require 'insert.php';
                insertContact($_POST);?>
                <p>送信が完了しました</p>
                <?php unset($_SESSION['csrfToken']);?>
            <?php endif; ?>
        <?php endif; ?>

        <?php if($pageflag === 0): ?>
            <?php
            if(!isset($_SESSION['csrfToken'])){
                $csrfToken = bin2hex(random_bytes(32)); 
                $_SESSION['csrfToken'] = $csrfToken;
            }
            $token = $_SESSION['csrfToken'];
            ?>
            <form class="form" method="POST" action="index.php">
                <?php if(!empty($_POST['btn_confirm'])&& !empty($error)) :?>
                <ul class="error">
                    <?php foreach($error as $value): ?>
                    <li class="error__list"><?php echo $value; ?></li>
                    <?php endforeach?>
                </ul>
                <?php endif ;?>
                <dl class="form-item">
                    <dt class="form-item__ttl" class="form-item__ttl"l>氏名</dt>
                    <dd class="form-item__content"><input type="text" name="your_name" value="<?php echo h($_POST['your_name']);?>"></dd>
                </dl>
                <dl class="form-item">
                    <dt class="form-item__ttl">メールアドレス</dt>
                    <dd class="form-item__content"><input type="email" name="email" value="<?php echo h($_POST['email']);?>"></dd>
                </dl>
                <dl class="form-item">
                    <dt class="form-item__ttl">ホームページ</dt>
                    <dd class="form-item__content">
                        <input type="url" name="url" value="<?php echo h($_POST['url']);?>">
                    </dd>
                </dl>
                <dl class="form-item">
                    <dt class="form-item__ttl">性別</dt>
                    <dd class="form-item__content form-item__radio">
                        <p><input type="radio" name="gender" value="0">男性</p>
                        <p><input type="radio" name="gender" value="1">女性</p>
                    </dd>
                </dl>
                <dl class="form-item">
                    <dt class="form-item__ttl">年齢</dt>
                    <dd class="form-item__content">
                        <select name="age" id="">
                            <option value="">選択してください</option>
                            <option value="1">~19歳</option>
                            <option value="2">20~29歳</option>
                            <option value="3">30~39歳</option>
                            <option value="4">40~49歳</option>
                            <option value="5">50~59歳</option>
                            <option value="6">60~歳</option>
                        </select>
                    </dd>
                </dl>
                <dl class="form-item">
                    <dt class="form-item__ttl">お問い合わせの内容</dt>
                    <dd class="form-item__content">
                        <textarea name="contact" value="<?php echo h($_POST['contact']);?>"></textarea>
                    </dd>
                </dl>
                <dl class="form-item">
                    <dt class="form-item__ttl">注意事項のチェック</dt>
                    <dd class="form-item__content form-item__checkbox">
                        <input type="checkbox" name="caution" value="1">
                        <p>注意事項にチェックする</p>
                    </dd>
                </dl>
                <div class="form-item__btn">
                    <input type="submit" name="btn_confirm" value="確認する">
                </div>
                <input type="hidden" name="csrf" value="<?php echo $token ?>">
            </form>
        <?php endif; ?>

        <footer class="footer">© 2020.sasabuchi.form</footer>
    </body>
</html>

<?php
/*
用語



*/
?>