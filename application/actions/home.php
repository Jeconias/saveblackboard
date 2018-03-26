<?php

namespace application\actions;

final class home
{
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function upload($request, $response)
    {
        $directory = $_SERVER['DOCUMENT_ROOT'].PATH.'/resources/users/'.$_SESSION['usuario']['id'];

        if (!is_dir($directory)) {
            mkdir($directory, 0777);
        }
        //NOVO NOME DA IMAGEM
        $nomeFile = $this->codeGenerator();

        $Upload = new $this->container->FileUpload();
        $Upload->upload($_FILES['files'], 'pt_BR');
        $Upload->file_max_size = 5040419;
        $Upload->file_force_extension = true;
        $Upload->dir_auto_create = true;
        $Upload->dir_auto_chmod = true;
        $Upload->file_new_name_body = $nomeFile;
        $Upload->allowed = array('image/png', 'image/jpeg', 'image/jpg');
        $Upload->Process($directory);

        //REFAZER O UPLOAD COM UM TAMANHO MENOR PARA EXIBIR
        $Upload->image_resize = true;
        $Upload->image_x = 450;
        $Upload->image_y = 300;
        $Upload->image_ratio_crop  =  true;
        $Upload->file_new_name_body = 'mine_'.$nomeFile;
        $Upload->Process($directory);

        if ($Upload->processed) {
            $crud = $this->container->crud;
            $crud->setInserir('imagens', array('turma' => $_SESSION['usuario']['id'], 'imagem' => $nomeFile.'.'.$Upload->file_dst_name_ext, 'ip' => $_SERVER['REMOTE_ADDR'], 'data' => date('Y-m-d')));
            $crud->setSelectsql('SELECT LAST_INSERT_ID() FROM imagens');
            $id_file = $crud->getSelect()[0]['LAST_INSERT_ID()'];
            return json_encode(array(
              'status' => true,
              'file' => PATH.'/resources/users/'.$_SESSION['usuario']['id'].'/mine_'.$nomeFile.'.'.$Upload->file_dst_name_ext,
              'id_file' => $id_file,
              'data' => strftime('%d/%b', time())
            ));
        }
        return json_encode(array(
          'status' => false,
          'error' => $Upload->error
        ));
    }

    public function download($request, $response)
    {
        //ID DA IMAGEM
        $fileDonwload = $request->getAttribute('img');
        //PEGANDO O NOME DA IAMGEM QUE ESTÁ NO BANCO DE DADOS
        $crud = $this->container->crud;
        $crud->setSelect('imagens', array('imagem'), array('id' => $fileDonwload));
        //SE A IMAGEM NÃO EXISTIR
        if (!count($crud->getSelect()) >= 1) {
          return json_encode('Imagem não localizada!');
        }
        //NOME DA IMAGEM
        $fileName = $crud->getSelect()[0]['imagem'];
        //PEGANDO A EXTENSÃO DA IMAGEM
        $extensao = explode('.', $fileName);
        //DIRECTORIO DAS IMAGENS + O NOME DA IMAGEM
        $diretorio = $_SERVER['DOCUMENT_ROOT'].PATH.'/resources/users/'.$_SESSION['usuario']['id'].'/'.$fileName;
        header('Content-Description: File Transfer');
        //configure nosso tipo de conteúdo para combinar o arquivo que estamos baixando
        header("Content-Type: image/".$extensao[1]);
        // Diga ao navegador quão grande o arquivo vai ser
        header("Content-Length:".filesize($diretorio). "\ n \ n");
        // forçar o arquivo a ser baixado
        header('Content-Disposition: attachment; filename='.$fileName);
        // ecoar o conteúdo do arquivo
        return file_get_contents($diretorio);
    }

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
}
