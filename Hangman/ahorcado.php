<?php
// Start session handling
session_start();
//Función para cambiar letras acentuadas por las que no
function unaccent($string) {
	$charToReplace = [
		'á' => 'a',
		'é' => 'e',
		'í' => 'i',
		'ó' => 'o',
		'ú' => 'u',
		'ü' => 'u'
	];
	
	foreach ($charToReplace as $key => $value) {
		$string = str_replace($key, $value, $string);
	}
	
	return $string;
}
//Función para ñ
function mb_str_split( $string ) {
	return preg_split('/(?<!^)(?!$)/u', $string );
}
//Iterar sobre los intentos sacando cada una de las letras usadas 
function letrasUsadas(){
	foreach($_SESSION['intentos'] as $letraUsada){
		echo '<td>'.$letraUsada.'</td>';
	}
}

$letras = [
	'A','B','C','D','E','F','G','H','I','J','K','L','M','N','Ñ','O','P','Q','R','S','T','U','V','W','X','Y','Z'
];
?>
<!DOCTYPE html>

<head>

<meta charset="UTF-8"/>
</head>

<body>
<center>
<?php
// Si no hay palabra, se elige y se empieza
	if (!isset($_SESSION['palabra'])){
		// Array palabras
		$arrayPalabras = array(
			'ordeñar', 
			'ungüento',
			'avión',
			'cañaveral',
			'paragüereños'
		);

		//Palabra aleatoria
		$_SESSION['palabra'] = strtolower($arrayPalabras[array_rand($arrayPalabras)]);
		
		//Intentos llevados a 0;
		$_SESSION['intentos'] = array();
			
		// Vidas a 6
		$_SESSION['vidas'] = 6;
	} 

// Se ha hecho intento, y es válido
	if (isset($_POST['intento'])){
		$arrayLetras = array();
		$intento = mb_strtolower($_POST['intento']);
		$intento = unaccent($intento);
		
		if (preg_match('/^([a-z]|ñ)$/', $intento)){
			// Comprueba si se ha introducido
			if (!in_array($intento, $_SESSION['intentos']) ){
				// Comprueba si está en la palabra
				if ( mb_strpos(($_SESSION['palabra']) , $intento) === FALSE ){
					// Quita vida
					--$_SESSION['vidas'];    
				}
				
				$_SESSION['intentos'][] = $intento;
				
			} else{
				echo "<script>alert('Ya has usado esta letra, hay que estar más atento');</script>";
			}
			
		}	
	} 

// Si vidas = 0
	if ($_SESSION['vidas'] == 0){
		echo '  <p>La palabra era "' . ($_SESSION['palabra'])  . '"</p>' . "\n\n";    
			
		echo '  <p><a href="ahorcado.php">¿Probamos de nuevo, amigo?</a></p>';     
		
		// Destruimos la variable palabra
		unset($_SESSION['palabra']);
	}
// Sino, muestra el estado del juego
	else{
		// Cuantas letras quedan
		$letrasQuedan = 0;
		$palabraAAdivinar = mb_str_split(($_SESSION['palabra']) );
		$palabraConCaracteres = unaccent($palabraAAdivinar);
		
		$salida = '';
		// Iterar a través de cada letra de la palabra
		for($i = 0; $i < mb_strlen(($_SESSION['palabra']) ); ++$i){
			// Comprueba si la letra actual ha sido adivinada
			if (in_array(($palabraConCaracteres[$i]), $_SESSION['intentos'])){
				// Mostramos letra en adivinadas
				$salida .= $palabraAAdivinar[$i]; 
			} 
			else{
				// Mostrar barrabaja
				$salida .= '_';
					
				// Incrementamos contador
				++$letrasQuedan;
			}
			// Añadimos un espacio entre '_'
			$salida .= ' ';
		}
		// Quitamos espacios de principio y final
		$salida = trim($salida);
			
		echo '  <p>' . $salida . '</p>' . "\n";
		echo '  <p> Te quedan ' . $_SESSION['vidas'] . ' vida(s)</p>';   
		
		// No hay letras para adivinar, empezamos juego     
		if ($letrasQuedan == 0 ){
			echo "\n\n" . '  <p><a href="ahorcado.php">Enhorabuena colega,¿Nuevo reto?</a></p>';  
		 
			// Quitar variable palabra de la sesión
			unset($_SESSION['palabra']);
		} 
	}
?>

	  <form method="post" action="ahorcado.php">
		<fieldset>
		  <legend align="center">Haga su apuesta</legend>

		  <table>
			<tr>
			  <td><label for="intento">Letra</label></td>

			  <td><select name="intento" id="intento">
					<?php 
					//Iterar sobre array de letras creado al principio
					foreach($letras as $letra) 
					{ 
						echo '<option value="' . $letra . '">' . $letra . '</option>'; 
					}
					?>
			  </select></td>
			</tr>

			<tr>
			  <td colspan="2"><input type="submit" name="submit" id="submit" value="Pruebe" /></td>
			</tr>
		  </table>
		</fieldset>
	  </form>
  
		<fieldset>
			<legend align="center">Letras ya usadas:</legend>
				<table border="1px" >
					<tr>
						<!--Llamada a la función creada arriba del todo-->
						<?php echo letrasUsadas(); ?>
					</tr>
				</table>
		</fieldset>
	
	</body>
</html>