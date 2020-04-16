<?php
use siap\auth\models\Autenticador;
use siap\home\models\Menu;

$auth = function ($request, $response, $next) {
    $aut = Autenticador::instanciar();
  
    if (!$aut->logado())
    {
        $url = $this->router->pathFor('login');
        return $response->withRedirect($url);
    }
    
    $twig = $this->get('renderer')->getEnvironment();
    $menus_pais = Menu::getPaiByPrivilegio($aut->getUsuarioRol());
      
    $twig->addGlobal('current_user', $aut);
    $twig->addGlobal('menus_pais', $menus_pais);
    $twig->addGlobal('inicio_current_ano', date('Y-01-01'));
    $response = $next($request, $response);
    return $response;
};