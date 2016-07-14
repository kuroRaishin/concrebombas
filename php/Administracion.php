<?php require_once('../Connections/conexion.php'); ?>
<?php
//initialize the session
if (!isset($_SESSION)) {
  session_start();
}

// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session varialbles
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);
	
  $logoutGoTo = "../index.php";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}
?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_empleados = 10;
$pageNum_empleados = 0;
if (isset($_GET['pageNum_empleados'])) {
  $pageNum_empleados = $_GET['pageNum_empleados'];
}
$startRow_empleados = $pageNum_empleados * $maxRows_empleados;

mysql_select_db($database_conexion, $conexion);
$query_empleados = "SELECT * FROM empleado ORDER BY empleado.Apellido";
$query_limit_empleados = sprintf("%s LIMIT %d, %d", $query_empleados, $startRow_empleados, $maxRows_empleados);
$empleados = mysql_query($query_limit_empleados, $conexion) or die(mysql_error());
$row_empleados = mysql_fetch_assoc($empleados);

if (isset($_GET['totalRows_empleados'])) {
  $totalRows_empleados = $_GET['totalRows_empleados'];
} else {
  $all_empleados = mysql_query($query_empleados);
  $totalRows_empleados = mysql_num_rows($all_empleados);
}
$totalPages_empleados = ceil($totalRows_empleados/$maxRows_empleados)-1;

$maxRows_cliente = 10;
$pageNum_cliente = 0;
if (isset($_GET['pageNum_cliente'])) {
  $pageNum_cliente = $_GET['pageNum_cliente'];
}
$startRow_cliente = $pageNum_cliente * $maxRows_cliente;

mysql_select_db($database_conexion, $conexion);
$query_cliente = "SELECT * FROM cliente ORDER BY cliente.NombreEmpresa";
$query_limit_cliente = sprintf("%s LIMIT %d, %d", $query_cliente, $startRow_cliente, $maxRows_cliente);
$cliente = mysql_query($query_limit_cliente, $conexion) or die(mysql_error());
$row_cliente = mysql_fetch_assoc($cliente);

if (isset($_GET['totalRows_cliente'])) {
  $totalRows_cliente = $_GET['totalRows_cliente'];
} else {
  $all_cliente = mysql_query($query_cliente);
  $totalRows_cliente = mysql_num_rows($all_cliente);
}
$totalPages_cliente = ceil($totalRows_cliente/$maxRows_cliente)-1;

mysql_select_db($database_conexion, $conexion);
$query_productos1 = "SELECT * FROM productos WHERE productos.idproducto<0 ORDER BY productos.idproducto DESC";
$productos1 = mysql_query($query_productos1, $conexion) or die(mysql_error());
$row_productos1 = mysql_fetch_assoc($productos1);
$totalRows_productos1 = mysql_num_rows($productos1);

mysql_select_db($database_conexion, $conexion);
$query_bombas1 = "SELECT * FROM bombas ORDER BY bombas.idBombas";
$bombas1 = mysql_query($query_bombas1, $conexion) or die(mysql_error());
$row_bombas1 = mysql_fetch_assoc($bombas1);
$totalRows_bombas1 = mysql_num_rows($bombas1);

$queryString_cliente = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_cliente") == false && 
        stristr($param, "totalRows_cliente") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_cliente = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_cliente = sprintf("&totalRows_cliente=%d%s", $totalRows_cliente, $queryString_cliente);

$queryString_empleados = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_empleados") == false && 
        stristr($param, "totalRows_empleados") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_empleados = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_empleados = sprintf("&totalRows_empleados=%d%s", $totalRows_empleados, $queryString_empleados);
?>
<!DOCTYPE html>

