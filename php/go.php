<?php
//amo
//ПРЕДОПРЕДЕЛЯЕМЫЕ ПЕРЕМЕННЫЕ
function CheckCurlResponse($code)
{
	$code=(int)$code;
	$errors=array(
		301=>'Moved permanently',
		400=>'Bad request',
		401=>'Unauthorized',
		403=>'Forbidden',
		404=>'Not found',
		500=>'Internal server error',
		502=>'Bad gateway',
		503=>'Service unavailable'
	);
	try
	{
		#Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
		if($code!=200 && $code!=204)
			throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error',$code);
	}
	catch(Exception $E)
	{
		die('Ошибка: '.$E->getMessage().PHP_EOL.'Код ошибки: '.$E->getCode());
	}
}

$data=array(
	'name'=>isset($_POST['name']) ? $_POST['name'] : 'price_form',
	'phone'=>isset($_POST['phone']) ? $_POST['phone'] : '',
	'email'=>isset($_POST['email']) ? $_POST['email'] : 'sd'
);

#Если не указано имя или телефон контакта - уведомляем
if(empty($data['name']))
	die('Не заполнено имя контакта');
if(empty($data['phone']))
	die('Не заполнен телефон контакта');


$responsible_user_id = 1084857; //id ответственного по сделке, контакту, компании
$lead_name = 'Заявка с сайта (TITAN-KUZOV)'; //Название добавляемой сделки
$lead_status_id = '15066508'; //id этапа продаж, куда помещать сделку
$contact_name = $data['name']; //Название добавляемого контакта
$contact_phone = $data['phone']; //Телефон контакта
$contact_email = $data['email']; //Емейл контакта
$time = time();
//АВТОРИЗАЦИЯ
$user=array(
	'USER_LOGIN'=>'feo5@mail.ru', #Ваш логин (электронная почта)
	'USER_HASH'=>'2e62cbd81f3dfd3297e326c3d0d57ceb' #Хэш для доступа к API (смотрите в профиле пользователя)
);
$subdomain='intercomplus';
#Формируем ссылку для запроса
$link='https://'.$subdomain.'.amocrm.ru/private/api/auth.php?type=json';
$curl=curl_init(); #Сохраняем дескриптор сеанса cURL
#Устанавливаем необходимые опции для сеанса cURL
curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
curl_setopt($curl,CURLOPT_URL,$link);
curl_setopt($curl,CURLOPT_POST,true);
curl_setopt($curl,CURLOPT_POSTFIELDS,http_build_query($user));
curl_setopt($curl,CURLOPT_HEADER,false);
curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
$out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
$code=curl_getinfo($curl,CURLINFO_HTTP_CODE); #Получим HTTP-код ответа сервера
curl_close($curl);  #Завершаем сеанс cURL
$Response=json_decode($out,true);


//echo '<b>Данные аккаунта:</b>';
//echo '<pre>';
//print_r($Response);
//echo '</pre>';
//exit;


//ПОЛУЧАЕМ ДАННЫЕ АККАУНТА
$link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/accounts/current'; #$subdomain уже объявляли выше
$curl=curl_init(); #Сохраняем дескриптор сеанса cURL
#Устанавливаем необходимые опции для сеанса cURL
curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
curl_setopt($curl,CURLOPT_URL,$link);
curl_setopt($curl,CURLOPT_HEADER,false);
curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
$out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
$code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
curl_close($curl);
$Response=json_decode($out,true);
$account=$Response['response']['account'];

//echo '<b>Данные аккаунта:</b>';
//echo '<pre>';
//print_r($Response);
//echo '</pre>';
//exit;

//ПОЛУЧАЕМ СУЩЕСТВУЮЩИЕ ПОЛЯ
$amoAllFields = $account['custom_fields']; //Все поля
$amoConactsFields = $account['custom_fields']['contacts']; //Поля контактов

//echo '<b>Поля из амо:</b>';
//echo '<pre>';
//print_r($amoAllFields);
//echo '</pre>';
//exit;

//ФОРМИРУЕМ МАССИВ С ЗАПОЛНЕННЫМИ ПОЛЯМИ КОНТАКТА
//Стандартные поля амо:
$sFields = array_flip(array(
		'PHONE', //Телефон. Варианты: WORK, WORKDD, MOB, FAX, HOME, OTHER
		'EMAIL' //Email. Варианты: WORK, PRIV, OTHER
	)
);
//Проставляем id этих полей из базы амо
foreach($amoConactsFields as $afield) {
	if(isset($sFields[$afield['code']])) {
		$sFields[$afield['code']] = $afield['id'];
	}
}

