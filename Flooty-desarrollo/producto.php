<?php
session_start();
error_reporting(E_ALL);
ini_set("display_errors", 1);
require 'sesion/conexion.php';

/* SESIÓN */
$esta_logueado = false;
$user_name = "";
$user_rol = "";
$id_usuario = 0;
$favoritos_usuario = [];

if (
    isset($_SESSION["usuario"]) &&
    isset($_SESSION["rol"]) &&
    ($_SESSION["rol"] == "usuario" || $_SESSION["rol"] == "admin")
) {
    $esta_logueado = true;
    $usuario_sesion = $_SESSION["usuario"];
    $usuario_sesion_seguro = $_conexion->real_escape_string($usuario_sesion);
    $consulta_usuario = "SELECT * FROM usuarios WHERE usuario = '$usuario_sesion_seguro' LIMIT 1";
    $resultado_usuario = $_conexion->query($consulta_usuario);
    if ($resultado_usuario && $resultado_usuario->num_rows > 0) {
        $user_info = $resultado_usuario->fetch_assoc();
        $user_name   = $user_info["usuario"];
        $user_rol    = $user_info["rol"];
        $id_usuario  = (int)$user_info["id"];
    } else {
        $esta_logueado = false;
    }
}

/* OBTENER PRODUCTO */
$id_producto = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
if ($id_producto <= 0) {
    header("Location: index.php");
    exit();
}

$consulta_producto = "
    SELECT p.*, c.nombre AS nombre_categoria, u.usuario AS nombre_vendedor
    FROM productos p
    LEFT JOIN categorias c ON p.id_categoria = c.id
    LEFT JOIN usuarios u ON p.id_usuario = u.id
    WHERE p.id = $id_producto
    LIMIT 1
";
$resultado_producto = $_conexion->query($consulta_producto);
if (!$resultado_producto || $resultado_producto->num_rows === 0) {
    header("Location: index.php");
    exit();
}
$producto = $resultado_producto->fetch_assoc();

/* FAVORITO */
if (isset($_GET["favorito"]) && $esta_logueado) {
    $consulta_fav = "SELECT * FROM favoritos WHERE id_usuario = $id_usuario AND id_producto = $id_producto";
    $resultado_fav = $_conexion->query($consulta_fav);
    if ($resultado_fav && $resultado_fav->num_rows > 0) {
        $_conexion->query("DELETE FROM favoritos WHERE id_usuario = $id_usuario AND id_producto = $id_producto");
    } else {
        $_conexion->query("INSERT INTO favoritos (id_usuario, id_producto) VALUES ($id_usuario, $id_producto)");
    }
    header("Location: producto.php?id=$id_producto");
    exit();
}

/* FAVORITOS DEL USUARIO */
if ($esta_logueado) {
    $consulta_favs = "SELECT id_producto FROM favoritos WHERE id_usuario = $id_usuario";
    $resultado_favs = $_conexion->query($consulta_favs);
    if ($resultado_favs && $resultado_favs->num_rows > 0) {
        while ($fila_fav = $resultado_favs->fetch_assoc()) {
            $favoritos_usuario[] = (int)$fila_fav["id_producto"];
        }
    }
}

$es_favorito = $esta_logueado && in_array($id_producto, $favoritos_usuario);

