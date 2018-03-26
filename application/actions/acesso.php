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
            //$funcao = new $this->container->funcoes();
            //$_SESSION['usuario']['login'] = $funcao->getLoginData($_SESSION['usuario']['login']);
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
            return \json_encode('Erro ao verificar email.');
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
}
