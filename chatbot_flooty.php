<!-- CHATBOT FLOOTY -->
<style>
    .chatbot-boton {
        position: fixed;
        right: 22px;
        bottom: 22px;
        width: 62px;
        height: 62px;
        border-radius: 50%;
        background: #97B770;
        color: white;
        border: none;
        font-size: 28px;
        cursor: pointer;
        box-shadow: 0 6px 18px rgba(0,0,0,0.25);
        z-index: 9999;
    }

    .chatbot-contenedor {
        position: fixed;
        right: 22px;
        bottom: 95px;
        width: 330px;
        max-width: calc(100vw - 40px);
        height: 440px;
        background: white;
        border-radius: 18px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.25);
        overflow: hidden;
        display: none;
        flex-direction: column;
        z-index: 9999;
        font-family: Arial, Helvetica, sans-serif;
    }

    .chatbot-header {
        background: #97B770;
        color: white;
        padding: 15px;
        font-weight: bold;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .chatbot-cerrar {
        background: transparent;
        border: none;
        color: white;
        font-size: 20px;
        cursor: pointer;
    }

    .chatbot-body {
        flex: 1;
        padding: 14px;
        overflow-y: auto;
        background: #f5f1e6;
    }

    .chatbot-msg {
        margin-bottom: 10px;
        padding: 10px 12px;
        border-radius: 12px;
        font-size: 14px;
        line-height: 1.4;
        max-width: 85%;
    }

    .chatbot-user {
        background: #97B770;
        color: white;
        margin-left: auto;
        border-bottom-right-radius: 4px;
    }

    .chatbot-bot {
        background: white;
        color: #333;
        margin-right: auto;
        border-bottom-left-radius: 4px;
    }

    .chatbot-input-area {
        display: flex;
        border-top: 1px solid #ddd;
        background: white;
    }

    .chatbot-input {
        flex: 1;
        border: none;
        padding: 13px;
        outline: none;
        font-size: 14px;
    }

    .chatbot-enviar {
        border: none;
        background: #97B770;
        color: white;
        padding: 0 15px;
        font-weight: bold;
        cursor: pointer;
    }

    .chatbot-opciones {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 8px;
    }

    .chatbot-opcion {
        background: white;
        border: 1px solid #97B770;
        color: #6f8f4e;
        border-radius: 20px;
        padding: 6px 9px;
        font-size: 12px;
        cursor: pointer;
    }
</style>

<button class="chatbot-boton" onclick="abrirChatbot()">💬</button>

<div class="chatbot-contenedor" id="chatbotFlooty">
    <div class="chatbot-header">
        <span>Ayuda Flooty</span>
        <button class="chatbot-cerrar" onclick="cerrarChatbot()">×</button>
    </div>

    <div class="chatbot-body" id="chatbotBody">
        <div class="chatbot-msg chatbot-bot">
            Hola 👋 Soy el asistente de Flooty. ¿En qué te puedo ayudar?
        </div>

        <div class="chatbot-opciones">
            <button class="chatbot-opcion" onclick="preguntaRapida('¿Cómo reservo un producto?')">Reservar</button>
            <button class="chatbot-opcion" onclick="preguntaRapida('¿Cómo publico un anuncio?')">Publicar</button>
            <button class="chatbot-opcion" onclick="preguntaRapida('¿Dónde veo mis reservas?')">Mis reservas</button>
            <button class="chatbot-opcion" onclick="preguntaRapida('¿Cómo edito mi perfil?')">Perfil</button>
        </div>
    </div>

    <div class="chatbot-input-area">
        <input type="text" id="chatbotInput" class="chatbot-input" placeholder="Escribe tu pregunta...">
        <button class="chatbot-enviar" onclick="enviarChatbot()">Enviar</button>
    </div>
</div>

<script>
    function abrirChatbot() {
        document.getElementById("chatbotFlooty").style.display = "flex";
    }

    function cerrarChatbot() {
        document.getElementById("chatbotFlooty").style.display = "none";
    }

    function agregarMensaje(texto, tipo) {
        const body = document.getElementById("chatbotBody");
        const div = document.createElement("div");

        div.className = "chatbot-msg " + (tipo === "user" ? "chatbot-user" : "chatbot-bot");
        div.innerHTML = texto;

        body.appendChild(div);
        body.scrollTop = body.scrollHeight;
    }

    function responderBot(pregunta) {
        const texto = pregunta.toLowerCase();

        if (texto.includes("reserv") || texto.includes("alquil")) {
            return "Para reservar un producto, entra en el producto, selecciona fecha de inicio y fin, escribe un mensaje si quieres y pulsa reservar. La reserva quedará pendiente hasta que el propietario la acepte.";
        }

        if (texto.includes("public") || texto.includes("anuncio") || texto.includes("producto")) {
            return "Para publicar un anuncio, inicia sesión y entra en la opción de publicar producto. Completa título, descripción, categoría, precio, fianza, ciudad e imágenes.";
        }

        if (texto.includes("mis reservas") || texto.includes("reserva")) {
            return "Puedes ver tus reservas desde el menú lateral en <strong>Mis reservas</strong>. Allí verás el estado: pendiente, aceptada, rechazada, cancelada o finalizada.";
        }

        if (texto.includes("recibidas") || texto.includes("aceptar") || texto.includes("rechazar")) {
            return "Las reservas que recibes por tus productos aparecen en <strong>Reservas recibidas</strong>. Desde ahí puedes aceptar, rechazar o finalizar una reserva.";
        }

        if (texto.includes("perfil") || texto.includes("datos") || texto.includes("avatar") || texto.includes("foto")) {
            return "Puedes editar tu perfil desde <strong>Mis datos</strong>. Ahí puedes cambiar tu nombre, teléfono, dirección, email, contraseña y foto de avatar.";
        }

        if (texto.includes("favorito") || texto.includes("favoritos")) {
            return "Para guardar un producto como favorito, pulsa el corazón del producto. Luego puedes verlos desde la sección <strong>Favoritos</strong>.";
        }

        if (texto.includes("pausar") || texto.includes("activar")) {
            return "Desde <strong>Mis anuncios</strong> puedes pausar o activar tus productos. Si un producto está pausado, no aparece en el inicio.";
        }

        if (texto.includes("hola") || texto.includes("buenas")) {
            return "¡Hola! 😊 Puedes preguntarme sobre reservas, anuncios, favoritos, perfil o productos.";
        }

        return "No entendí bien la pregunta. Puedes preguntarme por ejemplo: cómo reservar, cómo publicar un anuncio, cómo ver mis reservas o cómo editar tu perfil.";
    }

    function enviarChatbot() {
        const input = document.getElementById("chatbotInput");
        const pregunta = input.value.trim();

        if (pregunta === "") return;

        agregarMensaje(pregunta, "user");

        const respuesta = responderBot(pregunta);

        setTimeout(() => {
            agregarMensaje(respuesta, "bot");
        }, 300);

        input.value = "";
    }

    function preguntaRapida(texto) {
        document.getElementById("chatbotInput").value = texto;
        enviarChatbot();
    }

    document.getElementById("chatbotInput").addEventListener("keypress", function(e) {
        if (e.key === "Enter") {
            enviarChatbot();
        }
    });
</script>
<!-- FIN CHATBOT FLOOTY -->