/* IMÁGENES */
$imagenes = json_decode($producto["imagenes"], true);
if (!is_array($imagenes) || count($imagenes) === 0) {
    $imagenes = ["img/default.png"];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($producto["titulo"]) ?> – Flooty</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, Helvetica, sans-serif; }

        body {
            min-height: 100vh;
            background-color: #f5f1e6;
            display: flex;
            flex-direction: column;
        }

        /* ── CONTENEDOR PRINCIPAL ── */
        .pagina-detalle {
            max-width: 1100px;
            margin: 30px auto;
            padding: 0 16px;
            flex: 1;
        }

        .btn-volver {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            color: #97B770;
            font-weight: bold;
            font-size: 15px;
            margin-bottom: 22px;
            transition: 0.2s;
        }
        .btn-volver:hover { color: #6f8f4e; }
        .btn-volver svg { width: 18px; height: 18px; }

        .detalle-grid {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 28px;
            align-items: start;
        }

        /* ── GALERÍA ── */
        .galeria-bloque {
            background: rgba(245,241,230,0.92);
            border-radius: 20px;
            border: 1px solid rgba(0,0,0,0.1);
            box-shadow: 0 10px 30px rgba(0,0,0,0.12);
            overflow: hidden;
        }

        .imagen-principal {
            width: 100%;
            height: 420px;
            overflow: hidden;
            background: #e0ddd5;
            position: relative;
        }
        .imagen-principal img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: opacity 0.35s ease;
        }

        /* flechas galería */
        .flecha-galeria {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255,255,255,0.82);
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            cursor: pointer;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            transition: 0.2s;
            z-index: 2;
            color: #555;
        }
        .flecha-galeria:hover { background: #97B770; color: white; }
        .flecha-izq { left: 12px; }
        .flecha-der { right: 12px; }
        .flecha-galeria.oculta { display: none; }

        .miniaturas {
            display: flex;
            gap: 10px;
            padding: 14px 18px;
            overflow-x: auto;
        }
        .miniatura {
            width: 72px;
            height: 72px;
            border-radius: 10px;
            overflow: hidden;
            cursor: pointer;
            border: 2px solid transparent;
            flex-shrink: 0;
            transition: border-color 0.2s;
        }
        .miniatura img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .miniatura.activa { border-color: #97B770; }

        /* info debajo de galería */
        .info-extra {
            display: flex;
            gap: 16px;
            padding: 16px 18px;
            border-top: 1px solid rgba(0,0,0,0.08);
            flex-wrap: wrap;
        }
        .tag-info {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: #555;
            background: rgba(151,183,112,0.12);
            padding: 5px 12px;
            border-radius: 20px;
        }
        .tag-info strong { color: #97B770; }

        /* ── PANEL DERECHO ── */
        .panel-derecho {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .card-info {
            background: rgba(245,241,230,0.92);
            border-radius: 20px;
            border: 1px solid rgba(0,0,0,0.1);
            box-shadow: 0 10px 30px rgba(0,0,0,0.12);
            padding: 24px;
        }

        .categoria-badge {
            display: inline-block;
            font-size: 12px;
            background-color: rgba(151,183,112,0.18);
            color: #6f8f4e;
            padding: 4px 12px;
            border-radius: 20px;
            margin-bottom: 12px;
            font-weight: 600;
        }

        .titulo-producto {
            font-size: 26px;
            font-weight: bold;
            color: #222;
            margin-bottom: 10px;
            line-height: 1.2;
        }

        .descripcion-producto {
            font-size: 15px;
            color: #555;
            line-height: 1.65;
            margin-bottom: 18px;
        }

        .precio-principal {
            font-size: 32px;
            font-weight: bold;
            color: #97B770;
        }
        .precio-principal span {
            font-size: 15px;
            font-weight: normal;
            color: #888;
        }

        .fianza-info {
            font-size: 13px;
            color: #888;
            margin-top: 4px;
        }

        .separador { border: none; border-top: 1px solid rgba(0,0,0,0.09); margin: 18px 0; }

        /* vendedor */
        .vendedor-bloque {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .avatar-vendedor {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #97B770;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
            font-weight: bold;
            flex-shrink: 0;
        }
        .vendedor-nombre { font-weight: bold; color: #333; font-size: 15px; }
        .vendedor-sub { font-size: 12px; color: #999; }

        /* ── CALCULADORA ALQUILER ── */
        .card-alquiler { background: rgba(245,241,230,0.92); border-radius: 20px; border: 1px solid rgba(0,0,0,0.1); box-shadow: 0 10px 30px rgba(0,0,0,0.12); padding: 24px; }

        .titulo-bloque { font-size: 17px; color: #97B770; font-weight: bold; margin-bottom: 16px; }

        .selector-dias {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 18px;
        }
        .btn-dia {
            width: 38px; height: 38px;
            border-radius: 50%;
            border: 2px solid #97B770;
            background: white;
            color: #97B770;
            font-size: 20px;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.2s;
            line-height: 1;
        }
        .btn-dia:hover { background: #97B770; color: white; }

        .contador-dias {
            font-size: 28px;
            font-weight: bold;
            color: #333;
            min-width: 60px;
            text-align: center;
        }
        .label-dias { font-size: 13px; color: #888; text-align: center; }

        .fecha-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 18px; }
        .fecha-grupo label { display: block; font-size: 12px; color: #888; margin-bottom: 5px; font-weight: 600; letter-spacing: 0.04em; }
        .fecha-grupo input[type="date"] {
            width: 100%;
            padding: 9px 12px;
            border-radius: 10px;
            border: 1px solid rgba(0,0,0,0.15);
            background: white;
            color: #333;
            font-size: 14px;
            outline: none;
        }
        .fecha-grupo input[type="date"]:focus { border-color: #97B770; }

        .resumen-precio {
            background: white;
            border-radius: 12px;
            padding: 14px 16px;
            margin-bottom: 18px;
        }
        .resumen-fila {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            color: #555;
            margin-bottom: 7px;
        }
        .resumen-fila.total {
            border-top: 1px solid rgba(0,0,0,0.1);
            padding-top: 10px;
            margin-top: 5px;
            font-weight: bold;
            font-size: 16px;
            color: #333;
        }
        .resumen-fila.total span:last-child { color: #97B770; font-size: 18px; }

        .btn-reservar {
            width: 100%;
            padding: 14px;
            background-color: #97B770;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn-reservar:hover { background-color: #7fa45a; }
        .btn-reservar:disabled { background-color: #ccc; cursor: not-allowed; }

        .btn-favorito-detalle {
            width: 100%;
            padding: 11px;
            background: white;
            color: #97B770;
            border: 2px solid #97B770;
            border-radius: 10px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 10px;
        }
        .btn-favorito-detalle:hover, .btn-favorito-detalle.activo { background: #97B770; color: white; }

        /* ── FOOTER ── */
        .footer-container {
            background: rgba(245,241,230,0.9);
            border-radius: 20px;
            border: 1px solid rgba(0,0,0,0.1);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            margin: 20px 10px 10px 10px;
            padding: 18px 25px;
        }
        .footer-contenido { display: flex; justify-content: space-between; align-items: center; gap: 20px; flex-wrap: wrap; }
        .footer-logo { font-size: 22px; font-weight: bold; color: #97B770; }
        .footer-texto { font-size: 14px; color: #666; }
        .footer-links { display: flex; gap: 15px; flex-wrap: wrap; }
        .footer-links a { text-decoration: none; color: #666; font-size: 14px; transition: 0.2s; }
        .footer-links a:hover { color: #97B770; }

        /* aviso login */
        .aviso-login {
            background: rgba(151,183,112,0.1);
            border: 1px solid rgba(151,183,112,0.3);
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 14px;
            color: #666;
            text-align: center;
        }
        .aviso-login a { color: #97B770; font-weight: bold; }

        @media (max-width: 860px) {
            .detalle-grid { grid-template-columns: 1fr; }
            .imagen-principal { height: 300px; }
        }
        @media (max-width: 480px) {
            .fecha-grid { grid-template-columns: 1fr; }
            .titulo-producto { font-size: 22px; }
        }
    </style>
</head>
<body>

<?php include __DIR__ . '/nav_basico.php'; ?>

<div class="pagina-detalle">

    <a href="index.php" class="btn-volver">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="15 18 9 12 15 6"/>
        </svg>
        Volver al inicio
    </a>

    <div class="detalle-grid">

        <!-- ══ COLUMNA IZQUIERDA: galería ══ -->
        <div class="galeria-bloque">
            <div class="imagen-principal" id="imgPrincipalWrapper">
                <?php if (count($imagenes) > 1): ?>
                    <button class="flecha-galeria flecha-izq oculta" id="flechaIzq" onclick="cambiarImagen(-1)">&#8249;</button>
                    <button class="flecha-galeria flecha-der" id="flechaDer" onclick="cambiarImagen(1)">&#8250;</button>
                <?php endif; ?>
                <img id="imgPrincipal"
                     src="<?= htmlspecialchars($imagenes[0]) ?>"
                     alt="<?= htmlspecialchars($producto["titulo"]) ?>">
            </div>

            <?php if (count($imagenes) > 1): ?>
            <div class="miniaturas" id="miniaturas">
                <?php foreach ($imagenes as $i => $img): ?>
                    <div class="miniatura <?= $i === 0 ? 'activa' : '' ?>"
                         onclick="seleccionarImagen(<?= $i ?>)">
                        <img src="<?= htmlspecialchars($img) ?>" alt="Foto <?= $i+1 ?>">
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <div class="info-extra">
                <?php if (!empty($producto["ciudad"])): ?>
                <span class="tag-info">
                    📍 <strong><?= htmlspecialchars($producto["ciudad"]) ?></strong>
                </span>
                <?php endif; ?>
                <span class="tag-info">
                    🗓️ Publicado el <strong><?= date("d/m/Y", strtotime($producto["fecha_creacion"])) ?></strong>
                </span>
                <?php if (!empty($producto["nombre_categoria"])): ?>
                <span class="tag-info">
                    🏷️ <strong><?= htmlspecialchars($producto["nombre_categoria"]) ?></strong>
                </span>
                <?php endif; ?>
            </div>
        </div>

        <!-- ══ COLUMNA DERECHA ══ -->
        <div class="panel-derecho">

            <!-- info del producto -->
            <div class="card-info">
                <?php if (!empty($producto["nombre_categoria"])): ?>
                    <span class="categoria-badge"><?= htmlspecialchars($producto["nombre_categoria"]) ?></span>
                <?php endif; ?>

                <h1 class="titulo-producto"><?= htmlspecialchars($producto["titulo"]) ?></h1>
                <p class="descripcion-producto"><?= nl2br(htmlspecialchars($producto["descripcion"])) ?></p>

                <div class="precio-principal">
                    <?= number_format((float)$producto["precio_dia"], 2, ",", ".") ?> €
                    <span>/ día</span>
                </div>
                <?php if (!empty($producto["fianza"]) && $producto["fianza"] > 0): ?>
                    <div class="fianza-info">🛡️ Fianza: <?= number_format((float)$producto["fianza"], 2, ",", ".") ?> €</div>
                <?php endif; ?>

                <hr class="separador">

                <!-- vendedor -->
                <div class="vendedor-bloque">
                    <div class="avatar-vendedor">
                        <?= strtoupper(substr($producto["nombre_vendedor"] ?? "?", 0, 1)) ?>
                    </div>
                    <div>
                        <div class="vendedor-nombre"><?= htmlspecialchars($producto["nombre_vendedor"] ?? "Usuario") ?></div>
                        <div class="vendedor-sub">Anunciante en Flooty</div>
                    </div>
                </div>
            </div>

            <!-- calculadora alquiler -->
            <div class="card-alquiler">
                <div class="titulo-bloque">📅 Calcular precio de alquiler</div>

                <!-- selector días rápido -->
                <div class="selector-dias">
                    <button class="btn-dia" onclick="ajustarDias(-1)">−</button>
                    <div>
                        <div class="contador-dias" id="contadorDias">1</div>
                        <div class="label-dias">días</div>
                    </div>
                    <button class="btn-dia" onclick="ajustarDias(1)">+</button>
                </div>

                <!-- fechas -->
                <div class="fecha-grid">
                    <div class="fecha-grupo">
                        <label>Fecha inicio</label>
                        <input type="date" id="fechaInicio" onchange="calcularDesdefechas()">
                    </div>
                    <div class="fecha-grupo">
                        <label>Fecha fin</label>
                        <input type="date" id="fechaFin" onchange="calcularDesdefechas()">
                    </div>
                </div>

                <!-- resumen precio -->
                <div class="resumen-precio">
                    <div class="resumen-fila">
                        <span id="resumenDiasLabel"><?= number_format((float)$producto["precio_dia"], 2, ",", ".") ?> € × 1 día</span>
                        <span id="resumenSubtotal"><?= number_format((float)$producto["precio_dia"], 2, ",", ".") ?> €</span>
                    </div>
                    <?php if (!empty($producto["fianza"]) && $producto["fianza"] > 0): ?>
                    <div class="resumen-fila">
                        <span>Fianza (reembolsable)</span>
                        <span><?= number_format((float)$producto["fianza"], 2, ",", ".") ?> €</span>
                    </div>
                    <?php endif; ?>
                    <div class="resumen-fila total">
                        <span>Total estimado</span>
                        <span id="resumenTotal">
                            <?= number_format((float)$producto["precio_dia"] + (float)($producto["fianza"] ?? 0), 2, ",", ".") ?> €
                        </span>
                    </div>
                </div>

                <?php if ($esta_logueado): ?>
                    <div class="fecha-grupo" style="margin-bottom:14px;">
                        <label style="display:block;font-size:12px;color:#888;margin-bottom:5px;font-weight:600;letter-spacing:0.04em;">
                            Mensaje al propietario (opcional)
                        </label>
                        <textarea id="mensajeReserva" rows="3"
                            placeholder="Ej: ¿Está disponible para recogerlo el sábado?"
                            style="width:100%;padding:9px 12px;border-radius:10px;border:1px solid rgba(0,0,0,0.15);background:white;color:#333;font-size:14px;outline:none;resize:vertical;font-family:Arial,sans-serif;"
                            onfocus="this.style.borderColor='#97B770'" onblur="this.style.borderColor='rgba(0,0,0,0.15)'">
                        </textarea>
                    </div>

                    <button class="btn-reservar" id="btnReservar" onclick="solicitarReserva()">
                        Solicitar reserva
                    </button>

                    <!-- Feedback de la reserva -->
                    <div id="feedbackReserva" style="display:none;margin-top:12px;padding:12px 16px;border-radius:10px;font-size:14px;text-align:center;"></div>

                    <a href="producto.php?id=<?= $id_producto ?>&favorito=1"
                       class="btn-favorito-detalle <?= $es_favorito ? 'activo' : '' ?>">
                        <?= $es_favorito ? '♥ Quitar de favoritos' : '♡ Añadir a favoritos' ?>
                    </a>
                <?php else: ?>
                    <div class="aviso-login">
                        <a href="sesion/login.php">Inicia sesión</a> para reservar o guardar en favoritos
                    </div>
                <?php endif; ?>
            </div>

        </div><!-- /panel-derecho -->
    </div><!-- /detalle-grid -->
</div><!-- /pagina-detalle -->

<footer class="footer-container">
    <div class="footer-contenido">
        <div class="footer-logo">FLOOTY</div>
        <div class="footer-texto">© 2026 Flooty. Plataforma de alquiler entre personas.</div>
        <div class="footer-links">
            <a href="#">Aviso legal</a>
            <a href="#">Privacidad</a>
            <a href="#">Contacto</a>
            <a href="#">Ayuda</a>
        </div>
    </div>
</footer>

<script>
    /* ── Galería ── */
    const imagenes = <?= json_encode($imagenes) ?>;
    let idxActual = 0;

    function seleccionarImagen(idx) {
        idxActual = idx;
        document.getElementById("imgPrincipal").src = imagenes[idx];
        document.querySelectorAll(".miniatura").forEach((m, i) => {
            m.classList.toggle("activa", i === idx);
        });
        actualizarFlechas();
    }

    function cambiarImagen(dir) {
        let nuevo = idxActual + dir;
        if (nuevo < 0) nuevo = 0;
        if (nuevo >= imagenes.length) nuevo = imagenes.length - 1;
        seleccionarImagen(nuevo);
    }

    function actualizarFlechas() {
        const izq = document.getElementById("flechaIzq");
        const der = document.getElementById("flechaDer");
        if (!izq) return;
        izq.classList.toggle("oculta", idxActual === 0);
        der.classList.toggle("oculta", idxActual === imagenes.length - 1);
    }

    /* ── Calculadora ── */
    const precioDia  = <?= (float)$producto["precio_dia"] ?>;
    const fianza     = <?= (float)($producto["fianza"] ?? 0) ?>;
    let diasSeleccionados = 1;

    function formatearEuros(val) {
        return val.toLocaleString("es-ES", { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + " €";
    }

    function actualizarResumen() {
        const subtotal = precioDia * diasSeleccionados;
        const total    = subtotal + fianza;
        document.getElementById("contadorDias").textContent = diasSeleccionados;
        document.getElementById("resumenDiasLabel").textContent =
            formatearEuros(precioDia) + " × " + diasSeleccionados + (diasSeleccionados === 1 ? " día" : " días");
        document.getElementById("resumenSubtotal").textContent = formatearEuros(subtotal);
        document.getElementById("resumenTotal").textContent = formatearEuros(total);
    }

    function ajustarDias(delta) {
        diasSeleccionados = Math.max(1, diasSeleccionados + delta);
        // Sincronizar fechaFin con diasSeleccionados si fechaInicio está rellena
        const fi = document.getElementById("fechaInicio");
        if (fi.value) {
            const inicio = new Date(fi.value);
            inicio.setDate(inicio.getDate() + diasSeleccionados - 1);
            document.getElementById("fechaFin").value = inicio.toISOString().split("T")[0];
        }
        actualizarResumen();
    }

    function calcularDesdefechas() {
        const fi = document.getElementById("fechaInicio").value;
        const ff = document.getElementById("fechaFin").value;
        if (fi && ff) {
            const inicio = new Date(fi);
            const fin    = new Date(ff);
            const diff   = Math.round((fin - inicio) / (1000 * 60 * 60 * 24)) + 1;
            if (diff >= 1) {
                diasSeleccionados = diff;
                actualizarResumen();
            }
        }
    }

    // Inicializar fecha mínima a hoy
    const hoy = new Date().toISOString().split("T")[0];
    document.getElementById("fechaInicio").min = hoy;
    document.getElementById("fechaFin").min = hoy;
    document.getElementById("fechaInicio").value = hoy;
    const manana = new Date();
    manana.setDate(manana.getDate());
    document.getElementById("fechaFin").value = hoy;

    function mostrarFeedback(ok, texto) {
        const el = document.getElementById("feedbackReserva");
        el.style.display = "block";
        el.style.background   = ok ? "rgba(151,183,112,0.15)" : "rgba(220,80,80,0.1)";
        el.style.border       = ok ? "1px solid rgba(151,183,112,0.4)" : "1px solid rgba(220,80,80,0.3)";
        el.style.color        = ok ? "#4a7a2a" : "#b03030";
        el.innerHTML          = texto;
        el.scrollIntoView({ behavior: "smooth", block: "nearest" });
    }

    async function solicitarReserva() {
        const fi      = document.getElementById("fechaInicio").value;
        const ff      = document.getElementById("fechaFin").value;
        const mensaje = document.getElementById("mensajeReserva")
                        ? document.getElementById("mensajeReserva").value : "";

        if (!fi || !ff) {
            mostrarFeedback(false, "⚠️ Por favor selecciona las fechas de inicio y fin.");
            return;
        }
        if (new Date(ff) < new Date(fi)) {
            mostrarFeedback(false, "⚠️ La fecha de fin no puede ser anterior a la de inicio.");
            return;
        }

        const btn = document.getElementById("btnReservar");
        btn.disabled     = true;
        btn.textContent  = "Enviando…";

        const datos = new FormData();
        datos.append("id_producto",  "<?= $id_producto ?>");
        datos.append("fecha_inicio", fi);
        datos.append("fecha_fin",    ff);
        datos.append("mensaje",      mensaje);

        try {
            const resp = await fetch("crear_reserva.php", { method: "POST", body: datos });
            
            let texto = await resp.text();
            let json;
            try {
                json = JSON.parse(texto);
            } catch(e) {
                btn.disabled = false;
                btn.textContent = "Solicitar reserva";
                mostrarFeedback(false, "❌ Error del servidor: " + texto.substring(0, 100));
                console.error("Respuesta no válida: ", texto);
                return;
            }

            if (json.ok) {
                btn.textContent = "Ir al pago";
                mostrarFeedback(true,
                    "✅ <strong>Reserva creada.</strong><br>" +
                    "Ahora te redirigimos a la pasarela de pago…"
                );

                if (json.id_reserva) {
                    setTimeout(() => {
                        window.location.href = "pago.php?id_reserva=" + encodeURIComponent(json.id_reserva);
                    }, 700);
                } else {
                    btn.disabled = false;
                    btn.textContent = "Solicitar reserva";
                    mostrarFeedback(false, "❌ No se pudo iniciar el pago (id de reserva no recibido).");
                }
            } else {
                btn.disabled    = false;
                btn.textContent = "Solicitar reserva";
                mostrarFeedback(false, "❌ " + json.error);
            }
        } catch (e) {
            btn.disabled    = false;
            btn.textContent = "Solicitar reserva";
            mostrarFeedback(false, "❌ Error de conexión: " + e.message);
        }
    }

    actualizarResumen();
</script>
</body>
</html>
