<?php

class hangman extends game
{
	var $guesses;				
	var $letters = array();		
	var $wordIndex;				
	var $wordLetters = array();	
	var $wordList = array();	
	
	var $alphabet = array(		//array - todas las letras del alfabeto
		"a", "b", "c", "d", "e", "f", "g", "h",
		"i", "j", "k", "l", "m", "n", "o", "p", 
		"q", "r", "s", "t", "u", "v", "w", "x", 
		"y", "z");
		
	var $punctuation = array(	//matriz - signos de puntuación en la lista de palabras
		" ", "\,", "\'", "\"", "\/", "\\", "\[",
		"\]", "\+", "\-", "\_", "\=", "\!", "\~", 
		"\~", "\@", "\.", "\?", "\$", "\#", "\%", 
		"\^", "\&", "\*", "\(", "\)", "\{", "\}",
		"\|", "\:", "\;");

	/**
	* Propósito: constructor por defecto
	* Condiciones previas: ninguna
	* Condiciones posteriores: objeto principal iniciado
	**/
	function hangman()
	{
		/**
		* instanciar la clase de juego principal para que esta clase
		* hereda todos los atributos de la clase de juego
		* y métodos
		**/
		game::start();
	}
	
	/**
	* Propósito: iniciar un nuevo juego del ahorcado
	* Condiciones previas: número máximo de conjeturas
	* Condiciones posteriores: el juego está listo para mostrarse
	**/
	function newGame($max_guesses = 5)
	{
		//configurar el juego
		$this->start();
		
		//asegúrese de borrar las últimas letras que adivinaron
		$this->letters = array();
			
		//establece cuántas conjeturas obtienen antes de que termine el juego
		if ($max_guesses)
			$this->setGuesses($max_guesses);
			
		//elegir una palabra para que traten de adivinar
		$this->setWord();
	}
	
	/**
	* Propósito: establecer o recuperar el máximo de conjeturas antes de que termine el juego
	* Condiciones previas:
	* Postcondiciones:
	**/
	function playGame($game)
    {
        //el jugador presionó el botón para comenzar un nuevo juego
        if (isset($game['newgame']) || empty($this->wordList))
            $this->newGame();

        //El jugador está tratando de adivinar una letra
        if (!$this->isOver() && isset($game['letter']))
            echo $this->guessLetter($game['letter']);

        //mostrar el juego
        $this->displayGame();
    }
	
	/**
	* Propósito: establecer o recuperar el máximo de conjeturas que pueden hacer
	* Condiciones previas: cantidad de conjeturas (opcional)
	* Postcondiciones: las conjeturas se han actualizado
	**/
	function setGuesses($amount = 0)
	{		
		$this->guesses += $amount;
	}
	
