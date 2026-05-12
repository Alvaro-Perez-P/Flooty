<!-- CHATBOT FLOOTY ANIMADO -->

<style>

.chatbot-boton{
    position:fixed;
    right:22px;
    bottom:22px;
    width:82px;
    height:82px;
    background:transparent;
    border:none;
    cursor:pointer;
    z-index:9999;
    display:flex;
    align-items:center;
    justify-content:center;
    overflow:visible;
}

/* LOGO */

.flooty-mini,
.flooty-header{
    position:relative;
    width:74px;
    height:54px;
    animation:flootyFlotar 2.5s ease-in-out infinite;
}

.flooty-mini img,
.flooty-header img{
    width:100%;
    height:100%;
    object-fit:contain;
    display:block;
}

/* OJOS */

.ojo{
    position:absolute;
    width:9px;
    height:9px;
    background:white;
    border:1px solid #222;
    border-radius:50%;
    overflow:hidden;
    z-index:3;
    animation:parpadear 4s infinite;
}

/* OJO IZQUIERDO */

.ojo.izq{
    left:24px;
    top:22px;
}

/* OJO DERECHO */

.ojo.der{
    left:38px;
    top:28px;
}

/* PUPILAS */

.pupila{
    position:absolute;
    width:4px;
    height:4px;
    background:#111;
    border-radius:50%;
    left:2px;
    top:2px;
    transition:transform 0.08s linear;
}

/* BOCA */

.sonrisa-mini{
    position:absolute;
    left:29px;
    top:36px;
    width:10px;
    height:5px;
    border-bottom:2px solid #333;
    border-radius:0 0 10px 10px;
    z-index:3;
    transform:rotate(6deg);
}

/* ANIMACIONES */

@keyframes flootyFlotar{
    0%,100%{
        transform:translateY(0);
    }

    50%{
        transform:translateY(-5px);
    }
}

@keyframes parpadear{
    0%,92%,100%{
        transform:scaleY(1);
    }

    95%{
        transform:scaleY(0.12);
    }
}

/* CHAT */

.chatbot-contenedor{
    position:fixed;
    right:22px;
    bottom:105px;
    width:350px;
    max-width:calc(100vw - 40px);
    height:470px;
    background:white;
    border-radius:18px;
    box-shadow:0 8px 30px rgba(0,0,0,0.25);
    overflow:hidden;
    display:none;
    flex-direction:column;
    z-index:9999;
    font-family:Arial, Helvetica, sans-serif;
}

