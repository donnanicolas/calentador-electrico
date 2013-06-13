<?php

class SiteController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		$temperaturasCT = NULL;
		$temperaturasCP = NULL;
		$temperaturasOT = NULL;
		$temperaturasOP = NULL;

		if(isset($_GET['res']) && isset($_GET['t'])) {
			$temperaturasCT = array();
			$temperaturasCP = array();
			$temperaturasOT = array();
			$temperaturasOP = array();
			$temperaturasDesplazadas = array();
			$temperaturaInicial = rand((20*0.8), (20*1.2));
			$temperaturaExterior = rand(-20, 30);
			$voltaje = rand((220*0.9),(220*1.1));
			$resistencia =  rand(($_GET['res'] * 0.95), ($_GET['res'] * 1.05));
			$calorEntregado = (pow($voltaje, 2)/$resistencia)/4.186;

			$coeficientePerdidaT = 0.04;
			$coeficientePerdidaP = 0.023;

			for ($i=0; $i < $_GET['t']; $i++) { 
				if($i > 0) {
					$calorPerdidoCT = (($temperaturasCT[$i-1] - $temperaturaExterior) * (0.06/0.001)*$coeficientePerdidaT)/4.186;
					$calorPerdidoCP = (($temperaturasCP[$i-1] - $temperaturaExterior) * (0.06/0.001)*$coeficientePerdidaP)/4.186;
					$calorPerdidoOT = (($temperaturasOT[$i-1] - $temperaturaExterior) * (0.055/0.001)*$coeficientePerdidaT)/4.186;
					$calorPerdidoOP = (($temperaturasOP[$i-1] - $temperaturaExterior) * (0.055/0.001)*$coeficientePerdidaP)/4.186;
					$temperaturasCT[$i] = $temperaturasCT[$i - 1] + ($calorEntregado - $calorPerdidoCT)/1000;
					$temperaturasCP[$i] = $temperaturasCP[$i - 1] + ($calorEntregado - $calorPerdidoCP)/1000;
					$temperaturasOT[$i] = $temperaturasOT[$i - 1] + ($calorEntregado - $calorPerdidoOT)/1000;
					$temperaturasOP[$i] = $temperaturasOP[$i - 1] + ($calorEntregado - $calorPerdidoOP)/1000;
					if ($temperaturasCT[$i] > 100 && !isset($tLimiteCT)) {
							$tLimiteCT = $i;
					}
					if ($temperaturasCP[$i] > 100 && !isset($tLimiteCP)) {
							$tLimiteCP = $i;
					}
					if ($temperaturasOT[$i] > 100 && !isset($tLimiteOT)) {
							$tLimiteOT = $i;
					}
					if ($temperaturasOP[$i] > 100 && !isset($tLimiteOP)) {
							$tLimiteOP = $i;
					}
				} else {
					$temperaturasCT[$i] = $temperaturaInicial;
					$temperaturasCP[$i] = $temperaturaInicial;
					$temperaturasOT[$i] = $temperaturaInicial;
					$temperaturasOP[$i] = $temperaturaInicial;
				}
				if(isset($_GET['l'])) {
					if($temperaturasOP[$i] > $_GET['l'] || $temperaturasCP[$i] > $_GET['l'] || $temperaturasCT[$i] > $_GET['l'] || $temperaturasOT[$i] > $_GET['l']) {
						break;
					}
				}
			}

			$values = true;
		}

		if (!isset($tLimiteCT)) {
				$tLimiteCT = NULL;
		}
		if (!isset($tLimiteCP)) {
				$tLimiteCP = NULL;
		}
		if (!isset($tLimiteOT)) {
				$tLimiteOT = NULL;
		}
		if (!isset($tLimiteOP)) {
				$tLimiteOP = NULL;
		}
		$this->render('index', array(
			'min' => min($tLimiteOP, $tLimiteOT, $tLimiteCP, $tLimiteCT),
			'temperaturaInicial' => $temperaturaInicial,
			'temperaturaExterior' => $temperaturaExterior,
			'voltaje' => $voltaje,
			'resistencia' => $resistencia,
			'temperaturasCT' => $temperaturasCT,
			'temperaturasCP' => $temperaturasCP,
			'temperaturasOT' => $temperaturasOT,
			'temperaturasOP' => $temperaturasOP,
			'tLimiteCT' => $tLimiteCT,
			'tLimiteCP' => $tLimiteCP,
			'tLimiteOT' => $tLimiteOT,
			'tLimiteOP' => $tLimiteOP,
		));
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}

	/**
	 * Displays the contact page
	 */
	public function actionContact()
	{
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				$name='=?UTF-8?B?'.base64_encode($model->name).'?=';
				$subject='=?UTF-8?B?'.base64_encode($model->subject).'?=';
				$headers="From: $name <{$model->email}>\r\n".
					"Reply-To: {$model->email}\r\n".
					"MIME-Version: 1.0\r\n".
					"Content-type: text/plain; charset=UTF-8";

				mail(Yii::app()->params['adminEmail'],$subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		$model=new LoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login())
				$this->redirect(Yii::app()->user->returnUrl);
		}
		// display the login form
		$this->render('login',array('model'=>$model));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}
}