//ДОБАВЛЯЕМ СДЕЛКУ
$leads['request']['leads']['add']=array(
	array(
		'name' => $lead_name,
		'status_id' => $lead_status_id, //id статуса
		'responsible_user_id' => $responsible_user_id, //id ответственного по сделке
		//'date_create'=>1298904164, //optional
		//'price'=>300000,
		'tags' => 'lend, TITAN-KUZOV', #Теги
		//'custom_fields'=>array()
	)
);

//echo '<b>Поля из амо:</b>';
//echo '<pre>';
//print_r($leads);
//echo '</pre>';
//exit;

$link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/leads/set';
$curl=curl_init(); #Сохраняем дескриптор сеанса cURL
#Устанавливаем необходимые опции для сеанса cURL
curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
curl_setopt($curl,CURLOPT_URL,$link);
curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($leads));
curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
curl_setopt($curl,CURLOPT_HEADER,false);
curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
$out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
$code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
$Response=json_decode($out,true);

//echo '<b>Новая сделка:</b>';
//echo '<pre>'; print_r($Response);
//echo '</pre>';
//exit;

if(is_array($Response['response']['leads']['add']))
	foreach($Response['response']['leads']['add'] as $lead) {
		$lead_id = $lead["id"]; //id новой сделки
	};

//echo '<b>id новой сделки:</b>';
//echo '<pre>';
//print_r($lead_id);
//echo '</pre>';
//exit;

//ДОБАВЛЯЕМ СДЕЛКУ - КОНЕЦ


//ДОБАВЛЕНИЕ КОНТАКТА
$contact = array(
	'name' => $contact_name,
	'linked_leads_id' => array($lead_id), //id сделки
	'responsible_user_id' => $responsible_user_id, //id ответственного
	'tags' => 'lend, TITAN-KUZOV',
	'custom_fields'=>array(
		array(
			'id' => $sFields['PHONE'],
			'values' => array(
				array(
					'value' => $contact_phone,
					'enum' => 'MOB'
				)
			)
		),
		array(
			'id' => $sFields['EMAIL'],
			'values' => array(
				array(
					'value' => $contact_email,
					'enum' => 'WORK'
				)
			)
		)
	)
);
$set['request']['contacts']['add'][]=$contact;
#Формируем ссылку для запроса
$link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/contacts/set';
$curl=curl_init(); #Сохраняем дескриптор сеанса cURL
#Устанавливаем необходимые опции для сеанса cURL
curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
curl_setopt($curl,CURLOPT_URL,$link);
curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($set));
curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
curl_setopt($curl,CURLOPT_HEADER,false);
curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
$out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
$code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
CheckCurlResponse($code);
$Response=json_decode($out,true);

//echo '<b>Новый контакт:</b>';
//echo '<pre>';
//print_r($out);
//echo '</pre>';
//exit;


if(is_array($Response['response']['contacts']['add']))
	foreach($Response['response']['contacts']['add'] as $set) {
		$set_id = $set["id"]; //id нового контакта
	};

//echo '<b>id нового контакта:</b>';
//echo '<pre>';
//print_r($set_id);
//echo '</pre>';
//exit;

//ДОБАВЛЕНИЕ КОНТАКТА - КОНЕЦ

//ДОБАВЛЕНИЕ ЗАДАЧИ
$tasks['request']['tasks']['add']=array(
  #Привязываем к сделке
  array(
    'element_id'=>$lead_id, #ID сделки
    'element_type'=>2, #Показываем, что это - сделка, а не контакт
	'date_create'=>$time,
    'task_type'=>1, #Звонок
    'text'=>'Заявка с лендинга (TITAN-KUZOV)',
    'responsible_user_id'=>$responsible_user_id,
    'complete_till'=>$time
  ),
  #Привязываем к контакту
//  array(
//    'element_id'=>$set_id,
//    'element_type'=>1,
//	'date_create'=>$time,
//    'task_type'=>1, #Звонок
//    'text'=>'Заявка с лендинга (HOME)',
//    'responsible_user_id'=>$responsible_user_id,
//    'complete_till'=>$time
//  ),
);
////print_r($time);
#Формируем ссылку для запроса
$link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/tasks/set';
$curl=curl_init(); #Сохраняем дескриптор сеанса cURL
#Устанавливаем необходимые опции для сеанса cURL
curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
curl_setopt($curl,CURLOPT_URL,$link);
curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($tasks));
curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
curl_setopt($curl,CURLOPT_HEADER,false);
curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);

$out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
$code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
//echo '<b>Новая задача:</b>'; echo '<pre>'; print_r($Response); echo '</pre>';
header("Location: http://titan-kuzov.ru/thank_you.html"); /* Перенаправление браузера */

/* Можно убедиться, что следующий за командой код не выполнится из-за
перенаправления.*/
exit;
//amo
