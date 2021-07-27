<?php
error_reporting(-1);
header('Content-Type: text/html; charset=utf-8');
require_once("./mailsender/PHPMailerAutoload.php");

$subject = 'Новая заявка с сайта light-house.kz '.$_POST['name'].'. '.$_POST['phone'];

$form_data = array(
	'Страница'	=> 'Light-house.kz ',
	'Форма' 		=> 'Получить эскиз подсветки',
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
    $step_data_telegram .= $step['title'] . ' - ' . $step['value'] ."\n";
}


$message = "<table style='width: 100%;'>$message</table><br>$steps_data";




$mail = new PHPMailer;
$mail->addAddress('maximum454@gmail.com');
//$mail->addAddress('lied@ledcapital.ru');
//$mail->addAddress('info@ledcapital.ru');
$mail->Subject = $subject;
$mail->Body    = $message;


// $url = 'https://crm.olivin.ru/static/rec_zayavka/';
// $params = array(
// 	'subject' => $subject,
// 	'email' => $_POST['email'],
// 	'name' => $_POST['name'],
// 	'tel' => $_POST['phone'],
// 	'info' => $steps_data,
// 	'cat' => 2
// ); 
// $result = file_get_contents($url, false, stream_context_create(array(
// 	'http' => array(
// 		'method'  => 'POST',
// 		'header'  => 'Content-type: application/x-www-form-urlencoded',
// 		'content' => http_build_query($params)
// 	)
// )));


if($mail->send()) {
    echo 'success';
    message_to_telegram('Новая заявка с сайта light-house.kz'."\n Почта: ". $_POST['email'] . "\n Имя: " .  $_POST['name'] . "\n Телефон: " . $_POST['phone'] . "\n" . $step_data_telegram);

}else return false;

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
                'parse_mode' => html,
                'text' => $text,
            ),
        )
    );
    curl_exec($ch);
}
//Light: 1698168239:AAEEmmlBGkGMu1YQnmdxj4wJReNo9cR0Nyw
//id_chat: 113185455
//My: 1902978954:AAElCBiYvwXs2wjGylDvyf1qshNLm1e36rk
//My id_chat: -433517678

