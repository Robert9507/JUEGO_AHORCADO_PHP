<?php

class game {

	var $health;	
	var $over;		
	var $score;		
	var $won;		

	/**
	* Propósito: configurar las variables de entorno del juego
	* Condiciones previas: activar el depurador (opcional)
	* Condiciones posteriores: el entorno del juego está listo para comenzar un juego
	**/
	function start()
	{
		$this->score = 0;
		$this->health = 100;
		$this->over = false;
		$this->won = false;
	}
	
	/**
	* Propósito: terminar el juego
	* Condiciones previas: activa el juego sobre la bandera
	* Condiciones posteriores: la bandera de fin de juego es verdadera
	**/
	function end()
	{
		$this->over = true;
	}
	
	/**
	* Propósito: cambiar o recuperar la puntuación del jugador
	* Condiciones previas: cantidad (opcional)
	* Condiciones posteriores: devuelve la puntuación actualizada del jugador
	**/
	function setScore($amount = 0)
	{
		return $this->score += $amount;
	}
	
	/**
	* Propósito: cambiar o recuperar la vida del jugador
	* Condiciones previas: cantidad (opcional)
	* Condiciones posteriores: devuelve la salud actualizada del jugador
	**/
	function setHealth($amount = 0)
	{			
		return ceil($this->health += $amount);
	}
	
	/**
	* Propósito: devolver bool para indicar si el juego ha terminado o no
	* Condiciones previas: ninguna
	* Postcondiciones: devuelve true o flase
	**/
	function isOver()
	{
		if ($this->won)
			return true;
			
		if ($this->over)
			return true;
			
		if ($this->health < 0) 
			return true;
			
		return false;
	}

} //clase de juego final

/**
* Propósito: mostrar un mensaje de depuración formateado
* Condiciones previas: el objeto o mensaje a mostrar
* Condiciones posteriores: devuelve la puntuación actualizada del jugador
**/
function debug($object = NULL, $msg = "")
{
	if (isset($object) || isset($msg))
	{
		echo "<pre>";
		
		if (isset($object))
			print_r($object);
			
		if (isset($msg))
			echo "\n\t$msg\n";
		
		echo "\n</pre>";
	}
}

/**
* Propósito: devolver un mensaje de error formateado
* Condiciones previas: el mensaje a formatear
* Condiciones posteriores: se devuelve el mensaje formateado
**/
function errorMsg($msg)
{
	return "<div class=\"errorMsg\">$msg</div>";
}

/**
* Propósito: devolver un mensaje de éxito formateado
* Condiciones previas: el mensaje a formatear
* Condiciones posteriores: se devuelve el mensaje formateado
**/
function successMsg($msg)
{
	return "<div class=\"successMsg\">$msg</div>";
}