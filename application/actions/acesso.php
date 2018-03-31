<?php

namespace application\actions;

final class acesso
{
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function getLogin($request, $response)
    {
        if (isset($_SESSION['usuario'])) {
            unset($_SESSION['usuario']);
            session_destroy();
        }
        return $this->container->views->render($response, 'login.phtml');
    }
    public function postLogin($request, $response)
    {
        $email = str_replace(' ', '', strip_tags(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL)));

        $query = $this->container->crud;
        $query->setSelect('turmas', array('*'), array('email' => $email));
        $user = $query->getSelect();

        if (!isset($query->getSelect()[0])) {
            return json_encode('Email não encontrado');
        }

        // VERIFICAR SENHA
        if (crypt($_POST['password'], $user[0]['senha']) === $user[0]['senha']) {
            $_SESSION['usuario'] = $user[0];

            //REGISTRAR ENTRADA NO LOG
            //$this->container->logger->addInfo($_SESSION['usuario']['cpf'].' logou:', array('IP' => $_SERVER['REMOTE_ADDR']));

            //REGISTRAR ENTRADA NO BANCO DE DADOS
            $query->setUpdate('turmas', array('login' => date('Y-m-d H:i:s')), array('email' => $email));

            //GERAR FRASE DE ULTIMO LOGIN
            $_SESSION['usuario']['login'] = $this->ultimoLogin($_SESSION['usuario']['login']);

            //SETANDO COOKIE PARA LOGIN
            setcookie('turma', $email, time() + 60 * 60 * 24 * 7);
            return json_encode(true);
        } else {
            return json_encode('Senha incorreta');
        }
    }

    public function home($request, $response)
    {
        $crud = $this->container->crud;
        // $crud->setSelect('imagens', array('*'), array('turma' => $_SESSION['usuario']['id']));
        $crud->setSelectsql('SELECT * FROM imagens WHERE turma=:id ORDER BY data DESC', array('id' => $_SESSION['usuario']['id']));
        $dados['imagens'] = $crud->getSelect();
        return $this->container->views->render($response, 'home.phtml', $dados);
    }

    public function getNovaturma($request, $response)
    {
        return $this->container->views->render($response, 'cadastro.phtml');
    }

    public function postNovaturma($request, $response)
    {
        $nome = str_replace(' ', '', $_POST['nome']);
        $email = mb_strtolower(str_replace(' ', '', $_POST['email']), 'UTF-8');
        $senha = $_POST['senha'];

        $crud = $this->container->crud;
        $crud->setInserir('turmas', array(
        'nome' => $nome,
        'email' => $email,
        'senha' => $senha
      ));

        if ($crud->getInserir() >= 1) {
            return $response->withRedirect(PATH.'/login?s=true');
        }
        return $response->withRedirect(PATH.'/login?s=false');
    }
    public function checkNovaturma($request, $response)
    {
        //GARANTINDO UM VALOR PARA EMAIL
        if (!isset($_POST['email'])) {
            return json_encode('Erro ao verificar email.');
        }

        //PEGANDO TODOS OS EMAILS DO BANCO DE DADOS
        $crud = $this->container->crud;
        $crud->setSelect('turmas', array('email'));

        $dados = $crud->getSelect();

        //SE O BANCO DE DADOS ESTIVER VÁZIL ELE RETORNA FALSE PARA EVITAR UM ERRO
        if (!isset($crud->getSelect()[0])) {
            return json_encode(array('email' => false));
        }

        //VERIFICANDO SE O EMAIL EXISTE
        $dados_count = count($dados);
        $a = 0;
        while ($a < $dados_count) {
            $emails [] = mb_strtolower($dados[$a]['email'], 'UTF-8');
            $a++;
        }
        //SE O VALOR RETORNADO PELO in_array FOR TRUE, ENTÃO EXISTE UM EMAIL JÁ CADASTRADO
        $emailVerificado = in_array(mb_strtolower(str_replace(' ', '', $_POST['email']), 'UTF-8'), $emails);

        return json_encode(array('email' => $emailVerificado));
    }

    public function getResetpassword($request, $response)
    {
        $email64 = $request->getAttribute('email');
        $code = $request->getAttribute('code');

        $crud = $this->container->crud;
        $crud->setSelect('recuperar_senha', array('email'), array('email' => $email64, 'code' => $code), 1);

        if (count($crud->getSelect()) == 0) {
          return $response->withRedirect(PATH.'/recovery/password?reset=false');
        }
        return $this->container->views->render($response, 'reset.phtml');
    }

    public function postResetpassword($request, $response)
    {
        $email64 = $request->getAttribute('email');
        $code = $request->getAttribute('code');

        $crud = $this->container->crud;
        $crud->setSelect('recuperar_senha', array('email'), array('email' => $email64, 'code' => $code), 1);

        if (count($crud->getSelect()) == 0) {
          return $response->withRedirect(PATH.'/recovery/password?reset=false');
        }

        $crud->setUpdate('turmas', array('senha' => $_POST['senha']), array('email' => base64_decode($email64)));

        if (count($crud->getUpdate()) == 1) {
          $crud->setDelete('recuperar_senha', array('email' => $email64));
          return $response->withRedirect(PATH.'/login?reset=true');
        }
        return $response->withRedirect(PATH.'/login?reset=false');
    }

    public function getRecoverypassword($request, $response)
    {
        return $this->container->views->render($response, 'recovery.phtml');
    }

    public function postRecoverypassword($request, $response)
    {
        $responseGoogle = null;
        if (isset($_POST['g-recaptcha-response']) && $_POST['g-recaptcha-response'] != "") {
            $responseGoogle = curl_init('https://www.google.com/recaptcha/api/siteverify?secrete=6Ldsl08UAAAAAKyvwRj3zPKpv22qL_Ap83Ya9bO5='.$_POST['g-recaptcha-response'].'&remoteip='.$_SERVER['REMOTE_ADDR']);
            if ($responseGoogle != "" && $responseGoogle == true) {
                $email = $_POST['email'];
                $crud = $this->container->crud;
                $crud->setSelect('turmas', array('nome'), array('email' => $email), 1);
                $result = $crud->getSelect();

                if (count($result) == 1) {
                    //COLOCAR EM BASE64
                    $email64 = base64_encode($email);
                    //GERAR O CÓDIGO
                    $code = $this->codeGenerator();
                    //ARMAZENAR OS VALORES NO BD
                    $crud->setInserir('recuperar_senha', array('email' => $email64, 'code' => $code));
                    //PEGAR O PRIMEIRO NOME
                    $nome = explode(' ', $result[0]['nome']);
                    //ARRAY COM OS VALORES PARA ENVIO
                    $url_root = empty($_SERVER['HTTPS']) ? 'http://'.$_SERVER['SERVER_NAME'] : 'https://' . $_SERVER['SERVER_NAME'];
                    $values = array('nome' => $nome[0], 'link' => $url_root.PATH.'/reset/password/'.$email64.'/'.$code);
                    //PASSAR A PÁGINA PARA UMA STRING
                    $viewEmail = file_get_contents($_SERVER['DOCUMENT_ROOT'].PATH.'/resources/views/email/recuperacao.html');

                    //SETANDO OS VALORES
                    foreach ($values as $key => $value) {
                        $viewEmail = str_replace('%'.$key.'%', $value, $viewEmail);
                    }
                    $viewEmail = str_replace('%ano%', date('Y'), $viewEmail);

                    $mail = $this->container->mailer;
                    $mail->CharSet = 'UTF-8';
                    try {
                      //Server settings
                      $mail->SMTPDebug = 0;                                 // Enable verbose debug output
                      $mail->isSMTP();                                      // Set mailer to use SMTP
                      $mail->Host = 'mail.olamundoweb.com.br';  // Specify main and backup SMTP servers
                      $mail->SMTPAuth = true;                               // Enable SMTP authentication
                      $mail->Username = 'no-reply@olamundoweb.com.br';                 // SMTP username
                      $mail->Password = 'No-replyOla1';                           // SMTP password
                      $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
                      $mail->Port = 465;                                    // TCP port to connect to

                      $mail->AddEmbeddedImage($_SERVER['DOCUMENT_ROOT'].PATH.'/resources/assets/images/reset_password.png', 'logo');
                      $mail->setFrom('no-reply@olamundoweb.com.br', 'Olá Mundo Web');
                      $mail->addAddress($email);

                      $mail->isHTML(true);
                      $mail->Subject = 'Recuperação de senha';
                      $mail->Body    = $viewEmail;

                      $mail->send();
                      return json_encode(true);
                    } catch (Exception $e) {
                      return json_encode(false);
                      $return =  'Mailer Error: ' . $mail->ErrorInfo;
                    }
                }
                return json_encode('Email não encontrado!');
            } else {
                return json_encode('Tem certeza que você não é um Robô?!');
            }
        } else {
            return json_encode('Confirme que você não é um Robô! ;D');
        }
    }

    //GERAR UM CÓDIGO ALEATÓRIO DE 22 CARACTERES
    private function codeGenerator()
    {
        $string = 'abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ0123456789';
        $retorno = '';
        for ($i = 1; $i <= 22; $i++) {
            $rand = mt_rand(1, strlen($string));
            $retorno .= $string[$rand-1];
        }
        return $retorno;
    }
    private function ultimoLogin($data)
    {
      if ($data != null) {
          if (date('m-d') == date('m-d', strtotime($data))) {
              return 'Seu último login foi hoje ás '.strftime('%R', strtotime($data));
          } elseif (date('m-d', strtotime($data)) == date('m-d', strtotime('-1 day'))) {
              return 'Seu último login foi ontem ás '.strftime('%R', strtotime($data));
          }
          return 'Seu último login foi dia '.strftime('%d de %h ás %R', strtotime($data));
      }
      return null;
    }
}