<html lang="es"> 
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Concrebombas</title>
        <!-- Load Roboto font -->
        <link href='http://fonts.googleapis.com/css?family=Roboto:400,300,700&amp;subset=latin,latin-ext' rel='stylesheet' type='text/css'>
        <!-- Load css styles -->
        <link rel="stylesheet" type="text/css" href="../css/bootstrap.css" />
        <link rel="stylesheet" type="text/css" href="../css/bootstrap-responsive.css" />
        <link rel="stylesheet" type="text/css" href="../css/style.css" />
        <link rel="stylesheet" type="text/css" href="../css/pluton.css" />
        <link rel="stylesheet" type="text/css" href="../css/table.css" />
        <!--[if IE 7]>
            <link rel="stylesheet" type="text/css" href="css/pluton-ie7.css" />
        <![endif]-->
        <link rel="stylesheet" type="text/css" href="../css/jquery.cslider.css" />
        <link rel="stylesheet" type="text/css" href="../css/jquery.bxslider.css" />
        <link rel="stylesheet" type="text/css" href="../css/animate.css" />
        <!-- Fav and touch icons -->
        <link rel="apple-touch-icon-precomposed" sizes="144x144" href="../images/ico/apple-touch-icon-144.png">
        <link rel="apple-touch-icon-precomposed" sizes="114x114" href="../images/ico/apple-touch-icon-114.png">
        <link rel="apple-touch-icon-precomposed" sizes="72x72" href="../images/apple-touch-icon-72.png">
        <link rel="apple-touch-icon-precomposed" href="../images/ico/apple-touch-icon-57.png">
        <link rel="shortcut icon" href="../images/ico/favicon.ico.bmp">
        <style type="text/css">
        body,td,th {
	font-family: Roboto, sans-serif;
	font-size: 16px;
	font-weight: bold;
}
        </style>
