<?php

require 'vendor/autoload.php';
// composer require phpoffice/phpspreadsheet
use PhpOffice\PhpSpreadsheet\IOFactory;

if (!class_exists("UpdateWorldIds")) {
    class UpdateWorldIds
    {
        function __construct() {
            $this->userName = isset($_POST['username']) ? $_POST['username'] : 'nnerna_admin';
            $this->passWord = isset($_POST['password']) ? $_POST['password'] : 'NNERNAWorks53!';
            $this->rootServer = isset($_POST['rootserver']) ? $_POST['rootserver'] : 'https://nnerna.charlestonna.org/main_server';
        }

        public function UpdateWorldIdsFunc()
        {
            if (isset($_FILES['spreadsheet'])) {
                $errors = array();
                $file_name = $_FILES['spreadsheet']['name'];
                $file_size = $_FILES['spreadsheet']['size'];
                $file_tmp = $_FILES['spreadsheet']['tmp_name'];
                $file_ext = strtolower(end(explode('.',$_FILES['spreadsheet']['name'])));

                // Only allow certain file extensions
                $extensions = array("csv","xls","txt","xlsx","tsv","ods");

                if (in_array($file_ext,$extensions) === false) {
                    $errors[] = "extension not allowed, please choose a CSV or EXCEL file.";
                }

                // Limit file sizes to under 10MB
                if ($file_size > 10000000) {
                    $errors[] = "File size must be under 10 MB";
                }

                if (!empty($errors)) {
                    print_r($errors);
                    exit;
                }

                // We will be using the tmp file and not moving or writing it anywhere else.
                // PhpSpreadsheet is nice as it will autodetect the filetype, csv, tsv, xls, xlsx, etc.
                $inputFileType = IOFactory::identify($file_tmp);
                $reader = IOFactory::createReader($inputFileType);
                $spreadsheet = $reader->load($file_tmp);
                $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

                // Replace array keys with values from first array (the csv or xls header), then shift off that first array.
                array_walk($sheetData, function(&$a) use ($sheetData) {
                    $header = array_shift($sheetData);
                    $a = array_combine($header, $a);
                });
                array_shift($sheetData);
            }

            ?>
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="utf-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <title>Bootstrap Template</title>
                <link href="css/bootstrap.min.css" rel="stylesheet">
                <link href="css/update-worldids.css" rel="stylesheet">
            </head>
            <body>
            <?php
            if (!isset($_POST['username']) && !isset($_FILES['spreadsheet'])) {
                ?>
            <form>
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
                <script src="js/bootstrap.min.js"></script>

                <div id="signin" class="container">
                    <form class="form-signin" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                        <h2 class="form-signin-heading">Update World IDs</h2>
                        <div id="no-auth-message"></div>
                        <label for="inputRootServer" class="sr-only">Root Server</label>
                        <input name="rootserver" type="rootserver" id="inputRootServer" class="form-control" placeholder="Root Server" required autofocus>
                        <label for="inputUsername" class="sr-only">Username</label>
                        <input name="username" type="username" id="inputUsername" class="form-control" placeholder="Username" required autofocus>
                        <label for="inputPassword" class="sr-only">Password</label>
                        <input name="password" type="password" id="inputPassword" class="form-control" placeholder="Password" required>
                        <button class="btn btn-lg btn-primary btn-block" type="submit">Authenticate</button>
                    </form>
                </div>
                <?php
            }
            if ($this->auth_bmlt($this->userName, $this->passWord) || isset($_FILES['spreadsheet'])) {
                ?>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <button class="btn btn-outline-secondary" type="submit">Submit</button>
                        </div>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" name="spreadsheet" id="inputSpreadsheet">
                            <label class="custom-file-label" for="inputSpreadsheet">Choose file</label>
                        </div>
                    </div>
                    <script>
                        $('#inputSpreadsheet').on('change',function(){
                            //get the file name
                            var fileName = $(this).val();
                            var cleanFileName = fileName.replace('C:\\fakepath\\', " ");
                            //replace the "Choose a file" label
                            $(this).next('.custom-file-label').html(cleanFileName);
                        })
                    </script>
                    <?php
                    echo auth_bmlt($this->userName, $this->passWord);
                    echo '<pre>';
                    //print_r($sheetData);
                    foreach ($sheetData as $meeting) {
                        echo $meeting['Committee'] . "\n";
                    }
                    echo '</pre>';
                    ?>
                </form>
                <?php
            } else if (isset($this->userName) && !auth_bmlt($this->userName, $this->passWord)){
                echo '<div style="color:red;">Dude you suck, wrong username or password</div>';
                echo auth_bmlt($_POST['username'], $_POST['password'], $_POST['rootserver']);
                ?>
                <div id="signin" class="container" style="display: block;">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"
                          enctype="multipart/form-data">
                        <h2 class="form-signin-heading">Update World IDs from NAWS</h2>
                        <p>
                            <label for="inputRootServer" class="sr-only">Root Server</label>
                            <input name="rootserver" type="rootserver" id="inputRootServer" class="form-control" size="35" placeholder="Root Server"
                                   required autofocus>
                        </p>
                        <p>
                            <label for="inputUsername" class="sr-only">Username</label>
                            <input name="username" type="username" id="inputUsername" class="form-control" placeholder="Username"
                                   required autofocus>
                        </p>
                        <p>
                            <label for="inputPassword" class="sr-only">Password</label>
                            <input name="password" type="password" id="inputPassword" class="form-control"
                                   placeholder="Password" required>
                        </p>
                        <p>
                            <button type="submit">Authenticate</button>
                        </p>
                    </form>
                </div>
                <?php

            } ?>

            </body>
            </html>

            <?php
            $word = "hello";
            echo $word;
            $this->logout_auth($this->userName);
            echo $this->auth_bmlt($this->userName, $this->passWord);
        }

        public function auth_bmlt($username, $password, $master = false) {
            error_log($url);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->rootServer . '/local_server/server_admin/xml.php');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_COOKIEJAR,  ($master ? 'master' : $username) . '_cookie.txt');
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0) +BMLTN' );
            curl_setopt($ch, CURLOPT_POSTFIELDS, 'admin_action=login&c_comdef_admin_login='.$username.'&c_comdef_admin_password='.urlencode($password));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER,  false);
            $res = curl_exec($ch);
            curl_close($ch);
            return preg_match('/^OK$/', $res) == 1;
        }

        public function get($url,  $username = 'master') {
            error_log($url);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $username . '_cookie.txt');
            curl_setopt($ch, CURLOPT_COOKIEJAR, $username . '_cookie.txt');
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0) +BMLTN' );
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $data = curl_exec($ch);
            $errorno = curl_errno($ch);
            curl_close($ch);
            if ($errorno > 0) {
                throw new Exception(curl_strerror($errorno));
            }

            return $data;
        }


        public function logout_auth($username) {
            session_unset();
            $cookie_file = $username . '_cookie.txt';
            if (file_exists($cookie_file)) {
                $ch = curl_init();
                curl_setopt( $ch, CURLOPT_URL, $this->rootServer . '/local_server/server_admin/xml.php?admin_action=logout' );
                curl_setopt( $ch, CURLOPT_POST, 1 );
                curl_setopt( $ch, CURLOPT_COOKIEJAR, $cookie_file );
                curl_setopt( $ch, CURLOPT_COOKIEFILE, $cookie_file );
                curl_setopt( $ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0) +yap' );
                curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
                curl_setopt( $ch, CURLOPT_HEADER, false );
                $res = curl_exec( $ch );
                curl_close( $ch );
            } else {
                $res = "BYE;";
            }

            return !preg_match('/BYE/', $res);
        }
    }
}

if (class_exists("UpdateWorldIds")) {
    $UpdateWorldIds_instance = new UpdateWorldIds();
}

$UpdateWorldIds_instance->UpdateWorldIdsFunc();
