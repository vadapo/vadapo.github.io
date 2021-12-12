session_start(); // Токен будем хранить в сессии

// Параметры приложения
$clientId     = '8025170'; // ID приложения
$clientSecret = 'Rd7XKFoQGzYKn4JjGD5S'; // Защищённый ключ
$redirectUri  = 'https://vadapo.github.io/'; // Адрес, на который будет переадресован пользователь после прохождения авторизации

// Формируем ссылку для авторизации
$params = array(
	'client_id'     => $clientId,
	'redirect_uri'  => $redirectUri,
	'response_type' => 'code',
	'v'             => '5.131', // (обязательный параметр) версия API, которую Вы используете https://vk.com/dev/versions

	// Права доступа приложения https://vk.com/dev/permissions
	// Если указать "offline", полученный access_token будет "вечным" (токен умрёт, если пользователь сменит свой пароль или удалит приложение).
	// Если не указать "offline", то полученный токен будет жить 12 часов.
	'scope'         => 'photos,offline',
);

// Выводим на экран ссылку для открытия окна диалога авторизации
echo '<a href="http://oauth.vk.com/authorize?' . http_build_query( $params ) . '">Авторизация через ВКонтакте</a>';

if ( isset( $_GET['code'] ) ) {
 
	$params = array(
		'client_id'     => $clientId,
		'client_secret' => $clientSecret,
		'code'          => $_GET['code'],
		'redirect_uri'  => $redirectUri
	);
 
	if (!$content = @file_get_contents('https://oauth.vk.com/access_token?' . http_build_query($params))) {
		$error = error_get_last();
		throw new Exception('HTTP request failed. Error: ' . $error['message']);
	}
 
	$response = json_decode($content);
 
	// Если при получении токена произошла ошибка
	if (isset($response->error)) {
		throw new Exception('При получении токена произошла ошибка. Error: ' . $response->error . '. Error description: ' . $response->error_description);
	}
 
	$token = $response->access_token; // Токен
	$expiresIn = $response->expires_in; // Время жизни токена
	$userId = $response->user_id; // ID авторизовавшегося пользователя
 
	// Сохраняем токен в сессии
	$_SESSION['token'] = $token;
 
 
} elseif ( isset( $_GET['error'] ) ) { // Если при авторизации произошла ошибка
 
	throw new Exception( 'При авторизации произошла ошибка. Error: ' . $_GET['error']
	                     . '. Error reason: ' . $_GET['error_reason']
	                     . '. Error description: ' . $_GET['error_description'] );
}
