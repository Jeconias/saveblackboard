<?php

namespace application\classes;

class Language
{
	private $language = array();
	private $lang;

	public function __construct()
	{
		$this->lang = 'pt';
		$this->init();
	}

	private function init()
	{
		$this->language['initial'] 		   = 'pt';

		//OUTROS
		$this->language['welcomeTitle']    = 'Bem-Vindo(a)';
		$this->language['registerTitle']   = 'Register';
		$this->language['languages']       = 'Idiomas';
		$this->language['uploadImage'] 	   = 'Imagem enviada';
		$this->language['removeImage'] 	   = 'Imagem deletada';
		$this->language['removeImageFail'] = 'Imagem não deletada';

		//PAGINA LOGIN
		$this->language['classEmail'] 	   = 'Email da Turma';
		$this->language['key']			   = 'Senha';
		$this->language['forgetPass']      = 'Esqueceu sua senha?';
		$this->language['access']          = 'Entrar';
		$this->language['register']        = 'Cadastre sua turma';
		$this->language['sectionOne']      = 'Sua turma tira fotos do quadro-negro? Garanta essas imagens sempre com você!';
		$this->language['sectionTwo']      = 'Esse é um projeto apenas para experiência. Visite o GitHub';
		$this->language['keyIncorrect']    = 'Senha incorreta';

		//PAGINA DE RECUPERAÇÃO DE SENHA
		$this->language['recoverPass']     = 'Recuperar senha';
		$this->language['send']            = 'Enviar';
		$this->language['emailNotFound']   = 'Email não encontrado';
		$this->language['emailFound']      = 'Email enviado para você';
		$this->language['robotAssurance']  = 'Tem certeza que você não é um robô?';
		//PAGINA DE CADASTRO
		$this->language['className']       = 'Nome da turma';
		$this->language['email']           = 'Email';
		$this->language['confirmPass']	   = 'Confirme a senha';
		$this->language['buttonRegister']  = 'Registrar!';
		$this->language['buttonBack']      = 'Voltar';

		//PAGINA HOME
		$this->language['lastLogin']       = 'Seu último login foi às';
		$this->language['logout']          = 'Encerrar sessão';
		$this->language['uploadImg']       = 'Subir imagem';
	}

	public function changeLanguage($translate)
	{
		$file = __DIR__.'/lang/class.language.saveblackboard.'.$translate.'.php';
		if($translate != $this->lang && file_exists($file)){
			$this->language = array();

			include($file);
			if(is_array($newLanguage)){
				$this->language = array_merge($this->language, $newLanguage);
				$this->lang = $translate;
				return true;
			}else{
				$this->init();
				return false;
			}
		}
	}

	public function getLanguage():array
	{
		return $this->language;
	}

	public function getLanguageFilter($filter)
	{
		return $this->language[$filter];
	}
}