</head>
    
    <body>
        <div class="navbar">
            <div class="navbar-inner">
                <div class="container">
                    <p align="left"><a href="#" class="brand">
                        <img  src="../Imagenes/LOGONAME.png" width="128" height="49" />
                        <!-- This is website logo -->
                    </a></p>
                    <h1 align="center">Panel de control del Administrador</h1>
                    <!-- Navigation button, visible on small resolution -->
                    </p>
                    <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                        <i class="icon-menu"></i>
                  </button>
                    <!-- Main navigation -->
                    <div class="nav-collapse collapse pull-right">
                        <ul class="nav" id="top-navigation">
                            <li class="active"><a href="#home">Home</a></li>
                            <li><a href="#event">Eventos</a></li>
                            <li><a href="#product">Productos</a></li>
                            <li><a href="#employe">Empleados</a></li>
                            <li><a href="#clients">Clientes</a></li>
                            <li><a href="#contact">Servicio Tecnico</a></li>
                            <li><a href="<?php echo $logoutAction ?>">Cerrar Sesion</a></li>
                      </ul>
                  </div>
                    <!-- End main navigation -->
              </div>
            </div>
        </div>
        <!-- Start home section -->
        <div id="home">
          <div class="about-text"><img src="../Imagenes/concrefont.png">bienvenido</div>
        </div>
            </div>
        </div>
        <!-- End home section -->
        <!-- Event section start -->
        <div class="section primary-section" id="event">
            <div class="container">
                <!-- Start title section -->
                <div class="title">
                    <h1>&nbsp;</h1>
                    <h1>Editar Eventos</h1>
                    <!-- Section's title goes here -->
                  <p>En esta seccion puede revisar, editar, eliminar e ingresar nuevas agendaciones.</p>
                    <!--Simple description for section goes here. -->
                </div>
                <div class="row-fluid">
                    <div class="span4"></div>
                    <div class="span4"></div>
                    <div class="span4" ></div>
                  <div align="center">
                    <?php
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/error_log.txt');
error_reporting(0);
include("calendario/config.inc.php");
$mostrar="";
function fecha ($valor)
	{
	$timer = explode(" ",$valor);
	$fecha = explode("-",$timer[0]);
	$fechex = $fecha[2]."/".$fecha[1]."/".$fecha[0];
				return $fechex;
			}
			if (isset($_POST["guardarevento"])=="Si")
			{
				$q1="insert into reservacion (fecha,descripcion) values ('".$_POST["fecha"]."','".strip_tags($_POST["titulo"])."')";
				mysql_select_db($dbname);
				if ($r1=mysql_query($q1)) $mostrar="<p class='ok' id='mensaje'>la bomba se a reservado correctamente.</p>";
				else $mostrar= "<p class='error' id='mensaje'>Se ha producido un error con su reservación.</p>";
			}
			if (isset($_GET["borrarevento"]))
			{
				$q1="delete from reservacion where id='".$_GET["borrarevento"]."' limit 1";
				mysql_select_db($dbname);
				if ($r1=mysql_query($q1)) $mostrar="<p class='ok' id='mensaje'>su reservacion a sido cancelada correctamente.</p>";
				else $mostrar="<p class='error' id='mensaje'>Se ha producido un error al cancelar su reserva.</p>";
			}
			
			if (isset($_POST["addevent"])=="Si")
			{
				
				$q1="insert into reservacion (fecha,descripcion) values ('".$_POST["fechas"]."','".$_POST["titulos"]."')";
				mysql_select_db($dbname);
				if ($r1=mysql_query($q1)) $mostrar="<p class='ok' id='mensaje'>reserva guardada correctamente.</p>";
				else $mostrar="<p class='error' id='mensaje'>Se ha producido un error guardando su reserva.</p>";
			}
			
			if (!isset($_GET["fecha"])) 
			{
				$mesactual=intval(date("m"));
				if ($mesactual<10) $elmes="0".$mesactual;
				else $elmes=$mesactual;
				$elanio=date("Y");
			} 
			else 
			{
				$cortefecha=explode("-",$_GET["fecha"]);
				$mesactual=intval($cortefecha[1]);
				if ($mesactual<10) $elmes="0".$mesactual;
				else $elmes=$mesactual;
				$elanio=$cortefecha[0];
			}
			
			$primeromes=date("N",mktime(0,0,0,$mesactual,1,$elanio));
			
			if (!isset($_GET["mes"])) $hoy=date("Y-m-d"); 
			else $hoy=$_GET["ano"]."-".$_GET["mes"]."-01";
			
			if (($elanio % 4 == 0) && (($elanio % 100 != 0) || ($elanio % 400 == 0))) $dias=array("","31","29","31","30","31","30","31","31","30","31","30","31");
			else $dias=array("","31","28","31","30","31","30","31","31","30","31","30","31");
			
			$ides=array();
			$eventos=array();
			$titulos=array();
			
			$q1="select * from reservacion where month(fecha)='".$elmes."' and year(fecha)='".$elanio."'";
			mysql_select_db($dbname);
			$r1=mysql_query($q1);
			if ($f1=mysql_fetch_array($r1))
			{
				$h=0;
				do
				{
					$ides[$h]=$f1["id"];
					$eventos[$h]=$f1["fecha"];
					$titulos[$h]=$f1["descripcion"];
					$h+=1;
				}
				while($f1=mysql_fetch_array($r1));
			}
			$meses=array("","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
			$diasantes=$primeromes-1;
			$diasdespues=42;
			$tope=$dias[$mesactual]+$diasantes;
			if ($tope%7!=0) $totalfilas=intval(($tope/7)+1);
			else $totalfilas=intval(($tope/7));
			echo "<h2>Calendario de reservas para: ".$meses[$mesactual]." de ".$elanio."</h2>";
			echo $mostrar;
			echo "<script>function mostrar(cual) {if (document.getElementById(cual).style.display=='block') {document.getElementById(cual).style.display='none';} else {document.getElementById(cual).style.display='block'} }</script>";
			echo "<table class='calendario' cellspacing='0' cellpadding='0'>";
			echo "<tr><th>L</th><th>M</th><th>M</th><th>J</th><th>V</th><th>S</th><th>D</th></tr><tr>";
			$j=1;
			$filita=0;
			function buscarevento($fecha,$eventos,$titulos)
			{
				$clave=array_search($fecha,$eventos,true);
				return $titulos[$clave];
			}
			for ($i=1;$i<=$diasdespues;$i++)
			{
				if ($filita<$totalfilas)
				{
				if ($i>=$primeromes && $i<=$tope) 
				{
					echo "<td";
					if ($j<10) $dd="0".$j;else $dd=$j;
					$compuesta=$elanio."-$elmes-$dd";
					if (count($eventos)>0 && in_array($compuesta,$eventos,true)) {echo " class=' evento";$noagregar=true;}
					else {echo " class='activa";$noagregar=false;}
					if ($hoy==$compuesta) echo " hoy";
					if ($noagregar==false) echo "'>$j<a href='javascript:mostrar(\"evento$j\")' title='Crear una reservación el ".fecha($compuesta)."' class='vtip'><img src='add.png' /></a><form id='evento$j' method='post' action='".$_SERVER["PHP_SELF"]."' style='display:none'><input type='text' name='titulo' class='text' /><input type='Submit' name='Enviar' value='Guardar' class='enviar' /><input type='hidden' name='guardarevento' value='Si' /><input type='hidden' name='fecha' value='$compuesta' /></form>";
					else echo "'>$j<a href='javascript:mostrar(\"evento$j\")' title='Agregar una reservación el ".fecha($compuesta)."' class='vtip'><img src='add.png' /></a><form id='evento$j' method='post' action='".$_SERVER["PHP_SELF"]."' style='display:none'><input type='text' name='titulos' class='text' /><input type='Submit' name='Enviar' value='Guardar' class='enviar' /><input type='hidden' name='addevent' value='Si' /><input type='hidden' name='fechas' value='$compuesta' /></form>";
					
					$sqlevent="select * from reservacion where fecha='".$compuesta."' order by id";
					mysql_select_db($dbname);
					$revent=mysql_query($sqlevent);
					while($rowevent=mysql_fetch_array($revent))
					{
						echo "<p>$rowevent[descripcion]<a href='".$_SERVER["PHP_SELF"]."?borrarevento=".$rowevent["id"]."' onClick=\"return confirm('&iquest;Confirmas la eliminaci&oacute;n de la reservación?')\" title='Eliminar esta reservación del ".fecha($compuesta)."' class='vtip'><img src='delete.png' /></a></p>";
					}
					
					echo "</td>";
					$j+=1;
				}
				else echo "<td class='desactivada'>&nbsp;</td>";
				if ($i==7 || $i==14 || $i==21 || $i==28 || $i==35 || $i==42) {echo "<tr>";$filita+=1;}
				}
			}
			echo "</table>";
			$mesanterior=date("Y-m-d",mktime(0,0,0,$mesactual-1,01,$elanio));
			$messiguiente=date("Y-m-d",mktime(0,0,0,$mesactual+1,01,$elanio));
			echo "<p>&laquo; <a href='".$_SERVER["PHP_SELF"]."?fecha=$mesanterior'>Mes Anterior</a> - <a href='".$_SERVER["PHP_SELF"]."?fecha=$messiguiente'>Mes Siguiente</a> &raquo;</p>";
			?>
                  </div>
                </div>
            </div>
        </div>
        <!-- Service section end -->
        <!-- Portfolio section start -->
    <div class="section secondary-section " id="product">
            <div class="triangle"></div>
      <div class="title">
            <h1>Lista de productos de Portafolio</h1>
            <p>Aqui puede editar la información de los productos y servicios que se pueden ver en la pagina principal.</p>
        </div>
      <div align="center">
      <table width="200%" border="1">
  <tr>
  	<th width="56" scope="col">Imagen</th>
    <th width="188" scope="col">ID</th>
    <th width="213" scope="col">Titulo</th>
    <th width="236" scope="col">Nombreo</th>
    <th width="224" scope="col">Precio</th>
    <th width="257" scope="col">Referencia</th>
    <th width="397" scope="col">Descripción</th>
    <th width="397" scope="col">Edición</th>
    
 
  </tr>
  <?php do { ?>
    <tr>
      <td><img src="<?php echo $row_productos1['imagen']; ?>" alt="product" width="58" height="51"></td>
      <td><?php echo $row_productos1['idproducto']; ?></td>
      <td><?php echo $row_productos1['tituloProducto']; ?></td>
      <td><?php echo $row_productos1['NombreProducto']; ?></td>
      <td><?php echo $row_productos1['PrecioProducto']; ?></td>
      <td><?php echo $row_productos1['ReferenciaProducto']; ?></td>
      <td><?php echo $row_productos1['DescripcionProducto']; ?></td>
      <td><a href="EditProducts.php?recordID=<?php echo $row_productos1['idproducto']; ?>">Editar</a></td>
    </tr>
    <?php } while ($row_productos1 = mysql_fetch_assoc($productos1)); ?>
    <?php do { ?>
  <tr>
    <td><img src="<?php echo $row_bombas1['ImagenBomba']; ?>" alt="bomb"></td>
    <td><?php echo $row_bombas1['idBombas']; ?></td>
    <td><?php echo $row_bombas1['Titulo']; ?></td>
    <td><?php echo $row_bombas1['Nombre']; ?></td>
    <td><?php echo $row_bombas1['PrecioBomba']; ?></td>
    <td><?php echo $row_bombas1['ReferenciaBomba']; ?></td>
    <td><?php echo $row_bombas1['Descripcion']; ?></td>
    <td><a href="EditBombs.php?recordID=<?php echo $row_bombas1['idBombas']; ?>">Editar</a></td>
  </tr>
  <?php } while ($row_bombas1 = mysql_fetch_assoc($bombas1)); ?>
          </table>
	 </div>
    </div>
        <!-- Product section end -->
        <!-- Employe section start -->
        <div class="section primary-section" id="employe">
            <div class="triangle"></div>
            <div class="container">
                <div class="title">
                    <h1>Lista de Empleados</h1>
                    <p>Aqui encuentra la lista de empleados, puede revisar los empleados, crear nuevo o inabilitar</p>
                </div>
                
 <div class="container">
<div align="center">
<table width="1461" border="1">
  <tr>
    <th width="76" scope="col"><div align="left">Nombre</div></th>
    <th width="77" scope="col"><div align="left">Apellido</div></th>
    <th width="102" scope="col"><div align="left">Tipo documento</div></th>
    <th width="118" scope="col"><div align="left">Numero Documento</div></th>
    <th width="81" scope="col"><div align="left">Telefono</div></th>
    <th width="53" scope="col"><div align="left">Movil</div></th>
    <th width="42" scope="col"><div align="left">Email</div></th>
    <th width="61" scope="col"><div align="left">Cargo</div></th>
    <th width="61" scope="col">Acciones</th>
  </tr>
  <?php do { ?>
    <tr>
      <td><?php echo $row_empleados['Nombre']; ?></td>
      <td><?php echo $row_empleados['Apellido']; ?></td>
      <td><?php echo $row_empleados['Identificacion']; ?></td>
      <td><?php echo $row_empleados['NumeroIdentificacion']; ?></td>
      <td><?php echo $row_empleados['Telefono']; ?></td>
      <td><?php echo $row_empleados['Movil']; ?></td>
      <td><?php echo $row_empleados['Email']; ?></td>
      <td><?php echo $row_empleados['Cargo']; ?></td>
      <td><a href="EditEmployee.php?recordID=<?php echo $row_empleados['IDempleado']; ?>">Editar</a></td>
    </tr>
    <?php } while ($row_empleados = mysql_fetch_assoc($empleados)); ?>
</table>
<p><a href="<?php printf("%s?pageNum_empleados=%d%s", $currentPage, 0, $queryString_empleados); ?>" class="button-ps">Inicio</a> | <a href="<?php printf("%s?pageNum_empleados=%d%s", $currentPage, max(0, $pageNum_empleados - 1), $queryString_empleados); ?>" class="button-ps">&lt;&lt;</a> | <a href="<?php printf("%s?pageNum_empleados=%d%s", $currentPage, min($totalPages_empleados, $pageNum_empleados + 1), $queryString_empleados); ?>" class="button-ps">&gt;&gt;</a> | <a href="<?php printf("%s?pageNum_empleados=%d%s", $currentPage, $totalPages_empleados, $queryString_empleados); ?>" class="button-ps">Ultima</a></p>

</div>
 </div>
            </div>
        </div>
        <!-- Employe section end -->
        <div class="section secondary-section">
            <div class="triangle"></div>
            <div class="container centered">
                <p class="large-text">La capacidad de vender, de comunicarse con otro ser humano, cliente, empleado, jefe, esposa o hijo, construye la base del exito personal. Las habilidades de comunicación como escribir, hablar, negociar son fundametales para una vida exitosa- <em>Robert kyosaki.</em></p>
         
            </div>
        </div>
        <!-- Client section start -->
        <div id="clients">
            <div class="section primary-section">
                <div class="triangle"></div>
              <div class="container">
                <div class="title">
                        <h1>Listado Clientes</h1>
                        <p>Revisa la lista de clientes, ingresa nuevos o elimina existentes</p>
                </div>
              </div>
              <div align="center">
                <table width="200" border="1">
                  <tr>
                    <th scope="col"><div align="center">Nombre</div></th>
                    <th scope="col"><div align="center">Empresa</div></th>
                    <th scope="col"><div align="center">Domicilio</div></th>
                    <th scope="col"><div align="center">Almacen</div></th>
                    <th scope="col"><div align="center">Telefono</div></th>
                    <th scope="col"><div align="center">Movil</div></th>
                    <th scope="col"><div align="center">Fax</div></th>
                    <th scope="col"><div align="center">Email</div></th>
                    <th scope="col">Acciones</th>
                  </tr>
                  <?php do { ?>
                    <tr>
                      <td><div align="center"><?php echo $row_cliente['NombreCliente']; ?></div></td>
                      <td><div align="center"><?php echo $row_cliente['NombreEmpresa']; ?></div></td>
                      <td><div align="center"><?php echo $row_cliente['Domicilio']; ?></div></td>
                      <td><div align="center"><?php echo $row_cliente['Almacen']; ?></div></td>
                      <td><div align="center"><?php echo $row_cliente['Telefono']; ?></div></td>
                      <td><div align="center"><?php echo $row_cliente['Movil']; ?></div></td>
                      <td><div align="center"><?php echo $row_cliente['Fax']; ?></div></td>
                      <td><div align="center"><?php echo $row_cliente['Email']; ?></div></td>
                      <td><a href="EditClient.php?recordID=<?php echo $row_cliente['IDcliente']; ?>">Editar</a></td>
                    </tr>
                    <?php } while ($row_cliente = mysql_fetch_assoc($cliente)); ?>
                </table>
                <p><a href="<?php printf("%s?pageNum_cliente=%d%s", $currentPage, 0, $queryString_cliente); ?>" class="button-ps">Inicio</a> | <a href="<?php printf("%s?pageNum_cliente=%d%s", $currentPage, max(0, $pageNum_cliente - 1), $queryString_cliente); ?>" class="button-ps">&lt;&lt;</a> | <a href="<?php printf("%s?pageNum_cliente=%d%s", $currentPage, min($totalPages_cliente, $pageNum_cliente + 1), $queryString_cliente); ?>" class="button-ps">&gt;&gt;</a> | <a href="<?php printf("%s?pageNum_cliente=%d%s", $currentPage, $totalPages_cliente, $queryString_cliente); ?>" class="button-ps">Ultima</a></p>
              </div>
        </div>
    <!-- Client section end -->   
    <!-- Contact section start -->
        <div id="contact" class="contact">
            <div class="section secondary-section">
                <div class="container">
                    <div class="title">
                        <h1>Contacta con los Programadores</h1>
                        <p>En caso de nesecitar algun cambio no especificado en este panel de Control, contactalos en: </p>
                    </div>
                </div>
                <div class="container">
                    <div class="span9 center contact-info">
                        <p>Av. 1ra de Mayo N° 13-68, BOGOTÁ, Colombia</p>
                        <p class="info-mail">lmorjuela12@misena.edu.co</p>
                        <p>+57 1 3016596928</p>
                        <p>+57 1 3107840614</p>
                        <div class="title">
                            <h3>Go social!</h3>
                            <p>O contactalos por sus redes sociales</p>
                        </div>
                  </div>
                    <div class="row-fluid centered">
                        <ul class="social">
                            <li>
                                <a href="https://www.facebook.com/public/Luis-Miguel-Orjuela-Alfonso">
                                    <span class="icon-facebook-circled"></span>
                                </a>
                            </li>
                            <li>
                                <a href="https://twitter.com/kuro_raishin">
                                    <span class="icon-twitter-circled"></span>
                                </a>
                            </li>
                            <li>
                                <a href="https://www.linkedin.com/m/me">
                                    <span class="icon-linkedin-circled"></span>
                                </a>
                            </li>                             
                            <li>
                                <a href="https://plus.google.com/u/0/111601287366163235900/about">
                                    <span class="icon-gplus-circled"></span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- Contact section edn -->
        <!-- Footer section start -->
        <div class="footer">
            <p>&copy; PIERRE MAGIQUE Developers<br>
            SENA 2016</p>
        </div>
        <!-- Footer section end -->
        <!-- ScrollUp button start -->
        <div class="scrollup">
            <a href="#">
                <i class="icon-up-open"></i>
            </a>
        </div>
        <!-- ScrollUp button end -->
        <!-- Include javascript -->
    <script src="../js/jquery.js"></script>
    <script type="text/javascript" src="../js/jquery.mixitup.js"></script>
    <script type="text/javascript" src="../js/bootstrap.js"></script>
    <script type="text/javascript" src="../js/modernizr.custom.js"></script>
    <script type="text/javascript" src="../js/jquery.bxslider.js"></script>
    <script type="text/javascript" src="../js/jquery.cslider.js"></script>
    <script type="text/javascript" src="../js/jquery.placeholder.js"></script>
    <script type="text/javascript" src="../js/jquery.inview.js"></script>

		
        <!-- Load google maps api and call initializeMap function defined in app.js -->
    <script async defer type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false&callback=initializeMap"></script>
        <!-- css3-mediaqueries.js for IE8 or older -->
        <!--[if lt IE 9]>
            <script src="js/respond.min.js"></script>
        <![endif]-->
   <script type="text/javascript" src="../js/app.js"></script>
</body>
</html>
<?php
mysql_free_result($empleados);

mysql_free_result($cliente);

mysql_free_result($productos1);

mysql_free_result($bombas1);
?>
