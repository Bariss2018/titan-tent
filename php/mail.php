<?php
if(isset($_POST['name']) && isset($_POST['phone']))
{
    $name = htmlentities($_POST['name']);
    $eduform = htmlentities($_POST['phone']);
    $output ="
    <html>
    <head>
    <title>Анкетные данные</title>
    </head>
    <body>
    Вас зовут: $name<br />
    Форма обучения: $eduform<br />";
//    echo $output;
//	sleep(5);
	header('Location: http://intercom-plus.ru/crm/index.html');
	exit;
}
else
{
    echo "Введенные данные некорректны";
}

?>