	/**
	* Propósito: mostrar la interfaz del juego
	* Condiciones previas: ninguna
	* Condiciones posteriores: iniciar un juego o seguir jugando el juego actual
	**/
	function displayGame()
	{
		//mientras el juego no ha terminado
		if (!$this->isOver())
		{
			echo "<div id=\"picture\">" . $this->picture() . "</div>
				<div id=\"guess_word\">" . $this->solvedWord() . "</div>
				<div id=\"select_letter\">
					Introduzca una letra:
						<input type=\"text\" name=\"letter\" value=\"\" size=\"2\" maxlength=\"1\" />
						<input type=\"submit\" name=\"submit\" value=\"Adivinar\" />
				</div>";
				
				if (!empty($this->letters))
					echo "<div id=\"guessed_letters\">Letras adivinadas: " . implode($this->letters, ", ") . "</div>";
		}
		else
		{
			//they've won the game
			if ($this->won)
				echo successMsg("¡Felicidades! has ganado el juego.<br/>
								Su puntaje final fue: $this->score");
			else if ($this->health < 0)
			{
				echo errorMsg("¡Juego terminado! Buen intento..<br/>
								Su puntaje final fue: $this->score");

				echo "<div id=\"picture\">" . $this->picture() . "</div>";
			}

			echo "<div id=\"start_game\"><input type=\"submit\" name=\"newgame\" value=\"Nuevo Juego\" /></div>";
		}
	}
	
	/**
	* Propósito: adivinar una letra en esta palabra
	* Condiciones previas: un juego ha comenzado
	* Condiciones posteriores: los datos del juego se actualizan
	**/
	function guessLetter($letter)
	{			

		if ($this->isOver())
			return;

		if (!is_string($letter) || strlen($letter) != 1 || !$this->isLetter($letter))
			return errorMsg("¡Ups! Por favor ingrese una letra.");
			
		//comprobar si ya han adivinado la letra
		if (in_array($letter, $this->letters))
			return errorMsg("¡Ups! Ya has adivinado esta letra.");
			
		//solo permite letras minúsculas
		$letter = strtolower($letter);
			
		//si la palabra contiene esta letra
		if (!(strpos($this->wordList[$this->wordIndex], $letter) === false))
		{
			//aumentar su puntaje según la cantidad de intentos que hayan hecho hasta ahora
			if ($this->health > (100/ceil($this->guesses/5)))
				$this->setScore(5);
			else if ($this->health > (100/ceil($this->guesses/4)))
				$this->setScore(4);
			else if ($this->health > (100/ceil($this->guesses/3)))
				$this->setScore(3);
			else if ($this->health > (100/ceil($this->guesses/2)))
				$this->setScore(2);
			else
				$this->setScore(1);
				
			//agregue la letra a la matriz de letras
			array_push($this->letters, $letter);
			
			//si han encontrado todas las letras de esta palabra
			if (implode(array_intersect($this->wordLetters, $this->letters), "") == 
				str_replace($this->punctuation, "", strtolower($this->wordList[$this->wordIndex])))
				$this->won = true;
			else
				return successMsg("Buena suposición, eso es correcto!");
		}
		else //la palabra no contiene la letra
		{
		
			//reducir su vida
			$this->setHealth(ceil(100/$this->guesses) * -1);
			
			//agregue la letra a la matriz de letras
			array_push($this->letters, $letter);
			
			if ($this->isOver())
				return;
			else
				return errorMsg("No hay letra en esta palabra.");
		}
	}
	
	/**
	* Propósito: elige una palabra al azar para tratar de resolver
	* Condiciones previas: ninguna
	* Condiciones posteriores: si la palabra existe, se ha establecido un índice de palabras
	**/
	function setWord()
	{
		//si la lista de palabras está vacía, primero debemos cargarla
		if (empty($this->wordList))
			$this->loadWords();
	
		//restablecer el índice de palabras a una nueva palabra
		if (!empty($this->wordList))
			$this->wordIndex = rand(0, count($this->wordList)-1);
			
		//convertir la cadena en una matriz que podamos usar
		$this->wordToArray();
	}
	
	/**
	* Propósito: cargar las palabras del archivo de configuración en una matriz
	* Condiciones previas: nombre de archivo desde el que cargar las palabras (opcional)
	* Postcondiciones: la lista de palabras ha sido cargada
	**/
	function loadWords($filename = "config/wordlist.txt")
	{
		if (file_exists($filename))
		{
			$fstream = fopen($filename, "r");
			while ($word = fscanf($fstream, "%s %s %s %s %s %s %s %s %s %s\n")) {

				$phrase = "";

				if (is_string($word[0]))
				{
					foreach ($word as $value)
						$phrase .= $value . " ";

					array_push($this->wordList, trim($phrase));
				}
			}
		}
	}
	
	/**
	* Propósito: devolver la imagen que debería mostrarse con este número de conjeturas incorrectas
	* Condiciones previas: ninguna
	* Condiciones posteriores: imagen devuelta
	**/
	function picture()
	{
		$count = 1;

		for ($i = 100; $i >= 0; $i-= ceil(100/$this->guesses))
		{
			if ($this->health == $i)
			{
				if (file_exists("images/" . ($count-1) . ".jpg"))
					return "<img src=\"images/" . ($count-1) . ".jpg\" alt=\"Hangman\" title=\"Hangman\" />";
				else
					return "ERROR: images/" . ($count-1) . ".jpg is missing from the hangman images folder.";
			}
				
			$count++;
		}
		
		return "<img src=\"images/" . ($count-1) . ".jpg\" alt=\"Hangman\" title=\"Hangman\" />";
	}
	
	/**
	* Propósito: mostrar la parte de la palabra que han resuelto hasta ahora
	* Condiciones previas: se ha establecido una palabra usando setWord()
	* Condiciones posteriores: las letras que han adivinado correctamente aparecen
	**/
	function solvedWord()
	{

		$result = "";
		
		for ($i = 0; $i < count($this->wordLetters); $i++)
		{
			$found = false;
			
			foreach($this->letters as $letter) {
				if ($letter == $this->wordLetters[$i])
				{
					$result .= $this->wordLetters[$i]; //Ell@s han adivinado esta carta
					$found = true;
				}
			}
			
			if (!$found && $this->isLetter($this->wordLetters[$i]))
				$result .= "_"; //Ell@s no han adivinado esta carta
				
			else if (!$found) //este es un espacio o carácter no alfabético
			{
				//hacer que los espacios sean más notorios
				if ($this->wordLetters[$i] == " ")
					$result .= "&nbsp;&nbsp;&nbsp;";
				else
					$result .= $this->wordLetters[$i];
			}
		}
	
		return $result;
	}
	
	/**
	* Propósito: convertir la palabra seleccionada en una matriz
	* Condiciones previas: se ha seleccionado una palabra
	* Condiciones posteriores: wordLetters ahora contiene una representación de matriz de la
	* palabra seleccionada
	**/
	function wordToArray()
	{
		$this->wordLetters = array(); 
		
		for ($i = 0; $i < strlen($this->wordList[$this->wordIndex]); $i++)
			array_push($this->wordLetters, $this->wordList[$this->wordIndex][$i]);
	}
	
	/**
	* Propósito: verificar si este valor es una letra
	* Condiciones previas: valor a comprobar
	* Condiciones posteriores: devuelve verdadero si se encuentra la letra
	**/
	function isLetter($value)
	{
		if (in_array($value, $this->alphabet))
			return true;
			
		return false;
	}
}

?>