.chatbot-header{
    background:#97B770;
    color:white;
    padding:14px;
    font-weight:bold;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.chatbot-header-info{
    display:flex;
    align-items:center;
    gap:10px;
}

.chatbot-cerrar{
    background:transparent;
    border:none;
    color:white;
    font-size:24px;
    cursor:pointer;
}

.chatbot-body{
    flex:1;
    padding:14px;
    overflow-y:auto;
    background:#f5f1e6;
}

.chatbot-msg{
    margin-bottom:10px;
    padding:10px 12px;
    border-radius:12px;
    font-size:14px;
    line-height:1.4;
    max-width:85%;
}

.chatbot-user{
    background:#97B770;
    color:white;
    margin-left:auto;
    border-bottom-right-radius:4px;
}

.chatbot-bot{
    background:white;
    color:#333;
    margin-right:auto;
    border-bottom-left-radius:4px;
}

/* OPCIONES */

.chatbot-opciones{
    display:flex;
    flex-wrap:wrap;
    gap:6px;
    margin-top:8px;
}

.chatbot-opcion{
    background:white;
    border:1px solid #97B770;
    color:#6f8f4e;
    border-radius:20px;
    padding:6px 9px;
    font-size:12px;
    cursor:pointer;
}

.chatbot-opcion:hover{
    background:#97B770;
    color:white;
}

/* INPUT */

.chatbot-input-area{
    display:flex;
    border-top:1px solid #ddd;
    background:white;
}

.chatbot-input{
    flex:1;
    border:none;
    padding:13px;
    outline:none;
    font-size:14px;
}

.chatbot-enviar{
    border:none;
    background:#97B770;
    color:white;
    padding:0 15px;
    font-weight:bold;
    cursor:pointer;
}

</style>

<!-- BOTON -->

<button class="chatbot-boton" onclick="abrirChatbot()" title="Ayuda Flooty">

    <div class="flooty-mini mascota-flooty">

        <img src="imagenes/logo_chatbot.png" alt="Flooty">

        <span class="ojo izq">
            <span class="pupila"></span>
        </span>

        <span class="ojo der">
            <span class="pupila"></span>
        </span>

        <span class="sonrisa-mini"></span>

    </div>

</button>

<!-- CHAT -->

<div class="chatbot-contenedor" id="chatbotFlooty">

    <div class="chatbot-header">

        <div class="chatbot-header-info">

            <div class="flooty-header mascota-flooty">

                <img src="imagenes/logo_chatbot.png" alt="Flooty">

                <span class="ojo izq">
                    <span class="pupila"></span>
                </span>

                <span class="ojo der">
                    <span class="pupila"></span>
                </span>

                <span class="sonrisa-mini"></span>

            </div>

            <span>Ayuda Flooty</span>

        </div>

        <button class="chatbot-cerrar" onclick="cerrarChatbot()">×</button>

    </div>

    <div class="chatbot-body" id="chatbotBody">

        <div class="chatbot-msg chatbot-bot">
            ¡Hola! 👋 Soy Flooty, tu asistente. ¿En qué puedo ayudarte?
        </div>

        <div class="chatbot-opciones">

            <button class="chatbot-opcion"
                onclick="preguntaRapida('¿Cómo reservo un producto?')">
                Reservar
            </button>

            <button class="chatbot-opcion"
                onclick="preguntaRapida('¿Cómo publico un anuncio?')">
                Publicar
            </button>

            <button class="chatbot-opcion"
                onclick="preguntaRapida('¿Dónde veo mis reservas?')">
                Mis reservas
            </button>

            <button class="chatbot-opcion"
                onclick="preguntaRapida('¿Cómo edito mi perfil?')">
                Perfil
            </button>

        </div>

    </div>

    <div class="chatbot-input-area">

        <input
            type="text"
            id="chatbotInput"
            class="chatbot-input"
            placeholder="Escribe tu pregunta..."
        >

        <button class="chatbot-enviar"
            onclick="enviarChatbot()">
            Enviar
        </button>

    </div>

</div>

<script>

function abrirChatbot(){
    document.getElementById("chatbotFlooty").style.display = "flex";
}

function cerrarChatbot(){
    document.getElementById("chatbotFlooty").style.display = "none";
}

function agregarMensaje(texto, tipo){

    const body = document.getElementById("chatbotBody");

    const div = document.createElement("div");

    div.className =
        "chatbot-msg " +
        (tipo === "user"
            ? "chatbot-user"
            : "chatbot-bot");

    div.innerHTML = texto;

    body.appendChild(div);

    body.scrollTop = body.scrollHeight;
}

function responderBot(pregunta){

    const texto = pregunta.toLowerCase();

    if(texto.includes("reserv")){
        return "Para reservar un producto entra en el producto y selecciona las fechas.";
    }

    if(texto.includes("public")){
        return "Para publicar un anuncio inicia sesión y pulsa publicar producto.";
    }

    if(texto.includes("perfil")){
        return "Puedes editar tu perfil desde Mis datos.";
    }

    if(texto.includes("favorito")){
        return "Pulsa el corazón del producto para guardarlo.";
    }

    if(texto.includes("hola")){
        return "Hola! Como estas? En que puedo ayudarte?"
}

if(texto.includes("camara")){
    return "Puedes alquilar cámaras y accesorios.";
}

if(texto.includes("consola")){
    return "También puedes alquilar consolas y videojuegos.";
}

if(texto.includes("ropa")){
    return "Flooty permite alquilar muchos tipos de productos.";
}

if(texto.includes("hogar")){
    return "Hay productos para el hogar y decoración.";
}

if(texto.includes("deporte")){
    return "Puedes encontrar productos deportivos.";
}

if(texto.includes("musica")){
    return "También podrían alquilarse instrumentos musicales.";
}

if(texto.includes("instrumentos")){
    return "Puedes alquilar instrumentos musicales.";
}

if(texto.includes("fotos")){
    return "Las fotos ayudan a mejorar tu anuncio.";
}

if(texto.includes("contacto")){
    return "Puedes contactar mediante la plataforma.";
}

if(texto.includes("soporte")){
    return "El soporte de Flooty te ayudará con problemas.";
}

if(texto.includes("privacidad")){
    return "Flooty protege los datos de los usuarios.";
}

if(texto.includes("legal")){
    return "Consulta el aviso legal y privacidad.";
}

    return "No entendí la pregunta 😅";
}

function enviarChatbot(){

    const input =
        document.getElementById("chatbotInput");

    const pregunta =
        input.value.trim();

    if(pregunta === ""){
        return;
    }

    agregarMensaje(pregunta, "user");

    const respuesta =
        responderBot(pregunta);

    setTimeout(function(){

        agregarMensaje(respuesta, "bot");

    },300);

    input.value = "";
}

function preguntaRapida(texto){

    document.getElementById("chatbotInput").value = texto;

    enviarChatbot();
}

document.getElementById("chatbotInput")
.addEventListener("keypress", function(e){

    if(e.key === "Enter"){
        enviarChatbot();
    }
});

/* OJOS SIGUEN EL MOUSE */

document.addEventListener("mousemove", function(e){

    const mascotas =
        document.querySelectorAll(".mascota-flooty");

    mascotas.forEach(function(mascota){

        const rect =
            mascota.getBoundingClientRect();

        const centroX =
            rect.left + rect.width / 2;

        const centroY =
            rect.top + rect.height / 2;

        const angulo =
            Math.atan2(
                e.clientY - centroY,
                e.clientX - centroX
            );

        const moverX =
            Math.cos(angulo) * 2;

        const moverY =
            Math.sin(angulo) * 2;

        mascota.querySelectorAll(".pupila")
        .forEach(function(pupila){

            pupila.style.transform =
                "translate(" +
                moverX +
                "px," +
                moverY +
                "px)";
        });
    });
});

</script>

<!-- FIN CHATBOT -->