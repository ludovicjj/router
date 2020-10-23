# Router

Création d'un router en PHP

## Features

* Ajouter des routes au router
* Récupérer une route par son nom
* Récupérer une route par son itinéraire
* Définir l'action d'une route et prise en charge d'une des actions suivantes :
    - Methode d'une classe
    - fonction anonyme
* Gestion des variables dans l'itinéraire des routes
* Gestion des variables avec des valeurs par défaut dans l'itinéraire des routes

## Missing

* Prendre en charge la methode ```__invoke()```
* Faire le lien avec la PSR7
* Implémenter les méthodes HTTP (GET, POST, PUT...)

## Exemple

#### Ajouter une route

Le code ci-dessous ajoute une route au router. Pour cela la route a besoin respectivement d'un nom, d'un itinéraire et 
d'un callable (action). Une exception du type ```RouteAlreadyExistException``` sera lancée si vous ajouter deux routes
avec un nom identique.

```
$router = new Router();
$myRoute = new Route("home", "/", function () {
    return "hello world !"
});

$router->add($route);
```

#### Récupérer une route par son nom

Le code ci-dessous permet de récupérer une route par son nom. Une exception du type ```RouteNotFoundException``` 
sera lancée si aucune route n'est trouvée.

```
$router = new Router();
$myRoute = new Route("home", "/", function () {
    return "hello world !"
});

$router->add($route);

$route = $router->get("home");
```

#### Récupérer une route par son itinéraire

Le code ci-dessous permet de récupérer une route par son itinéraire. Une exception du type ```RouteNotFoundException``` 
sera lancée si aucune route n'est trouvée.

```
$router = new Router();
$myRoute = new Route("home", "/", function () {
    return "hello world !"
});

$router->add($route);

$route = $router->match("/");
```

#### Action

Dans les exemples précédents, l'action de la route était une fonction anonyme. Il est possible d'utiliser la
méthode d'une classe comme action. Changez le dernier paramètres de la route par un tableau contenant le nom de
la classe et la méthode à appeler.

```
$router = new Router();
$myRoute = new Route("home", "/", [HomeController::class, 'index']);
$router->add($myRoute);
```

Pour lancer l'action de la route, utiliser la méthode ```call($path)``` du router. En arrière-plan, cette méthode
va vérifier si une route correspond au path donné et exécute l'action. Si aucune route ne correspond, une exception 
du type ```RouteNotFoundException``` sera lancée.

```
$router = new Router();
$myRoute = new Route("home", "/", [HomeController::class, 'index']);
$router->add($myRoute);

$router->call("/");
```

#### Itinéraire avec variables

Il est possible de définir une route avec un itinéraire contenant des portions variables. Pour cela encapsulez ses 
portions avec des ```{...}``` et donnez leur un nom unique. 

Exécutez l'action de la route en indiquant une valeur pour chaque variable dans l'itinéraire. 
Si une variable n'est pas définie une exception de type ```RouteNotFoundException``` sera lancée.

Pour récupérer les variables de la route dans l'action, il suffit de les déclarer comme arguments de l'action.
Vous pouvez déclarer les arguments dans l'ordre que vous souhaitez.
Les arguments de l'action doivent avoir les mêmes noms que ceux définis dans l'itinéraire. 

```
$router = new Router();
$myRoute = new Route("posts", "/posts/{id}/{slug}", function(string $slug, string $id) {
    return sprintf('%s : %s', $id, $slug);
});

$router->add($myRoute);

// return "5 : my-slug"
$router->call("/posts/5/my-slug");

```

#### Itinéraire avec variables ayant une valeur par défaut

Il est également possible de définir des routes dont les variables auront une valeur par 
défaut si elles ne sont pas précisées lors de l'appel de l'action.

```
$router = new Router();
$myRoute = new Route("posts", "/posts/page/{page}", function (string $page) {
    return sprintf('Current page is %s', $page);
});
$rmyRoute->addDefault('page', 1);
$router->add($myRoute);

// return "Current page is 1"
$router->call("/posts/page");
```

## Test

Pour lancer les tests utiliser la commande suivante :

```vendor/bin/phpunit```

La couverture de code sera généré et placer dans un fichier ```/coverage-html``` à la racine du projet.