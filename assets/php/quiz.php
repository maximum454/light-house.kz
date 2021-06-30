<?php

require_once("./mailsender/PHPMailerAutoload.php");

$subject = '[ledcapital.ru] Аренда экранов. '.$_POST['name'].'. '.$_POST['phone'];

$form_data = array(
	'Страница'	=> 'Аренда светодиодных экранов',
	'Форма' 		=> 'Узнать стоимость экрана (опросник)',
	'Имя'				=> $_POST['name'] ?: 'Не указано',
	'Телефон' 	=> $_POST['phone'] ?: 'Не указан',
	'E-mail' 		=> $_POST['email'] ?: 'Не указан'
);

$c = true;
foreach ( $form_data as $key => $value ) {
	if ( $value != "" ) {
		$message .= "
		" . ( ($c = !$c) ? '<tr>':'<tr style="background-color: #f8f8f8;">' ) . "
			<td style='padding: 10px; border: #e9e9e9 1px solid;'><b>$key</b></td>
			<td style='padding: 10px; border: #e9e9e9 1px solid;'>$value</td>
		</tr>
		";
	}
}

foreach ($_POST['steps'] as $key => $step) {
	if (is_array($step['value'])) {
		$inner_value = '';
		foreach ($step['value'] as $title => $value) {
			$inner_value .= $title.' - '.$value.'; ';
		}
		$step['value'] = $inner_value;
	}
	$steps_data .= $step['title'].': '.$step['value'].'<br>';
}

$message = "<table style='width: 100%;'>$message</table><br>$steps_data";


$mail = new PHPMailer;
// $mail->addAddress('adoonq@gmail.com');
$mail->addAddress('lied@ledcapital.ru');
$mail->addAddress('info@ledcapital.ru');
$mail->Subject = $subject;
$mail->Body    = $message;


$url = 'https://crm.olivin.ru/static/rec_zayavka/';
$params = array(
	'subject' => $subject,
	'email' => $_POST['email'],
	'name' => $_POST['name'],
	'tel' => $_POST['phone'],
	'info' => $steps_data,
	'cat' => 2
); 
$result = file_get_contents($url, false, stream_context_create(array(
	'http' => array(
		'method'  => 'POST',
		'header'  => 'Content-type: application/x-www-form-urlencoded',
		'content' => http_build_query($params)
	)
)));

if($mail->send()) {echo 'success';
message_to_telegram('Новая заявка с сайта light-house.kz, ' . $_POST['email'] . ' ' .  $_POST['name'] . ' ' . $_POST['phone']) . $message;}
else return false;

$mail->ClearAddresses();


function message_to_telegram($text)
{
    $ch = curl_init();
    curl_setopt_array(
        $ch,
        array(
            CURLOPT_URL => 'https://api.telegram.org/bot' . '1698168239:AAEEmmlBGkGMu1YQnmdxj4wJReNo9cR0Nyw' . '/sendMessage',
            CURLOPT_POST => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_POSTFIELDS => array(
                'chat_id' => 113185455,
                'text' => $text,
            ),
        )
    );
    curl_exec($ch);
}