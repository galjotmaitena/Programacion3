<?php

use Galjot\Maitena\NeumaticoBD;

require_once "./clases/neumaticoBD.php";

/* mostrarFotosDeModificados.php: Muestra (en una tabla HTML) todas las imagenes (50px X 50px) de los
neumáticos registrados en el directorio “./neumaticosModificados/”. Para ello, agregar un método estático (en
NeumaticoBD), llamado mostrarModificados */

echo NeumaticoBD::mostrarModificados();