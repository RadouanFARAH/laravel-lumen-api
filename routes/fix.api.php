<?php

	$router->group(["prefix" => 'v1'], function() use ($router) {

		$router->group(["prefix" => 'auth'], function() use ($router) {
			
			// Retourne la liste des reservations de l'utilisateur connecté
			$router->get("booking/{type}", 'FixController@myBooking');

			// Retourne l'utilisateur courant
			$router->get("user/{id}", 'FixController@user');

			// change le mot de passe
			$router->get("password", 'FixController@password');

			// change le mot de passe
			$router->post("custom/search", 'FixController@search');

		});
	});
?>